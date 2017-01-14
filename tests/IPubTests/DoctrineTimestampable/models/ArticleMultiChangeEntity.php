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
class ArticleMultiChangeEntity implements Entities\IEntityCreated, Entities\IEntityUpdated, Entities\IEntityRemoved
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
	 * @var mixed
	 *
	 * @IPub\Timestampable(on="change", field="type.title", value={"Published", "Deleted"})
	 */
	private $publishedAt;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @param TypeEntity $type
	 */
	public function setType(TypeEntity $type)
	{
		$this->type = $type;
	}

	/**
	 * @param mixed $publishedAt
	 */
	public function setPublishedAt($publishedAt)
	{
		$this->publishedAt = $publishedAt;
	}

	/**
	 * @return mixed
	 */
	public function getPublishedAt()
	{
		return $this->publishedAt;
	}
}
