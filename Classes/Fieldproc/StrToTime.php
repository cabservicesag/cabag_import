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
class  StrToTime implements FieldprocInterface {
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
		$this->conf = $stackPartConf;
		
		// use value or current field value
		if(!empty($this->conf['value'])){
			$object_handler->currentFieldValue = $this->conf['value'];
		}
		// use value or current field value
		if(empty($object_handler->currentFieldValue) && isset($this->conf['default'])){
			$object_handler->currentFieldValue = $this->conf['default'] == 'now' ? time() : $this->conf['default'];
			return true;
		}
		
		// str to time current value
		if(isset($this->conf['timezone'])){
		    $object_handler->currentFieldValue = strtotime($object_handler->currentFieldValue.' '.$this->conf['timezone']);
		} else {
		    $object_handler->currentFieldValue = strtotime($object_handler->currentFieldValue);
		}
		
		return true;
	}
}