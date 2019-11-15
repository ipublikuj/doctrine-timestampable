<?php
/**
 * IEntityCreated.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           06.01.15
 */

declare(strict_types = 1);

namespace IPub\DoctrineTimestampable\Entities;

/**
 * Doctrine timestampable creating entity interface
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IEntityCreated
{
	/**
	 * @param \DateTimeInterface $createdAt
	 *
	 * @return void
	 */
	public function setCreatedAt(\DateTimeInterface $createdAt) : void;

	/**
	 * @return \DateTimeInterface|NULL
	 */
	public function getCreatedAt() : ?\DateTimeInterface;
}
