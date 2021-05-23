<?php declare(strict_types = 1);

/**
 * DoctrineTimestampableExtension.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     DI
 * @since          1.0.0
 *
 * @date           06.01.16
 */

namespace IPub\DoctrineTimestampable\DI;

use Doctrine;
use IPub\DoctrineTimestampable;
use IPub\DoctrineTimestampable\Events;
use IPub\DoctrineTimestampable\Mapping;
use Nette;
use Nette\DI;
use Nette\Schema;
use stdClass;

/**
 * Doctrine timestampable extension container
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     DI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 *
 * @property-read stdClass $config
 */
final class DoctrineTimestampableExtension extends DI\CompilerExtension
{

	/**
	 * @param Nette\Configurator $config
	 * @param string $extensionName
	 */
	public static function register(
		Nette\Configurator $config,
		string $extensionName = 'doctrineTimestampable'
	): void {
		$config->onCompile[] = function (Nette\Configurator $config, Nette\DI\Compiler $compiler) use ($extensionName): void {
			$compiler->addExtension($extensionName, new DoctrineTimestampableExtension());
		};
	}

	/**
	 * {@inheritdoc}
	 */
	public function getConfigSchema(): Schema\Schema
	{
		return Schema\Expect::structure([
			'lazyAssociation' => Schema\Expect::bool(false),
			'autoMapField'    => Schema\Expect::bool(true),
			'dbFieldType'     => Schema\Expect::string('datetime'),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadConfiguration(): void
	{
		// Get container builder
		$builder = $this->getContainerBuilder();

		$configuration = $this->config;

		$builder->addDefinition($this->prefix('configuration'))
			->setType(DoctrineTimestampable\Configuration::class)
			->setArguments([
				'lazyAssociation' => $configuration->lazyAssociation,
				'autoMapField'    => $configuration->autoMapField,
				'dbFieldType'     => $configuration->dbFieldType,
			]);

		$builder->addDefinition($this->prefix('driver'))
			->setType(Mapping\Driver\Timestampable::class);

		$builder->addDefinition($this->prefix('subscriber'))
			->setType(Events\TimestampableSubscriber::class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeCompile(): void
	{
		parent::beforeCompile();

		// Get container builder
		$builder = $this->getContainerBuilder();

		$emServiceName = $builder->getByType(Doctrine\ORM\EntityManagerInterface::class, true);

		if ($emServiceName !== null) {
			/** @var DI\Definitions\ServiceDefinition $emService */
			$emService = $builder->getDefinition($emServiceName);

			$emService
				->addSetup('?->getEventManager()->addEventSubscriber(?)', [
					'@self',
					$builder->getDefinition($this->prefix('subscriber')),
				]);
		}
	}

}
