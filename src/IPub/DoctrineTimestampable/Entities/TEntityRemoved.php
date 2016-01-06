<?php
/**
 * TEntityRemoved.php
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
 * Doctrine timestampable removing entity
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
trait TEntityRemoved
{
	/**
	 * @var \DateTime
	 *
	 * @IPub\Timestampable(on="delete")
	 */
	protected $deletedAt;

	/**
	 * @param \DateTime $deletedAt
	 */
	public function setDeletedAt(\DateTime $deletedAt)
	{
		$this->deletedAt = $deletedAt;
	}

	/**
	 * @return \DateTime
	 */
	public function getDeletedAt()
	{
		return $this->deletedAt;
	}
}
