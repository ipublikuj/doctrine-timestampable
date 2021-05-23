<?php declare(strict_types = 1);

namespace Tests\Cases;

use DateTime;
use DateTimeInterface;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../BaseTestCase.php';

require_once __DIR__ . '/../../../libs/models/ArticleEntity.php';
require_once __DIR__ . '/../../../libs/models/ArticleMultiChangeEntity.php';
require_once __DIR__ . '/../../../libs/models/TypeEntity.php';

/**
 * @testCase
 */
class TimestampableTest extends BaseTestCase
{

	/** @var string[] */
	protected array $additionalConfigs = [
		__DIR__ . DIRECTORY_SEPARATOR . 'entities.neon',
	];

	public function testCreate(): void
	{
		$this->generateDbSchema();

		$article = new Models\ArticleEntity();

		$this->getEntityManager()->persist($article);
		$this->getEntityManager()->flush();

		Assert::true($article->getCreatedAt() instanceof DateTimeInterface);
		Assert::true($article->getUpdatedAt() instanceof DateTimeInterface);
		Assert::equal($article->getCreatedAt()->format(DATE_ATOM), $article->getUpdatedAt()->format(DATE_ATOM));
		Assert::null($article->getPublishedAt());
	}

	public function testUpdate(): void
	{
		$this->generateDbSchema();

		$article = new Models\ArticleEntity();

		$this->getEntityManager()->persist($article);
		$this->getEntityManager()->flush();

		$id = $article->getId();
		$createdAt = $article->getCreatedAt();

		$this->getEntityManager()->clear();

		// Wait for a second
		sleep(1);

		$article = $this->getEntityManager()->getRepository(Models\ArticleEntity::class)->find($id);
		$article->setTitle('Updated title'); // Need to modify at least one column to trigger onUpdate

		$this->getEntityManager()->flush();

		Assert::equal($createdAt->format(DATE_ATOM), $article->getCreatedAt()->format(DATE_ATOM));
		Assert::notEqual($article->getCreatedAt()->format(DATE_ATOM), $article->getUpdatedAt()->format(DATE_ATOM));
		Assert::null($article->getPublishedAt());

		// Wait for a second
		sleep(1);

		$published = new Models\TypeEntity();
		$published->setTitle('Published');

		$article->setType($published);

		$this->getEntityManager()->persist($article);
		$this->getEntityManager()->persist($published);
		$this->getEntityManager()->flush();
		$this->getEntityManager()->clear();

		$id = $article->getId();

		$article = $this->getEntityManager()->getRepository(Models\ArticleEntity::class)->find($id);

		Assert::notEqual($article->getPublishedAt()->format(DATE_ATOM), $article->getCreatedAt()->format(DATE_ATOM));
		Assert::equal($article->getPublishedAt()->format(DATE_ATOM), $article->getUpdatedAt()->format(DATE_ATOM));
		Assert::true($article->getPublishedAt() instanceof DateTimeInterface);
	}

	public function testRemove(): void
	{
		$this->generateDbSchema();

		$article = new Models\ArticleEntity();

		$this->getEntityManager()->persist($article);
		$this->getEntityManager()->flush();

		$id = $article->getId();

		$this->getEntityManager()->clear();

		$article = $this->getEntityManager()->getRepository(Models\ArticleEntity::class)->find($id);

		$this->getEntityManager()->remove($article);
		$this->getEntityManager()->flush();
		$this->getEntityManager()->clear();

		Assert::true($article->getDeletedAt() instanceof DateTimeInterface);
	}

	public function testForcedValues(): void
	{
		$this->generateDbSchema();

		$testDate = new DateTime('now -1week');

		$article = new Models\ArticleEntity();
		$article->setTitle('Article forced');
		$article->setCreatedAt($testDate);
		$article->setUpdatedAt($testDate);

		$this->getEntityManager()->persist($article);
		$this->getEntityManager()->flush();
		$this->getEntityManager()->clear();

		$id = $article->getId();

		$article = $this->getEntityManager()->getRepository(Models\ArticleEntity::class)->find($id);

		Assert::equal($testDate->format(DATE_ATOM), $article->getCreatedAt()->format(DATE_ATOM));
		Assert::equal($testDate->format(DATE_ATOM), $article->getUpdatedAt()->format(DATE_ATOM));
		Assert::null($article->getPublishedAt());

		$published = new Models\TypeEntity();
		$published->setTitle('Published');

		$publishedAt = new DateTime('now -1day');

		$article->setType($published);
		$article->setPublishedAt($publishedAt);

		$this->getEntityManager()->persist($article);
		$this->getEntityManager()->persist($published);
		$this->getEntityManager()->flush();
		$this->getEntityManager()->clear();

		$id = $article->getId();

		$article = $this->getEntityManager()->getRepository(Models\ArticleEntity::class)->find($id);

		Assert::true($article->getPublishedAt() instanceof DateTimeInterface);
		Assert::equal($publishedAt->format(DATE_ATOM), $article->getPublishedAt()->format(DATE_ATOM));
	}

	public function testMultipleValueTrackingField(): void
	{
		$this->generateDbSchema();

		$article = new Models\ArticleMultiChangeEntity();

		$this->getEntityManager()->persist($article);
		$this->getEntityManager()->flush();

		$id = $article->getId();

		$article = $this->getEntityManager()->getRepository(Models\ArticleMultiChangeEntity::class)->find($id);

		Assert::true($article->getCreatedAt() instanceof DateTimeInterface);
		Assert::true($article->getUpdatedAt() instanceof DateTimeInterface);
		Assert::null($article->getPublishedAt());

		$draft = new Models\TypeEntity();
		$draft->setTitle('Draft');

		$article->setType($draft);

		$this->getEntityManager()->persist($article);
		$this->getEntityManager()->persist($draft);
		$this->getEntityManager()->flush();

		Assert::null($article->getPublishedAt());

		$published = new Models\TypeEntity();
		$published->setTitle('Published');

		$article->setType($published);

		$this->getEntityManager()->persist($article);
		$this->getEntityManager()->persist($published);
		$this->getEntityManager()->flush();

		$firstPublished = $article->getPublishedAt();

		Assert::true($article->getPublishedAt() instanceof DateTimeInterface);

		$article->setType($draft);

		$this->getEntityManager()->persist($article);
		$this->getEntityManager()->flush();

		Assert::equal($firstPublished->format(DATE_ATOM), $article->getPublishedAt()->format(DATE_ATOM));

		// Wait for a second
		sleep(1);

		$deleted = new Models\TypeEntity();
		$deleted->setTitle('Deleted');

		$article->setType($deleted);

		$this->getEntityManager()->persist($article);
		$this->getEntityManager()->persist($deleted);
		$this->getEntityManager()->flush();

		Assert::notEqual($firstPublished->format(DATE_ATOM), $article->getPublishedAt()->format(DATE_ATOM));
		Assert::true($article->getPublishedAt() instanceof DateTimeInterface);
	}

}

$test_case = new TimestampableTest();
$test_case->run();
