<?php
/**
 * UnexpectedValueException.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Exceptions
 * @since          1.0.0
 *
 * @date           04.01.16
 */

declare(strict_types = 1);

namespace IPub\DoctrineTimestampable\Exceptions;

class UnexpectedValueException extends \UnexpectedValueException implements IException
{
}
