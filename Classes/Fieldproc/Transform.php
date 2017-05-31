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
class  Transform implements FieldprocInterface {
	// contains the configuration
	var $conf;
	
	/**
	* main()
	* 
	* - Proove if the field is empty if the required option isset
	* 
	* @param	array	mapping configuration for the current part includes required option and stack
	* @param	string	object handler
	* @return	array	modificated field data/exception if a required field is empty for example
	*/
	function main($stackPartConf, $object_handler) {
		global $LANG;
		
		if(is_array($stackPartConf) && count($stackPartConf)>0) {
			$this->conf = $stackPartConf;
		} else {
			throw new Exception($LANG->getLL('fieldProc.exception.noConfig'));
		}
		
		// use value or current field value
		if(!empty($this->conf['value'])){
			$object_handler->currentFieldValue = $this->conf['value'];
		}
		
		// transform
		if(isset($this->conf['transform.'][$object_handler->currentFieldValue])){
			$object_handler->currentFieldValue = $this->conf['transform.'][$object_handler->currentFieldValue];
		} else if(isset($this->conf['default'])){
			$object_handler->currentFieldValue = $this->conf['default'];
		} else {
			throw new Exception($LANG->getLL('fieldProc.exception.notfoundinstransformation'));
		}
		return true;
	}
}
