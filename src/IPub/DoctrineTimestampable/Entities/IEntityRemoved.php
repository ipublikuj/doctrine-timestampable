<?php
/**
 * IEntityRemoved.php
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

/**
 * Doctrine timestampable removing entity interface
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IEntityRemoved
{
	/**
	 * @param mixed $deletedAt
	 *
	 * @return $this
	 */
	function setDeletedAt($deletedAt);

	/**
	 * @return mixed
	 */
	function getDeletedAt();
}
