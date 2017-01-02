<?php
/**
 * Configuration.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     common
 * @since          1.0.0
 *
 * @date           06.01.16
 */

namespace IPub\DoctrineTimestampable;

use Nette;
use Nette\Http;

/**
 * Doctrine timestampable extension configuration storage
 * Store basic extension settings
 *
 * @package        iPublikuj:DoctrineTimestampable!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
final class Configuration extends Nette\Object
{
	/**
	 * Define class name
	 */
	const CLASS_NAME = __CLASS__;

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
	public function __construct($lazyAssociation = FALSE, $autoMapField = FALSE, $dbFieldType = 'datetime')
	{
		$this->lazyAssociation = $lazyAssociation;
		$this->autoMapField = $autoMapField;
		$this->dbFieldType = $dbFieldType;
	}

	/**
	 * @return bool
	 */
	public function autoMapField()
	{
		return $this->autoMapField === TRUE;
	}

	/**
	 * @return bool
	 */
	public function useLazyAssociation()
	{
		return $this->lazyAssociation === TRUE;
	}
}
