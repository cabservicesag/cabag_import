<?php
namespace Cabag\CabagImport\Domain\Repository\Storage;
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Nils Blattner <nb@cabag.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * CSV storage for the 'cabag_import' extension.
 *
 * @author	Nils Blattner <nb@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class Csv implements StorageInterface {
	/**
	 * @var tx_cabagimport_handler The handler using this storage.
	 */
	protected $handler = null;

	/**
	 * @var array The config array.
	 */
	protected $conf = array();

	/**
	 * @var array The index over the unique fields.
	 */
	protected $index = array();

	/**
	 * @var array The config for each field.
	 */
	protected $fields = array();

	/**
	 * @var array The key fields.
	 */
	protected $keys = array();

	/**
	 * @var string The mode to open the file with.
	 */
	protected $writeMode = 'w';

	/**
	 * @var string The path to the file that is written to.
	 */
	protected $filePath = '';

	/**
	 * @var file_resource The file that is written to.
	 */
	protected $file = null;

	/**
	 * @var int The next row that will be written.
	 */
	protected $nextRow = 0;

	/**
	 * @var string The string delimiting fields.
	 */
	protected $delimiter = ',';

	/**
	 * @var string The string enclosing fields.
	 */
	protected $enclosure = '"';

	/**
	 * Close the open file when the object gets removed.
	 *
	 * @return void
	 */
	public function __destruct() {
		if ($this->file !== null) {
			@fclose($this->file);
		}
	}

	/**
	 * Main function, initiates the class.
	 *
	 * @param tx_cabagimport_handler $tx_cabagimport_handler The handler using this storage.
	 * @return void
	 */
	public function main($tx_cabagimport_handler, $conf = array()) {
		$this->handler = $tx_cabagimport_handler;

		if (is_array($conf)) {
			$this->conf = $conf;
		} else {
			$this->conf = array();
		}

		$this->index = array();

		if ($this->file !== null) {
			@fclose($this->file);
			$this->file = null;
		}

		if (!is_array($this->conf['file.']) || empty($this->conf['file.']['path']) || is_dir($this->conf['file.']['path'])) {
			throw new \Exception($GLOBALS['LANG']->getLL('storage.exception.noFileConf'));
		}

		// remove any slashes that are too much and make it an absolute path
		$filePath = PATH_site . preg_replace(
			array(
				'/^\//',
				'/\/$/'
			),
			array(
				'',
				''
			),
			$this->conf['file.']['path']
		);

		$this->writeMode = 'w';

		if ($this->conf['file.']['mode'] === 'overwrite') {
			// nothing to do
		} else if ($this->conf['file.']['mode'] === 'append') {
			$this->writeMode = 'a';
		} else {
			if (file_exists($filePath)) {
				// find the first file of the form filename_1.ext that does not exist
				$matches = array();
				$regex = '/^(.*[\/\\][a-zA-Z0-9\-_\.]+)(\.[a-zA-Z0-9\-_]+)?$/ix';
				
				if (!preg_match($regex, $filePath, $matches)) {
					// bad file format
					throw new \Exception($GLOBALS['LANG']->getLL('storage.exception.badFile'));
				}

				$base = $matches[1];
				$ext = $matches[2];
				$c = 1;
				$filePath = $base . '_' . $c . $ext;

				while (file_exists($filePath)) {
					$c++;
					$filePath = $base . '_' . $c . $ext;
				}
			}
		}

		$this->filePath = $filePath;

		$this->nextRow = 0;

		$this->delimiter = substr(isset($this->conf['delimiter.']['chr']) ? chr(intval($this->conf['delimiter.']['chr'])) : $this->conf['delimiter'], 0, 1);
		$this->delimiter = empty($this->delimiter) ? ',' : $this->delimiter;

		$this->enclosure = substr(isset($this->conf['enclosure.']['chr']) ? chr(intval($this->conf['enclosure.']['chr'])) : $this->conf['enclosure'], 0, 1);
		$this->enclosure = empty($this->enclosure) ? '"' : $this->enclosure;

		$this->fields = array();
		
		if (!is_array($this->conf['fields.'])) {
			$this->conf['fields.'] = array();
		}

		foreach ($this->conf['fields.'] as $key => $fieldConf) {
			$tKey = $key;

			if (substr($key, -1) == '.') {
				if (isset($this->conf['fields.'][substr($key, 0, -1)])) {
					continue;
				}
				$tKey = substr($key, 0, -1);
			} else {
				continue;
			}

			$this->fields[$tKey] = $fieldConf;
		}

		$this->keys = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->handler->conf['handler.']['keyFields']);
	}

	/**
	 * Write the row.
	 *
	 * @param array $row The row to write.
	 * @return mixed This will not be processed by the handler.
	 */
	public function writeRow($row=false) {
		return $this->insertRow($row);
	}

	/**
	 * Write the row.
	 *
	 * @param array $row The row to write.
	 * @return mixed This will not be processed by the handler.
	 */
	public function insertRow($row=false, $table=false) {
		if ($row === false || empty($row)) {
			throw new \Exception($GLOBALS['LANG']->getLL('storage.exception.emptyRow'));
		}

		// make sure the file is opened
		$this->prepareWrite();

		// throws an exception if file can't be read
		$this->canWrite(true);
		
		if ($this->nextRow === 0 && ($this->conf['writeTitleRow'] || $this->conf['fields.']['writeTitleRow'])) {
			$titleRow = array();
			foreach ($row as $key => $fieldConf) {
				$titleRow[$key] = $this->fields[$key]['title'] ? $this->fields[$key]['title'] : $key;				
				// convert the header with the charset defined in the handler
				if(!empty($this->handler->conf['handler.']['in_charset']) && !empty($this->handler->conf['handler.']['out_charset'])){  
					$titleRow[$key] = iconv($this->handler->conf['handler.']['in_charset'], $this->handler->conf['handler.']['out_charset'], $titleRow[$key]);
				}
			}
			
			$this->fputcsv($titleRow);
			$this->nextRow++;
		}

		$row = $this->prepareRow($row);

		$this->handler->setMessage($GLOBALS['LANG']->getLL('storage.status.newRecord') . $this->getKeyFields($row));

		$this->fputcsv($row);

		$this->nextRow++;

		return $nextRow;
	}

	/**
	 * Dummy function, data cannot be manipulated once written at the moment.
	 *
	 * @return boolean false
	 */
	public function updateRow($row=false, $table=false) {
		return false;
	}

	/**
	 * Dummy function, data cannot be manipulated once written at the moment.
	 *
	 * @return boolean false
	 */
	public function deleteObsolete($table, $addWhere = '') {
		return false;
	}

	/**
	 * Dummy function, data cannot be manipulated once written at the moment.
	 *
	 * @return boolean false
	 */
	public function setTstamp($table, $uid) {
		return false;
	}

	/**
	 * Dummy function, no mm relations possible at this moment.
	 *
	 * @return boolean false
	 */
	public function createMMRelation($relations, $rowUid=false) {
		return false;
	}

	/**
	 * Returns if this object is in dry mode.
	 *
	 * @return boolean Whether or not this object is in dry mode.
	 */
	public function isDryMode() {
		if ($this->handler !== null) {
			return $this->handler->dryMode;
		} else {
			return true;
		}
	}

	/**
	 * Returns if this object can write at the moment.
	 *
	 * @param boolean $throw\Exception If set this function will throw an exception instead of return a boolean.
	 * @return boolean Whether or not this object can write at the moment.
	 */
	public function canWrite($throw\Exception = false) {
		$canWrite = $this->file !== NULL || $this->isDryMode();

		if ($throw\Exception && !$canWrite) {
			throw new \Exception($GLOBALS['LANG']->getLL('storage.exception.fileNoWrite'));
		}

		return $canWrite;
	}

	/**
	 * Prepares a row to be written.
	 *
	 * @param array $row The parsed row.
	 * @return array The row ready to be written.
	 */
	protected function prepareRow($row) {
		foreach ($this->fields as $key => &$fieldConf) {
			if ($fieldConf['isTstamp']) {
				$row[$key] = $this->handler->time;
			}
		}
		return $row;
	}

	/**
	 * Returns the key fields for a row.
	 *
	 * @param array $row The row.
	 * @param boolean $asArray Returns the fields as array if set, as query otherwise.
	 * @return array The key fields.
	 */
	protected function getKeyFields($row, $asArray = true) {
		$keyFields = array();
		foreach ($this->keys as $key) {
			if ($asArray) {
				$keyFields[$key] = $row[$key];
			} else {
				$keyFields[] = $key . ' = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($row[$key]);
			}
		}

		if ($asArray) {
			return $keyFields;
		} else {
			return '(' . implode(' AND ', $keyFields) . ')';
		}
	}

	/**
	 * Prepares the file to write.
	 *
	 * @return void
	 */
	protected function prepareWrite() {
		if ($this->file === null) {
			if (empty($this->filePath)) {
				throw new \Exception($GLOBALS['LANG']->getLL('storage.exception.badFileFormat'));
			}
			$this->file = fopen($this->filePath, $this->writeMode);

			if ($this->file === FALSE) {
				$this->file = null;
				throw new \Exception($GLOBALS['LANG']->getLL('storage.exception.badFile'));
			}
		}
	}

	/**
	 * Alias for fputcsv() where the writability is checked and only the row has to be provided.
	 *
	 * @param array $row The row to write.
	 * @return mixed Returns the length of the written string or throws an exception on failure.
	 */
	protected function fputcsv($row) {
		if ($this->isDryMode()) {
			return false;
		}

		if (!@fputcsv($this->file, $row, $this->delimiter, $this->enclosure)) {
			throw new \Exception($GLOBALS['LANG']->getLL('storage.exception.fileErrorOnWrite'));
		}
	}
}
