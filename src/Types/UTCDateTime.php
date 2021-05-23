<?php declare(strict_types = 1);

/**
 * UTCDateTime.php
 *
 * @copyright      More in LICENSE.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Types
 * @since          1.0.0
 *
 * @date           06.01.15
 */

namespace IPub\DoctrineTimestampable\Types;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\DBAL\Platforms;
use Doctrine\DBAL\Types;

/**
 * Doctrine phone data type
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class UTCDateTime extends Types\DateTimeType
{

	// Define datatype name
	public const UTC_DATETIME = 'utcdatetime';

	/** @var DateTimeZone|null */
	private static ?DateTimeZone $utc = null;

	/**
	 * @param mixed $value
	 * @param Platforms\AbstractPlatform $platform
	 *
	 * @return DateTimeInterface|null
	 *
	 * @throws Types\ConversionException
	 */
	// phpcs:ignore Generic.NamingConventions.CamelCapsFunctionName.ScopeNotCamelCaps
	public function convertToPHPValue($value, Platforms\AbstractPlatform $platform): ?DateTimeInterface
	{
		if ($value === null) {
			return null;
		}

		if (self::$utc === null) {
			self::$utc = new DateTimeZone('UTC');
		}

		$val = DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, self::$utc);

		if ($val === false) {
			throw Types\ConversionException::conversionFailed($value, $this->getName());
		}

		return $val;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::UTC_DATETIME;
	}

	/**
	 * @param mixed $value
	 * @param Platforms\AbstractPlatform $platform
	 *
	 * @return string|null
	 */
	public function convertToDatabaseValue($value, Platforms\AbstractPlatform $platform): ?string
	{
		if ($value === null) {
			return null;
		}

		if (self::$utc === null) {
			self::$utc = new DateTimeZone('UTC');
		}

		$value->setTimeZone(self::$utc);

		return $value->format($platform->getDateTimeFormatString());
	}

}
