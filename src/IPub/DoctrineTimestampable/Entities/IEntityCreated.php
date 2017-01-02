<?php
/**
 * IEntityCreated.php
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
	 * @param \DateTime $createdAt
	 *
	 * @return $this
	 */
	function setCreatedAt(\DateTime $createdAt);

	/**
	 * @return \DateTime
	 */
	function getCreatedAt();
}
