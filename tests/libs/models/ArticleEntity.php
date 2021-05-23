<?php declare(strict_types = 1);

namespace Tests\Cases\Models;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use IPub\DoctrineTimestampable\Entities;
use IPub\DoctrineTimestampable\Mapping\Annotation as IPub;

/**
 * @ORM\Entity
 */
class ArticleEntity implements Entities\IEntityCreated, Entities\IEntityUpdated, Entities\IEntityRemoved
{

	use Entities\TEntityCreated;
	use Entities\TEntityUpdated;
	use Entities\TEntityRemoved;

	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private int $id;

	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	private string $title;

	/** @ORM\ManyToOne(targetEntity="TypeEntity", inversedBy="articles") */
	private TypeEntity $type;

	/**
	 * @var DateTimeInterface|null
	 *
	 * @IPub\Timestampable(on="change", field="type.title", value="Published")
	 */
	private ?DateTimeInterface $publishedAt = null;

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 *
	 * @return void
	 */
	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	/**
	 * @param TypeEntity $type
	 *
	 * @return void
	 */
	public function setType(TypeEntity $type): void
	{
		$this->type = $type;
	}

	/**
	 * @param DateTimeInterface|null $publishedAt
	 *
	 * @return void
	 */
	public function setPublishedAt(?DateTimeInterface $publishedAt): void
	{
		$this->publishedAt = $publishedAt;
	}

	/**
	 * @return DateTimeInterface|null
	 */
	public function getPublishedAt(): ?DateTimeInterface
	{
		return $this->publishedAt;
	}

}
