<?php
/**
 * Test: IPub\DoctrineTimestampable\Extension
 * @testCase
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           06.01.16
 */

namespace IPubTests\DoctrineTimestampable;

use Nette;

use Tester;
use Tester\Assert;

use IPub;
use IPub\DoctrineTimestampable;

require __DIR__ . '/../bootstrap.php';

/**
 * Registering doctrine Timestampable extension tests
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Tests
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ExtensionTest extends Tester\TestCase
{
	public function testFunctional()
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('doctrineTimestampable.configuration') instanceof DoctrineTimestampable\Configuration);
		Assert::true($dic->getService('doctrineTimestampable.driver') instanceof DoctrineTimestampable\Mapping\Driver\Timestampable);
		Assert::true($dic->getService('doctrineTimestampable.subscriber') instanceof DoctrineTimestampable\Events\TimestampableSubscriber);
	}

	/**
	 * @return Nette\DI\Container
	 */
	protected function createContainer()
	{
		$rootDir = __DIR__ . '/../../';

		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		$config->addParameters(['container' => ['class' => 'SystemContainer_' . md5(time())]]);
		$config->addParameters(['appDir' => $rootDir, 'wwwDir' => $rootDir]);

		$config->addConfig(__DIR__ . '/files/config.neon', !isset($config->defaultExtensions['nette']) ? 'v23' : 'v22');

		DoctrineTimestampable\DI\DoctrineTimestampableExtension::register($config);

		return $config->createContainer();
	}
}

\run(new ExtensionTest());
