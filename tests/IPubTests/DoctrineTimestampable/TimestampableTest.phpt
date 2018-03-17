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

declare(strict_types = 1);

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

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once __DIR__ . DS . 'models' . DS . 'ArticleEntity.php';
require_once __DIR__ . DS . 'models' . DS . 'ArticleMultiChangeEntity.php';
require_once __DIR__ . DS . 'models' . DS . 'TypeEntity.php';

/**
 * Registering doctrine Timestampable functions tests
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Tests
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class TimestampableTest extends Tester\TestCase
{
	/**
	 * @var \Nette\DI\Container
	 */
	private $container;

	/**
	 * @var ORM\EntityManager
	 */
	private $em;

	/**
	 * {@inheritdoc}
	 */
	protected function setUp() : void
	{
		parent::setUp();

		$this->container = $this->createContainer();
		$this->em = $this->container->getByType('Kdyby\Doctrine\EntityManager');
	}

	public function testCreate() : void
	{
		$this->generateDbSchema();

		$article = new Models\ArticleEntity;

		$this->em->persist($article);
		$this->em->flush();

		Assert::true($article->getCreatedAt() instanceof \DateTime);
		Assert::true($article->getUpdatedAt()  instanceof \DateTime);
		Assert::equal($article->getCreatedAt()->format('Ymd H:i:s'), $article->getUpdatedAt()->format('Ymd H:i:s'));
		Assert::null($article->getPublishedAt());
	}

	public function testUpdate() : void
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

		$article = $this->em->getRepository(Models\ArticleEntity::class)->find($id);
		$article->setTitle('Updated title'); // Need to modify at least one column to trigger onUpdate

		$this->em->flush();

		Assert::equal($createdAt->format('Ymd H:i:s'), $article->getCreatedAt()->format('Ymd H:i:s'));
		Assert::notEqual($article->getCreatedAt()->format('Ymd H:i:s'), $article->getUpdatedAt()->format('Ymd H:i:s'));
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

		$article = $this->em->getRepository(Models\ArticleEntity::class)->find($id);

		Assert::notEqual($article->getPublishedAt()->format('Ymd H:i:s'), $article->getCreatedAt()->format('Ymd H:i:s'));
		Assert::equal($article->getPublishedAt()->format('Ymd H:i:s'), $article->getUpdatedAt()->format('Ymd H:i:s'));
		Assert::true($article->getPublishedAt() instanceof \DateTime);
	}

	public function testRemove() : void
	{
		$this->generateDbSchema();

		$article = new Models\ArticleEntity;

		$this->em->persist($article);
		$this->em->flush();

		$id = $article->getId();

		$this->em->clear();

		$article = $this->em->getRepository(Models\ArticleEntity::class)->find($id);

		$this->em->remove($article);
		$this->em->flush();
		$this->em->clear();

		Assert::true($article->getDeletedAt() instanceof \DateTime);
	}

	public function testForcedValues() : void
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

		$article = $this->em->getRepository(Models\ArticleEntity::class)->find($id);

		Assert::equal($testDate, $article->getCreatedAt());
		Assert::equal($testDate, $article->getUpdatedAt());
		Assert::null($article->getPublishedAt());

		$published = new Models\TypeEntity;
		$published->setTitle('Published');

		$publishedAt = new \DateTime('now -1day');

		$article->setType($published);
		$article->setPublishedAt($publishedAt);

		$this->em->persist($article);
		$this->em->persist($published);
		$this->em->flush();
		$this->em->clear();

		$id = $article->getId();

		$article = $this->em->getRepository(Models\ArticleEntity::class)->find($id);

		Assert::true($article->getPublishedAt() instanceof \DateTime);
		Assert::equal($publishedAt->format('Ymd H:i:s'), $article->getPublishedAt()->format('Ymd H:i:s'));
	}

	public function testMultipleValueTrackingField() : void
	{
		$this->generateDbSchema();

		$article = new Models\ArticleMultiChangeEntity;

		$this->em->persist($article);
		$this->em->flush();

		$id = $article->getId();

		$article = $this->em->getRepository(Models\ArticleMultiChangeEntity::class)->find($id);

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

		Assert::equal($firstPublished->format('Ymd H:i:s'), $article->getPublishedAt()->format('Ymd H:i:s'));

		// Wait for a second
		sleep(1);

		$deleted = new Models\TypeEntity;
		$deleted->setTitle('Deleted');

		$article->setType($deleted);

		$this->em->persist($article);
		$this->em->persist($deleted);
		$this->em->flush();

		Assert::notEqual($firstPublished->format('Ymd H:i:s'), $article->getPublishedAt()->format('Ymd H:i:s'));
		Assert::true($article->getPublishedAt() instanceof \DateTime);
	}

	/**
	 * @return void
	 *
	 * @throws ORM\Tools\ToolsException
	 */
	private function generateDbSchema() : void
	{
		$schema = new ORM\Tools\SchemaTool($this->em);
		$schema->createSchema($this->em->getMetadataFactory()->getAllMetadata());
	}

	/**
	 * @return Nette\DI\Container
	 */
	protected function createContainer() : Nette\DI\Container
	{
		$rootDir = __DIR__ . '/../../';

		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		$config->addParameters(['container' => ['class' => 'SystemContainer_' . md5((string) time())]]);
		$config->addParameters(['appDir' => $rootDir, 'wwwDir' => $rootDir]);

		$config->addConfig(__DIR__ . DS . 'files' . DS . 'config.neon');
		$config->addConfig(__DIR__ . DS . 'files' . DS . 'entities.neon');

		DoctrineTimestampable\DI\DoctrineTimestampableExtension::register($config);

		return $config->createContainer();
	}
}

\run(new TimestampableTest());
