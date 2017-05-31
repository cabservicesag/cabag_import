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
 * @author Jonas Duebi <jd@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class  FileExists implements FieldprocInterface, TYPO3\CMS\Core\SingletonInterface {
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

		// use value or current field value
		if(!empty($stackPartConf['value'])){
			$object_handler->currentFieldValue = $stackPartConf['value'];
		}

		$fileExists = file_exists($object_handler->currentFieldValue);

		// possibility to define value for return if exists or if not exists
		if(!empty($stackPartConf['ifExists']) && $fileExists) {
			$object_handler->currentFieldValue = $stackPartConf['ifExists'];
		}

		// possibility to define value for return if exists or if not exists
		if(!empty($stackPartConf['ifNotExists']) && !$fileExists) {
			$object_handler->currentFieldValue = $stackPartConf['ifNotExists'];
		}

		// if no config for return is set just set 0 or 1
		if(empty($stackPartConf['ifExists']) && empty($stackPartConf['ifNotExists'])) {
			$object_handler->currentFieldValue = $fileExists;
		}

		return true;
	}
}
