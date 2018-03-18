<?php
/**
 * UTCDateTime.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Types
 * @since          1.0.0
 *
 * @date           06.01.15
 */

declare(strict_types = 1);

namespace IPub\DoctrineTimestampable\Types;

use Doctrine\DBAL\Platforms;
use Doctrine\DBAL\Types;

use IPub;

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
	/**
	 * Define datatype name
	 */
	public const UTC_DATETIME = 'utcdatetime';

	/**
	 * @var \DateTimeZone|NULL
	 */
	static private $utc = NULL;

	/**
	 * @return string
	 */
	public function getName() : string
	{
		return self::UTC_DATETIME;
	}

	/**
	 * @param mixed $value
	 * @param Platforms\AbstractPlatform $platform
	 *
	 * @return \DateTimeInterface|NULL
	 *
	 * @throws Types\ConversionException
	 */
	public function convertToPHPValue($value, Platforms\AbstractPlatform $platform) : ?\DateTimeInterface
	{
		if ($value === NULL) {
			return NULL;
		}

		if (self::$utc === NULL) {
			self::$utc = new \DateTimeZone('UTC');
		}

		$val = \DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, self::$utc);

		if (!$val) {
			throw Types\ConversionException::conversionFailed($value, $this->getName());
		}

		return $val;
	}

	/**
	 * @param mixed $value
	 * @param Platforms\AbstractPlatform $platform
	 *
	 * @return string|NULL
	 */
	public function convertToDatabaseValue($value, Platforms\AbstractPlatform $platform) : ?string
	{
		if ($value === NULL) {
			return NULL;
		}

		if (self::$utc === NULL) {
			self::$utc = new \DateTimeZone('UTC');
		}

		$value->setTimeZone(self::$utc);

		return $value->format($platform->getDateTimeFormatString());
	}
}
