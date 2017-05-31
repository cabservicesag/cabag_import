<?php
namespace Cabag\CabagImport\Interpret;
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
 * Interpreter class for the 'cabag_import' extension.
 *
 * @author	Sonja Scholz <ss@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class Xml implements InterpretInterface {
	// Resource of the file/db etc
	var $resource;
	
	// interpret configuration part
	var $conf;
	
	// Array of the key fields/first line of the file
	var $key_fields;
	
	/**
	* Constructor
	* 
	* - Throws an exception if no file resource is given, or no conf is given
	* - Checks if the file exists
	* 
	* @param	array		interpreter part of the import configuration
	* @param	resource	file resource of the source file
	*/
	function init($conf=false, $source=false) {
		global $LANG, $TYPO3_CONF_VARS;
		
		// Save the interpret configuration part
		if(is_array($conf)) {
			$this->conf = $conf;
		} else {
			throw new Exception($LANG->getLL('interpret.exception.noConfig'));
		}
				
		while (!feof($source->resource)) {
			$this->xmlRaw .= fgets($source->resource, 4096);
		}
		
		if($conf['utf8_decode']) {
			$this->xmlArray = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2tree(utf8_decode($this->xmlRaw));
		} else {
			$this->xmlArray = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2tree($this->xmlRaw);
		}
		$this->recordPath = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->conf['recordPath'], 1);
		
		foreach($this->recordPath as $key){
			if(!empty($this->xmlArray[$key])){
				$this->xmlArray = $this->xmlArray[$key];
			} else {
				throw new Exception($LANG->getLL('interpret.exception.invalidRecordPath'). '-'.$key);
			}
		}
		
		// debug
		if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('parsed xml', 'cabag_import', -1, array('conf' => $this->conf, 'raw' => $this->xmlRaw, 'xml' => $this->xmlArray));
	}
	
	/**
	* getNextRow()
	* 
	* - Get the next row with the file resource
	* - If the row is the first row/consists the key fields save them to the array $key_fields
	* - Returns the array with the current row/the keyfields or throw an exception if there is no row any more
	* 
	* for example: array('SpecialKeyFieldName' => 'Scholz',
	*					 'Prename' => 'Sonja');
	* 
	* @return	array/string	current Row from the file or an exception
	*/
	function getNextRow() {
		global $LANG;
		
		// Read it as csv
		if(list(,$arrayLine) = each($this->xmlArray)) {
			return $this->getFlatArray($arrayLine);
		} else {
			return false;
			//throw new Exception($LANG->getLL('interpret.exception.notReadable'));
		}
	}
	
	/**
	* getFlatArray()
	*
	* - returns a flattened array
	*
	* @param array recursive array
	* @return array flat array
	*/
	function getFlatArray($array){
		do {
			$subArraysFound = false;
			
			foreach($array as $key => $value){
				if(is_array($value)){
					foreach($value as $subkey => $subvalue){
						if(is_array($subvalue)){
							$subArraysFound = true;
						}
						$array[$key.'_'.$subkey] = $subvalue;
					}
					unset($array[$key]);
				}
			}
			
		} while($subArraysFound);
		
		return $array;
	}
}
