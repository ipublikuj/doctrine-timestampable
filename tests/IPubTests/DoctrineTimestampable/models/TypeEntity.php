<?php
/**
 * Test: IPub\DoctrineTimestampable\Models
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Tests
 * @since          1.0.0
 *
 * @date           07.01.16
 */

declare(strict_types = 1);

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
	public function getId() : int
	{
		return $this->id;
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
	 * @return string
	 */
	public function getTitle() : string
	{
		return $this->title;
	}
}
