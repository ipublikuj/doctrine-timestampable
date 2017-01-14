<?php
/**
 * TimestampableSubscriber.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Events
 * @since          1.0.0
 *
 * @date           06.01.16
 */

declare(strict_types = 1);

namespace IPub\DoctrineTimestampable\Events;

use Nette;
use Nette\Utils;

use Doctrine;
use Doctrine\Common;
use Doctrine\ORM;

use IPub;
use IPub\DoctrineTimestampable;
use IPub\DoctrineTimestampable\Exceptions;
use IPub\DoctrineTimestampable\Mapping;

/**
 * Doctrine timestampable subscriber
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Events
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class TimestampableSubscriber extends Nette\Object implements Common\EventSubscriber
{
	/**
	 * @var Mapping\Driver\Timestampable
	 */
	private $driver;

	/**
	 * Register events
	 *
	 * @return array
	 */
	public function getSubscribedEvents() : array
	{
		return [
			ORM\Events::loadClassMetadata,
			ORM\Events::onFlush,
		];
	}

	/**
	 * @param Mapping\Driver\Timestampable $driver
	 */
	public function __construct(
		Mapping\Driver\Timestampable $driver
	) {
		$this->driver = $driver;
	}

	/**
	 * @param ORM\Event\LoadClassMetadataEventArgs $eventArgs
	 *
	 * @return void
	 *
	 * @throws Exceptions\InvalidMappingException
	 */
	public function loadClassMetadata(ORM\Event\LoadClassMetadataEventArgs $eventArgs)
	{
		/** @var ORM\Mapping\ClassMetadata $classMetadata */
		$classMetadata = $eventArgs->getClassMetadata();
		$this->driver->loadMetadataForObjectClass($eventArgs->getObjectManager(), $classMetadata);

		// Register pre persist event
		$this->registerEvent($classMetadata, ORM\Events::prePersist);
		// Register pre update event
		$this->registerEvent($classMetadata, ORM\Events::preUpdate);
		// Register pre remove event
		$this->registerEvent($classMetadata, ORM\Events::preRemove);
	}

	/**
	 * @param ORM\Event\OnFlushEventArgs $eventArgs
	 *
	 * @return void
	 *
	 * @throws Exceptions\UnexpectedValueException
	 */
	public function onFlush(ORM\Event\OnFlushEventArgs $eventArgs)
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();

		// Check all scheduled updates
		foreach ($uow->getScheduledEntityUpdates() as $object) {
			/** @var ORM\Mapping\ClassMetadata $classMetadata */
			$classMetadata = $em->getClassMetadata(get_class($object));

			if ($config = $this->driver->getObjectConfigurations($em, $classMetadata->getName())) {
				$changeSet = $uow->getEntityChangeSet($object);
				$needChanges = FALSE;

				if ($uow->isScheduledForInsert($object) && isset($config['create'])) {
					foreach ($config['create'] as $field) {
						// Field can not exist in change set, when persisting embedded document without parent for example
						$new = array_key_exists($field, $changeSet) ? $changeSet[$field][1] : FALSE;

						if ($new === NULL) { // let manual values
							$needChanges = TRUE;
							$this->updateField($uow, $object, $classMetadata, $field);
						}
					}
				}

				if (isset($config['update'])) {
					foreach ($config['update'] as $field) {
						$isInsertAndNull = $uow->isScheduledForInsert($object)
							&& array_key_exists($field, $changeSet)
							&& $changeSet[$field][1] === NULL;

						if (!isset($changeSet[$field]) || $isInsertAndNull) { // let manual values
							$needChanges = TRUE;
							$this->updateField($uow, $object, $classMetadata, $field);
						}
					}
				}

				if (isset($config['delete'])) {
					foreach ($config['delete'] as $field) {
						$isDeleteAndNull = $uow->isScheduledForDelete($object)
							&& array_key_exists($field, $changeSet)
							&& $changeSet[$field][1] === NULL;

						if (!isset($changeSet[$field]) || $isDeleteAndNull) { // let manual values
							$needChanges = TRUE;
							$this->updateField($uow, $object, $classMetadata, $field);
						}
					}
				}

				if (isset($config['change'])) {
					foreach ($config['change'] as $options) {
						if (isset($changeSet[$options['field']])) {
							continue; // Value was set manually
						}

						if (!is_array($options['trackedField'])) {
							$singleField = TRUE;
							$trackedFields = [$options['trackedField']];

						} else {
							$singleField = FALSE;
							$trackedFields = $options['trackedField'];
						}

						foreach ($trackedFields as $tracked) {
							$trackedChild = NULL;
							$parts = explode('.', $tracked);

							if (isset($parts[1])) {
								$tracked = $parts[0];
								$trackedChild = $parts[1];
							}

							if (isset($changeSet[$tracked])) {
								$changes = $changeSet[$tracked];

								if (isset($trackedChild)) {
									$changingObject = $changes[1];

									if (!is_object($changingObject)) {
										throw new Exceptions\UnexpectedValueException("Field - [{$options['field']}] is expected to be object in class - {$classMetadata->getName()}");
									}

									/** @var ORM\Mapping\ClassMetadata $objectMeta */
									$objectMeta = $em->getClassMetadata(get_class($changingObject));
									$em->initializeObject($changingObject);
									$value = $objectMeta->getReflectionProperty($trackedChild)->getValue($changingObject);

								} else {
									$value = $changes[1];
								}

								if (($singleField && in_array($value, (array) $options['value'])) || $options['value'] === NULL) {
									$needChanges = TRUE;
									$this->updateField($uow, $object, $classMetadata, $options['field']);
								}
							}
						}
					}
				}

				if ($needChanges) {
					$uow->recomputeSingleEntityChangeSet($classMetadata, $object);
				}
			}
		}
	}

	/**
	 * @param mixed $entity
	 * @param ORM\Event\LifecycleEventArgs $eventArgs
	 *
	 * @return void
	 */
	public function prePersist($entity, ORM\Event\LifecycleEventArgs $eventArgs)
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();
		$classMetadata = $em->getClassMetadata(get_class($entity));

		if ($config = $this->driver->getObjectConfigurations($em, $classMetadata->getName())) {
			foreach(['update', 'create'] as $event) {
				if (isset($config[$event])) {
					$this->updateFields($config[$event], $uow, $entity, $classMetadata);
				}
			}
		}
	}

	/**
	 * @param mixed $entity
	 * @param ORM\Event\LifecycleEventArgs $eventArgs
	 *
	 * @return void
	 */
	public function preUpdate($entity, ORM\Event\LifecycleEventArgs $eventArgs)
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();
		$classMetadata = $em->getClassMetadata(get_class($entity));

		if ($config = $this->driver->getObjectConfigurations($em, $classMetadata->getName())) {
			if (isset($config['update'])) {
				$this->updateFields($config['update'], $uow, $entity, $classMetadata);
			}
		}
	}

	/**
	 * @param mixed $entity
	 * @param ORM\Event\LifecycleEventArgs $eventArgs
	 *
	 * @return void
	 */
	public function preRemove($entity, ORM\Event\LifecycleEventArgs $eventArgs)
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();
		$classMetadata = $em->getClassMetadata(get_class($entity));

		if ($config = $this->driver->getObjectConfigurations($em, $classMetadata->getName())) {
			if (isset($config['delete'])) {
				$this->updateFields($config['delete'], $uow, $entity, $classMetadata);
			}
		}
	}

	/**
	 * @param array $fields
	 * @param ORM\UnitOfWork $uow
	 * @param mixed $object
	 * @param ORM\Mapping\ClassMetadata $classMetadata
	 *
	 * @return void
	 */
	private function updateFields(array $fields, ORM\UnitOfWork $uow, $object, ORM\Mapping\ClassMetadata $classMetadata)
	{
		foreach ($fields as $field) {
			if ($classMetadata->getReflectionProperty($field)->getValue($object) === NULL) { // let manual values
				$this->updateField($uow, $object, $classMetadata, $field);
			}
		}
	}

	/**
	 * Updates a field
	 *
	 * @param ORM\UnitOfWork $uow
	 * @param mixed $object
	 * @param ORM\Mapping\ClassMetadata $classMetadata
	 * @param string $field
	 *
	 * @return void
	 */
	private function updateField(ORM\UnitOfWork $uow, $object, ORM\Mapping\ClassMetadata $classMetadata, string $field)
	{
		$property = $classMetadata->getReflectionProperty($field);

		$oldValue = $property->getValue($object);
		$newValue = $this->getDateValue($classMetadata, $field);

		$property->setValue($object, $newValue);

		$uow->propertyChanged($object, $field, $oldValue, $newValue);
		$uow->scheduleExtraUpdate($object, [
			$field => [$oldValue, $newValue],
		]);
	}

	/**
	 * Get the date value to set on a timestampable field
	 *
	 * @param ORM\Mapping\ClassMetadata $classMetadata
	 * @param string $field
	 *
	 * @return mixed
	 */
	private function getDateValue(ORM\Mapping\ClassMetadata $classMetadata, string $field)
	{
		$mapping = $classMetadata->getFieldMapping($field);

		if (isset($mapping['type']) && $mapping['type'] === 'integer') {
			return time();
		}

		if (isset($mapping['type']) && $mapping['type'] == 'zenddate') {
			return new \Zend_Date();
		}

		return \DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''))
			->setTimezone(new \DateTimeZone(date_default_timezone_get()));
	}

	/**
	 * @param ORM\Mapping\ClassMetadata $classMetadata
	 * @param string $eventName
	 *
	 * @return void
	 *
	 * @throws ORM\Mapping\MappingException
	 */
	private function registerEvent(ORM\Mapping\ClassMetadata $classMetadata, string $eventName)
	{
		if (!$this->hasRegisteredListener($classMetadata, $eventName, get_called_class())) {
			$classMetadata->addEntityListener($eventName, get_called_class(), $eventName);
		}
	}

	/**
	 * @param ORM\Mapping\ClassMetadata $classMetadata
	 * @param string $eventName
	 * @param string $listenerClass
	 *
	 * @return bool
	 */
	private static function hasRegisteredListener(ORM\Mapping\ClassMetadata $classMetadata, string $eventName, string $listenerClass) : bool
	{
		if (!isset($classMetadata->entityListeners[$eventName])) {
			return FALSE;
		}

		foreach ($classMetadata->entityListeners[$eventName] as $listener) {
			if ($listener['class'] === $listenerClass && $listener['method'] === $eventName) {
				return TRUE;
			}
		}

		return FALSE;
	}
}
