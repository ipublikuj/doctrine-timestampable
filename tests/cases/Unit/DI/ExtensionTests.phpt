<?php declare(strict_types = 1);

namespace Tests\Cases;

use IPub\DoctrineTimestampable;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../BaseTestCase.php';

/**
 * @testCase
 */
final class ExtensionTests extends BaseTestCase
{

	public function testFunctional(): void
	{
		$dic = $this->createContainer();

		Assert::true($dic->getService('doctrineTimestampable.configuration') instanceof DoctrineTimestampable\Configuration);
		Assert::true($dic->getService('doctrineTimestampable.driver') instanceof DoctrineTimestampable\Mapping\Driver\Timestampable);
		Assert::true($dic->getService('doctrineTimestampable.subscriber') instanceof DoctrineTimestampable\Events\TimestampableSubscriber);
	}

}

$test_case = new ExtensionTests();
$test_case->run();
