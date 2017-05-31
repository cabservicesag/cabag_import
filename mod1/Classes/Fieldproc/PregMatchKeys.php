<?php
namespace Cabag\CabagImport\Fieldproc;
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Dimitri Koenig <dk@cabag.ch>
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
 * Collects all matched keys and returns all values, imploded, based on matched keys
 *
 * @author	Dimitri Koenig <dk@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class PregMatchKeys implements FieldprocInterface {
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
	function main($stackPartConf = FALSE, $object_handler) {
		global $LANG;

		if($stackPartConf != FALSE) {
			$this->conf = $stackPartConf;
		} else {
			throw new Exception($LANG->getLL('fieldProc.exception.noConfig'));
		}

		// use value or current field value
		if(!empty($this->conf['value'])){
			$object_handler->currentFieldValue = $this->conf['value'];
		}

		if(substr($stackPartConf['searchfor'], 0, 1) != '/') {
			$stackPartConf['searchfor'] = '/' . $stackPartConf['searchfor'] . '/';
		}

		if (empty($stackPartConf['implodeString'])) {
			$stackPartConf['implodeString'] = '';
		} else {
			if (preg_match('/#(.*)#/', $stackPartConf['implodeString'], $matches)) {
				$stackPartConf['implodeString'] = $matches[1];
			} else {
				$stackPartConf['implodeString'] = '';
			}
		}

		$currentRowRawKeys = array_keys($object_handler->currentRowRaw);
		$matchedValues = array();
		foreach ($currentRowRawKeys as $key) {
			if (preg_match($stackPartConf['searchfor'], $key)) {
				$matchedValues[$key] = $object_handler->currentRowRaw[$key];
			}
		}

		if (count($matchedValues)) {
			$object_handler->currentFieldValue = implode($stackPartConf['implodeString'], $matchedValues);
		} else {
			return FALSE;
		}

		return TRUE;
	}
}