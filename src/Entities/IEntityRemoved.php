<?php declare(strict_types = 1);

/**
 * IEntityRemoved.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Entities
 * @since          1.0.0
 *
 * @date           06.01.15
 */

namespace IPub\DoctrineTimestampable\Entities;

use DateTimeInterface;

/**
 * Doctrine timestampable removing entity interface
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
interface IEntityRemoved
{

	/**
	 * @param DateTimeInterface $deletedAt
	 *
	 * @return void
	 */
	public function setDeletedAt(DateTimeInterface $deletedAt): void;

	/**
	 * @return DateTimeInterface|null
	 */
	public function getDeletedAt(): ?DateTimeInterface;

}
