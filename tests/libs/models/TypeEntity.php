<?php declare(strict_types = 1);

namespace Tests\Cases\Models;

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
	private int $id;

	/** @ORM\Column(name="title", type="string", length=128) */
	private string $title;

	/** @ORM\OneToMany(targetEntity="ArticleEntity", mappedBy="type") */
	private $articles;

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
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
	 * @return string
	 */
	public function getTitle(): string
	{
		return $this->title;
	}

}
