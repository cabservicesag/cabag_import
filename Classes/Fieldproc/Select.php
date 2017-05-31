<?php
namespace Cabag\CabagImport\Fieldproc;
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Sonja Scholz <ss@cabag.ch>
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
 * Field proccessor class for the 'cabag_import' extension.
 *
 * @author	Sonja Scholz <ss@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class  Select implements FieldprocInterface {
	// contains the configuration
	var $conf;

	/**
	* main()
	*
	* - Proove if the field is empty if the required option isset
	* - Loop the stack part and call for every part the right function in this class
	*
	* @param	array	mapping configuration for the current part includes required option and stack
	* @param	string	field with the data
	* @return	array	modificated field data/exception if a required field is empty for example
	*/
	function main($stackPartConf=false, $object_handler) {
		global $LANG;

		if ($stackPartConf != FALSE) {
			$this->conf = $stackPartConf;
		} else {
			throw new Exception($LANG->getLL('fieldProc.exception.noConfig'));
		}

		$selectRes = $GLOBALS['TYPO3_DB']->sql_query($this->conf['sql']);

		if ($this->conf['justDoTheQuery']) {
			return TRUE;
		}

		if ($GLOBALS['TYPO3_DB']->sql_num_rows($selectRes) == 0) {
			if (empty($this->conf['defaultToCurrent'])) {
				$object_handler->currentFieldValue = 0;
				if (array_key_exists('default', $this->conf)) {
					$object_handler->currentFieldValue = $this->conf['default'];
				}
			}
		} else {
			if ($this->conf['compileToListTemplate']) {
				$finalContent = array();
				$template = $this->conf['compileToListTemplate'];

				while ($selectRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($selectRes)) {
					$content = preg_replace_callback(
						'/%%(.*)%%/Uis',
						function($matches) use($selectRow) {
							$key = $matches[1];
							if (stripos($key, 'getIndpEnv:') === 0) {
								$param = str_ireplace('getIndpEnv:', '', $key);
								return \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv($param);
							}

							if (isset($selectRow[$key])) {
								return $selectRow[$key];
							}

							return $matches[0];
						},
						$template
					);
					$content = trim($content);
					if (!empty($content)) {
						$finalContent[] = $content;
					}
				}

				$object_handler->currentFieldValue = implode("\n", $finalContent);
			} else {
				$selectRow = $GLOBALS['TYPO3_DB']->sql_fetch_row($selectRes);
				$object_handler->currentFieldValue = $selectRow[0];
			}
		}

		return TRUE;
	}
}
