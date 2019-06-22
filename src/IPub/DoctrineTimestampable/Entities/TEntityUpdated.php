<?php
/**
 * TEntityUpdated.php
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

use IPub\DoctrineTimestampable\Mapping\Annotation as IPub;

/**
 * Doctrine timestampable modifying entity
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
trait TEntityUpdated
{
	/**
	 * @var \DateTimeInterface|NULL
	 *
	 * @IPub\Timestampable(on="update")
	 */
	protected $updatedAt;

	/**
	 * @param \DateTimeInterface $updatedAt
	 *
	 * @return void
	 */
	public function setUpdatedAt(\DateTimeInterface $updatedAt) : void
	{
		$this->updatedAt = $updatedAt;
	}

	/**
	 * @return \DateTimeInterface|NULL
	 */
	public function getUpdatedAt() : ?\DateTimeInterface
	{
		return $this->updatedAt;
	}
}
