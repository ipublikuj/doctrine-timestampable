<?php
/**
 * Timestampable.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Annotation
 * @since          1.0.0
 *
 * @date           06.01.16
 */

declare(strict_types = 1);

namespace IPub\DoctrineTimestampable\Mapping\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Doctrine Timestampable annotation for Doctrine2
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Annotation
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class Timestampable extends Annotation
{
	/**
	 * @var string
	 */
	public $on = 'update';

	/**
	 * @var string|array
	 */
	public $field;

	/**
	 * @var mixed
	 */
	public $value;
}
