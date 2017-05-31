<?php
namespace Cabag\CabagImport\Interpret;
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
 * Interpreter class for the 'cabag_import' extension.
 *
 * @author	Nils Blattner <nb@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class CsvAlternative implements InterpretInterface {
	// Resource of the file/db etc
	var $resource;
	
	// interpret configuration part
	var $conf;
	
	// source object
	var $source;
	
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
		global $LANG;
		
		$this->resource = $source->resource;
		$this->source = $source;
		
		// Save the interpret configuration part
		if(is_array($conf)) {
			if(!empty($conf['delimiter.']['chr'])){
				$conf['delimiter'] = chr(trim($conf['delimiter.']['chr']));
			}
			
			$conf['delimiter'] = $conf['delimiter'] ? $conf['delimiter'] : ',';
			$conf['enclosure'] = preg_quote(trim($this->conf['enclosure']) ? trim($this->conf['enclosure']) : '"');
			
			$this->conf = $conf;
		} else {
			throw new Exception($LANG->getLL('interpret.exception.noConfig'));
		}
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
		if(($stringLine = fgets($this->resource, 10000)) !== FALSE) {
			$arrayLine = array();
			$enclosure = $this->conf['enclosure'];
			$delimiter = $this->conf['delimiter'];
			
			foreach (explode($delimiter, $stringLine) as $i => $value) {
				// remove enclosure characters and unescape them (CSV style -> "" => ")
				$value = preg_replace(
					array(
						'/^' . $enclosure . '/',
						'/' . $enclosure . '$/',
						'/' . $enclosure . $enclosure . '/'
					),
					array(
						'',
						'',
						$enclosure
					),
					$value
				);
				
				$arrayLine[$i + 1] = $value;
			}
			
			return $arrayLine;
		} else {
			return false;
			//throw new Exception($LANG->getLL('interpret.exception.notReadable'));
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_import/lib/class.tx_cabagimport_interpret_csv.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_import/lib/class.tx_cabagimport_interpret_csv.php']);
}
?>
