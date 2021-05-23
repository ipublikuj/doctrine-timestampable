<?php declare(strict_types = 1);

/**
 * TEntityCreated.php
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
use IPub\DoctrineTimestampable\Mapping\Annotation as IPub;

/**
 * Doctrine timestampable creating entity
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
trait TEntityCreated
{

	/**
	 * @var DateTimeInterface|null
	 *
	 * @IPub\Timestampable(on="create")
	 */
	protected ?DateTimeInterface $createdAt = null;

	/**
	 * @return DateTimeInterface|null
	 */
	public function getCreatedAt(): ?DateTimeInterface
	{
		return $this->createdAt;
	}

	/**
	 * @param DateTimeInterface $createdAt
	 *
	 * @return void
	 */
	public function setCreatedAt(DateTimeInterface $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

}
