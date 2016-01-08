<?php
/**
 * DoctrineTimestampableExtension.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           06.01.16
 */

namespace IPub\DoctrineTimestampable\DI;

use Nette;
use Nette\DI;
use Nette\Utils;
use Nette\PhpGenerator as Code;

use IPub\DoctrineTimestampable;
use IPub\DoctrineTimestampable\Events;
use IPub\DoctrineTimestampable\Mapping;
use IPub\DoctrineTimestampable\Types;

/**
 * Doctrine timestampable extension container
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DoctrineTimestampableExtension extends DI\CompilerExtension
{
	/**
	 * @var array
	 */
	private $defaults = [
		'lazyAssociation' => FALSE,
		'autoMapField'    => TRUE,
		'dbFieldType'     => 'datetime',
	];

	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		Utils\Validators::assert($config['lazyAssociation'], 'bool', 'lazyAssociation');
		Utils\Validators::assert($config['autoMapField'], 'bool', 'autoMapField');
		Utils\Validators::assert($config['dbFieldType'], 'string', 'dbFieldType');

		$builder->addDefinition($this->prefix('configuration'))
			->setClass(DoctrineTimestampable\Configuration::CLASS_NAME)
			->setArguments([
				$config['lazyAssociation'],
				$config['autoMapField'],
				$config['dbFieldType'],
			]);

		$builder->addDefinition($this->prefix('driver'))
			->setClass(Mapping\Driver\Timestampable::CLASS_NAME);

		$builder->addDefinition($this->prefix('subscriber'))
			->setClass(Events\TimestampableSubscriber::CLASS_NAME);
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$builder->getDefinition($builder->getByType('Doctrine\ORM\EntityManagerInterface') ?: 'doctrine.default.entityManager')
			->addSetup('?->getEventManager()->addEventSubscriber(?)', ['@self', $builder->getDefinition($this->prefix('subscriber'))]);
	}

	/**
	 * Returns array of typeName => typeClass
	 *
	 * @return array
	 */
	public function getDatabaseTypes()
	{
		return [
			Types\UTCDateTime::UTC_DATETIME => Types\UTCDateTime::CLASS_NAME,
		];
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 */
	public static function register(Nette\Configurator $config, $extensionName = 'doctrineTimestampable')
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new DoctrineTimestampableExtension);
		};
	}
}
