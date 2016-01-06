<?php
/**
 * TEntityUpdated.php
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

namespace IPub\DoctrineTimestampable\Entities;

use IPub\DoctrineTimestampable\Mapping\Annotation as IPub;

/**
 * Doctrine timestampable modifying entity
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
trait TEntityUpdated
{
	/**
	 * @var \DateTime
	 *
	 * @IPub\Timestampable(on="update")
	 */
	protected $updatedAt;

	/**
	 * @param \DateTime $updatedAt
	 */
	public function setUpdatedAt(\DateTime $updatedAt)
	{
		$this->updatedAt = $updatedAt;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}
}
