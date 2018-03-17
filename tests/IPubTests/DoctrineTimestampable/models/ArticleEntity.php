<?php
/**
 * Test: IPub\DoctrineTimestampable\Models
 * @testCase
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           05.01.16
 */

declare(strict_types = 1);

namespace IPubTests\DoctrineTimestampable\Models;

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
	private $id;

	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $title;

	/**
	 * @ORM\ManyToOne(targetEntity="TypeEntity", inversedBy="articles")
	 */
	private $type;

	/**
	 * @var \DateTimeInterface|NULL
	 *
	 * @IPub\Timestampable(on="change", field="type.title", value="Published")
	 */
	private $publishedAt;

	/**
	 * @return int
	 */
	public function getId() : int
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTitle() : string
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 *
	 * @return void
	 */
	public function setTitle(string $title) : void
	{
		$this->title = $title;
	}

	/**
	 * @param TypeEntity $type
	 *
	 * @return void
	 */
	public function setType(TypeEntity $type) : void
	{
		$this->type = $type;
	}

	/**
	 * @param \DateTimeInterface|NULL $publishedAt
	 *
	 * @return void
	 */
	public function setPublishedAt(?\DateTimeInterface $publishedAt) : void
	{
		$this->publishedAt = $publishedAt;
	}

	/**
	 * @return \DateTimeInterface|NULL
	 */
	public function getPublishedAt() : ?\DateTimeInterface
	{
		return $this->publishedAt;
	}
}
