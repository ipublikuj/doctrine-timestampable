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

declare(strict_types = 1);

namespace IPub\DoctrineTimestampable\Entities;

/**
 * Doctrine timestampable creating entity interface
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
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
