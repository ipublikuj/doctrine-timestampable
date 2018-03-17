<?php
/**
 * Configuration.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           06.01.16
 */

declare(strict_types = 1);

namespace IPub\DoctrineTimestampable;

use Nette;

/**
 * Doctrine timestampable extension configuration storage
 * Store basic extension settings
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Configuration
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * Flag if use lazy association or not
	 *
	 * @var bool
	 */
	public $lazyAssociation = FALSE;

	/**
	 * Automatically map filed if not set
	 *
	 * @var bool
	 */
	public $autoMapField = TRUE;

	/**
	 * Default database type
	 *
	 * @var string
	 */
	public $dbFieldType = 'datetime';

	/**
	 * @param bool $lazyAssociation
	 * @param bool $autoMapField
	 * @param string $dbFieldType
	 */
	public function __construct(bool $lazyAssociation = FALSE, bool $autoMapField = FALSE, string $dbFieldType = 'datetime')
	{
		$this->lazyAssociation = $lazyAssociation;
		$this->autoMapField = $autoMapField;
		$this->dbFieldType = $dbFieldType;
	}

	/**
	 * @return bool
	 */
	public function autoMapField() : bool
	{
		return $this->autoMapField === TRUE;
	}

	/**
	 * @return bool
	 */
	public function useLazyAssociation() : bool
	{
		return $this->lazyAssociation === TRUE;
	}
}
