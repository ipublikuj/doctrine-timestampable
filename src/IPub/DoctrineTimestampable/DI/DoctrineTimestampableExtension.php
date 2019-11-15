<?php
/**
 * DoctrineTimestampableExtension.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           06.01.16
 */

declare(strict_types = 1);

namespace IPub\DoctrineTimestampable\DI;

use Doctrine;

use Nette;
use Nette\DI;
use Nette\PhpGenerator as Code;
use Nette\Utils;

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
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
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

	/**
	 * @return void
	 *
	 * @throws Utils\AssertionException
	 */
	public function loadConfiguration() : void
	{
		// Get container builder
		$builder = $this->getContainerBuilder();
		/** @var array $configuration */
		if (method_exists($this, 'validateConfig')) {
			$configuration = $this->validateConfig($this->defaults);
		} else {
			$configuration = $this->getConfig($this->defaults);
		}

		Utils\Validators::assert($configuration['lazyAssociation'], 'bool', 'lazyAssociation');
		Utils\Validators::assert($configuration['autoMapField'], 'bool', 'autoMapField');
		Utils\Validators::assert($configuration['dbFieldType'], 'string', 'dbFieldType');

		$builder->addDefinition($this->prefix('configuration'))
			->setType(DoctrineTimestampable\Configuration::class)
			->setArguments([
				$configuration['lazyAssociation'],
				$configuration['autoMapField'],
				$configuration['dbFieldType'],
			]);

		$builder->addDefinition($this->prefix('driver'))
			->setType(Mapping\Driver\Timestampable::class);

		$builder->addDefinition($this->prefix('subscriber'))
			->setType(Events\TimestampableSubscriber::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile() : void
	{
		parent::beforeCompile();

		$builder = $this->getContainerBuilder();

		$builder->getDefinition($builder->getByType(Doctrine\ORM\EntityManagerInterface::class, TRUE))
			->addSetup('?->getEventManager()->addEventSubscriber(?)', ['@self', $builder->getDefinition($this->prefix('subscriber'))]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function afterCompile(Code\ClassType $class) : void
	{
		parent::afterCompile($class);

		/** @var Code\Method $initialize */
		$initialize = $class->methods['initialize'];
		$initialize->addBody('Doctrine\DBAL\Types\Type::addType(\'' . Types\UTCDateTime::UTC_DATETIME . '\', \'' . Types\UTCDateTime::class . '\');');
	}

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 */
	public static function register(Nette\Configurator $config, string $extensionName = 'doctrineTimestampable') : void
	{
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new DoctrineTimestampableExtension);
		};
	}
}
