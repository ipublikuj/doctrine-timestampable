<?php
/**
 * IEntityUpdated.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           06.01.15
 */

declare(strict_types = 1);

namespace IPub\DoctrineTimestampable\Entities;

/**
 * Doctrine timestampable modifying entity interface
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IEntityUpdated
{
	/**
	 * @param \DateTimeInterface $updatedAt
	 *
	 * @return void
	 */
	function setUpdatedAt(\DateTimeInterface $updatedAt) : void;

	/**
	 * @return \DateTimeInterface|NULL
	 */
	function getUpdatedAt() : ?\DateTimeInterface;
}
