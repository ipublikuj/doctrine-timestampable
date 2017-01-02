<?php
/**
 * UTCDateTime.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     Types
 * @since          1.0.0
 *
 * @date           06.01.15
 */

namespace IPub\DoctrineTimestampable\Types;

use Doctrine;
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
	 * Define class name
	 */
	const CLASS_NAME = __CLASS__;

	/**
	 * Define datatype name
	 */
	const UTC_DATETIME = 'utcdatetime';

	/**
	 * @var \DateTimeZone|NULL
	 */
	static private $utc = NULL;

	/**
	 * @return string
	 */
	public function getName()
	{
		return self::UTC_DATETIME;
	}

	/**
	 * @param mixed $value
	 * @param Platforms\AbstractPlatform $platform
	 *
	 * @return \DateTime|NULL
	 *
	 * @throws Types\ConversionException
	 */
	public function convertToPHPValue($value, Platforms\AbstractPlatform $platform)
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
	public function convertToDatabaseValue($value, Platforms\AbstractPlatform $platform)
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
