<?php
/**
 * Test: IPub\DoctrineTimestampable\Timestampable
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

use Doctrine;
use Doctrine\ORM;
use Doctrine\Common;

use IPub;
use IPub\DoctrineTimestampable;
use IPub\DoctrineTimestampable\Events;
use IPub\DoctrineTimestampable\Mapping;

require __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/models/ArticleEntity.php';
require_once __DIR__ . '/models/ArticleMultiChangeEntity.php';
require_once __DIR__ . '/models/TypeEntity.php';

/**
 * Registering doctrine Timestampable functions tests
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Tests
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class TimestampableTest extends Tester\TestCase
{
	/**
	 * @var \Nette\DI\Container
	 */
	private $container;

	/**
	 * @var \Kdyby\Doctrine\EntityManager
	 */
	private $em;

	/**
	 * @var Events\TimestampableListener
	 */
	private $listener;

	/**
	 * @var DoctrineTimestampable\Configuration
	 */
	private $configuration;

	protected function setUp()
	{
		parent::setUp();

		$this->container = $this->createContainer();
		$this->em = $this->container->getByType('Kdyby\Doctrine\EntityManager');
		$this->listener = $this->container->getByType('IPub\DoctrineTimestampable\Events\TimestampableListener');
		$this->configuration = $this->container->getByType('IPub\DoctrineTimestampable\Configuration');
	}

	public function testCreate()
	{
		$this->generateDbSchema();

		$article = new Models\ArticleEntity;

		$this->em->persist($article);
		$this->em->flush();

		Assert::true($article->getCreatedAt() instanceof \DateTime);
		Assert::true($article->getUpdatedAt()  instanceof \DateTime);
		Assert::equal($article->getCreatedAt(), $article->getUpdatedAt());
		Assert::null($article->getPublishedAt());
	}

	public function testUpdate()
	{
		$this->generateDbSchema();

		$article = new Models\ArticleEntity;

		$this->em->persist($article);
		$this->em->flush();

		$id = $article->getId();
		$createdAt = $article->getCreatedAt();

		$this->em->clear();

		// Wait for a second
		sleep(1);

		$article = $this->em->getRepository('IPubTests\DoctrineTimestampable\Models\ArticleEntity')->find($id);
		$article->setTitle('Updated title'); // Need to modify at least one column to trigger onUpdate

		$this->em->flush();

		Assert::equal($createdAt, $article->getCreatedAt());
		Assert::notEqual($article->getCreatedAt(), $article->getUpdatedAt());
		Assert::null($article->getPublishedAt());

		// Wait for a second
		sleep(1);

		$published = new Models\TypeEntity;
		$published->setTitle('Published');

		$article->setType($published);

		$this->em->persist($article);
		$this->em->persist($published);
		$this->em->flush();
		$this->em->clear();

		$id = $article->getId();

		$article = $this->em->getRepository('IPubTests\DoctrineTimestampable\Models\ArticleEntity')->find($id);

		Assert::notEqual($article->getPublishedAt(), $article->getUpdatedAt());
		Assert::true($article->getPublishedAt() instanceof \DateTime);
	}

	public function testRemove()
	{
		$this->generateDbSchema();

		$article = new Models\ArticleEntity;

		$this->em->persist($article);
		$this->em->flush();

		$id = $article->getId();

		$this->em->clear();

		$article = $this->em->getRepository('IPubTests\DoctrineTimestampable\Models\ArticleEntity')->find($id);

		$this->em->remove($article);
		$this->em->flush();
		$this->em->clear();

		Assert::true($article->getDeletedAt() instanceof \DateTime);
	}

	public function testForcedValues()
	{
		$this->generateDbSchema();

		$testDate = new \DateTime('now -1week');

		$article = new Models\ArticleEntity;
		$article->setTitle('Article forced');
		$article->setCreatedAt($testDate);
		$article->setUpdatedAt($testDate);

		$this->em->persist($article);
		$this->em->flush();
		$this->em->clear();

		$id = $article->getId();

		$article = $this->em->getRepository('IPubTests\DoctrineTimestampable\Models\ArticleEntity')->find($id);

		Assert::equal($testDate, $article->getCreatedAt());
		Assert::equal($testDate, $article->getUpdatedAt());
		Assert::null($article->getPublishedAt());

		$published = new Models\TypeEntity;
		$published->setTitle('Published');

		$article->setType($published);
		$article->setPublishedAt('forcedUser');

		$this->em->persist($article);
		$this->em->persist($published);
		$this->em->flush();
		$this->em->clear();

		$id = $article->getId();

		$article = $this->em->getRepository('IPubTests\DoctrineTimestampable\Models\ArticleEntity')->find($id);

		Assert::true($article->getPublishedAt() instanceof \DateTime);
	}

	public function testMultipleValueTrackingField()
	{
		$this->generateDbSchema();

		$article = new Models\ArticleMultiChangeEntity;

		$this->em->persist($article);
		$this->em->flush();

		$id = $article->getId();

		$article = $this->em->getRepository('IPubTests\DoctrineTimestampable\Models\ArticleMultiChangeEntity')->find($id);

		Assert::true($article->getCreatedAt() instanceof \DateTime);
		Assert::true($article->getUpdatedAt() instanceof \DateTime);
		Assert::null($article->getPublishedAt());

		$draft = new Models\TypeEntity;
		$draft->setTitle('Draft');

		$article->setType($draft);

		$this->em->persist($article);
		$this->em->persist($draft);
		$this->em->flush();

		Assert::null($article->getPublishedAt());

		$published = new Models\TypeEntity;
		$published->setTitle('Published');

		$article->setType($published);

		$this->em->persist($article);
		$this->em->persist($published);
		$this->em->flush();

		$firstPublished = $article->getPublishedAt();

		Assert::true($article->getPublishedAt() instanceof \DateTime);

		$article->setType($draft);

		$this->em->persist($article);
		$this->em->flush();

		Assert::equal($firstPublished, $article->getPublishedAt());

		$deleted = new Models\TypeEntity;
		$deleted->setTitle('Deleted');

		$article->setType($deleted);

		$this->em->persist($article);
		$this->em->persist($deleted);
		$this->em->flush();

		Assert::notEqual($firstPublished, $article->getPublishedAt());
		Assert::true($article->getPublishedAt() instanceof \DateTime);
	}

	private function generateDbSchema()
	{
		$schema = new ORM\Tools\SchemaTool($this->em);
		$schema->createSchema($this->em->getMetadataFactory()->getAllMetadata());
	}

	/**
	 * @return Nette\DI\Container
	 */
	protected function createContainer()
	{
		$rootDir = __DIR__ . '/../../';

		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		$config->addParameters(['container' => ['class' => 'SystemContainer_' . md5('withModel')]]);
		$config->addParameters(['appDir' => $rootDir, 'wwwDir' => $rootDir]);

		$config->addConfig(__DIR__ . '/files/config.neon', !isset($config->defaultExtensions['nette']) ? 'v23' : 'v22');
		$config->addConfig(__DIR__ . '/files/entities.neon', $config::NONE);

		DoctrineTimestampable\DI\DoctrineTimestampableExtension::register($config);

		return $config->createContainer();
	}
}

\run(new TimestampableTest());
