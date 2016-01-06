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
 * @date           07.01.16
 */

namespace IPubTests\DoctrineTimestampable\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TypeEntity
{
	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\Column(name="title", type="string", length=128)
	 */
	private $title;

	/**
	 * @ORM\OneToMany(targetEntity="ArticleEntity", mappedBy="type")
	 */
	private $articles;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}
}
