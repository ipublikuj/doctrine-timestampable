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
use IPub\DoctrineTimestampable\Types;

use Kdyby;
use Kdyby\Doctrine;
use Kdyby\DoctrineCache;
use Kdyby\Events;

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
	];

	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		Utils\Validators::assert($config['lazyAssociation'], 'bool', 'lazyAssociation');
		Utils\Validators::assert($config['autoMapField'], 'bool', 'autoMapField');

		$builder->addDefinition($this->prefix('configuration'))
			->setClass('IPub\DoctrineTimestampable\Configuration')
			->setArguments([
				$config['lazyAssociation'],
				$config['autoMapField']
			]);

		$builder->addDefinition($this->prefix('driver'))
			->setClass('IPub\DoctrineTimestampable\Mapping\Driver\Timestampable');

		$builder->addDefinition($this->prefix('listener'))
			->setClass('IPub\DoctrineTimestampable\Events\TimestampableListener')
			->addTag(Events\DI\EventsExtension::TAG_SUBSCRIBER);
	}

	/**
	 * Returns array of typeName => typeClass
	 *
	 * @return array
	 */
	public function getDatabaseTypes()
	{
		return [
			Types\UTCDateTime::PHONE => 'IPub\DoctrineTimestampable\Types\UTCDateTime',
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
