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
	 * @var mixed
	 *
	 * @IPub\Timestampable(on="delete")
	 */
	protected $deletedAt;

	/**
	 * {@inheritdoc}
	 */
	public function setDeletedAt($deletedAt)
	{
		$this->deletedAt = $deletedAt;

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDeletedAt()
	{
		return $this->deletedAt;
	}
}
