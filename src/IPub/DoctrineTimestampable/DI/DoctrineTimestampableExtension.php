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
use Nette\Schema;

use IPub\DoctrineTimestampable;
use IPub\DoctrineTimestampable\Events;
use IPub\DoctrineTimestampable\Mapping;

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
	 * {@inheritdoc}
	 */
	public function getConfigSchema() : Schema\Schema
	{
		return Schema\Expect::structure([
			'lazyAssociation' => Schema\Expect::bool(FALSE),
			'autoMapField'    => Schema\Expect::bool(TRUE),
			'dbFieldType'     => Schema\Expect::string('datetime'),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration() : void
	{
		$builder = $this->getContainerBuilder();
		$configuration = $this->getConfig();

		$builder->addDefinition($this->prefix('configuration'))
			->setType(DoctrineTimestampable\Configuration::class)
			->setArguments([
				$configuration->lazyAssociation,
				$configuration->autoMapField,
				$configuration->dbFieldType,
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
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 */
	public static function register(
		Nette\Configurator $config,
		string $extensionName = 'doctrineTimestampable'
	) : void {
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName) {
			$compiler->addExtension($extensionName, new DoctrineTimestampableExtension);
		};
	}
}
