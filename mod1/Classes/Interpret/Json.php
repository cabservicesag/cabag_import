<?php

namespace Cabag\CabagImport\Interpret;
/***************************************************************
*  Copyright notice
*
*  (c) 2016 Lavinia Nergu <ln@cabag.ch>
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
 * @author	 Lavinia Negru <ln@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class Json implements InterpretInterface {
	/**
	 * The current file resource
	 *
	 * @var resource
	 */
	public $resource;

	/**
	 * interpret configuration part
	 *
	 * @var array
	 */
	public $conf;

	/**
	 * source object
	 *
	 * @var Cabag\CabagImport\Domain\Repository\Source\SourceInterface
	 */
	protected $source;

	/**
	 * rows
	 *
	 * @var array
	 */
	public $rows;

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

		$this->resource = $source->resource;
		$this->source = $source;

		// Save the interpret configuration part
		if(is_array($conf)) {
			$this->conf = $conf;
		} else {
			throw new Exception($LANG->getLL('interpret.exception.noConfig'));
		}
		
		
		if($this->conf['impoad'] != '') {
			$impoadConf = $this->conf['impoad'];
		} else {
			// default split setting is comma
			$impoadConf =',';
		}
		
		$data = '';
		while (!feof($this->resource)) {
			$data .= fread($this->resource, 8192);
		}
		
		$this->rows = $this->str_getjson($data);

		if (count($this->rows) == 0) {
			throw new \Exception('No rows found in the json!');
		}
		$arrayLines = $this->rows;
		
		foreach ($arrayLines as &$arrayLine) {
		    
		    if(is_array($arrayLine)){
			foreach($arrayLine as $key=>&$value){
			    //if(!in_array($key, $treatAsArray)){
				if(is_array($value)) {
					$value = implode($impoadConf,$value);
				}
			}
		    }
		}
	 
		$this->rows = $arrayLines;
		
		array_unshift($this->rows, array());
		
		reset($this->rows);
		
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
	public function getNextRow() {
		global $LANG, $TYPO3_CONF_VARS;

		return next($this->rows);


//		// if there is no enclosure set, the parameter has to be omited
//		while (!feof($this->resource)) {
//			$data .= fread($this->resource, 8192);
//		}
//		$arrayLines = $this->str_getjson($data);
//
//
//
//		// return values from array with increased key-ids
//		if($arrayLine !== FALSE) {
//			foreach($arrayLine as $key => $value){
//
//				$arrayLine[$key+1] = $value;
//			}
//			return $arrayLine;
//		} else {
//			return false;
//		}
	}

	/**
	 * str_getcsv alternative
	 *
	 * @see str_getcsv
	 */
	protected function str_getjson($input) {
		if (is_string($input) && !empty($input)) {
			$inputJsonDecode = json_decode($input, true);
			reset($inputJsonDecode);
			if(key($inputJsonDecode) == 'JSON'){
			    return array_shift($inputJsonDecode);
			} else {
			    return $inputJsonDecode;
			}
		}

		return false;
	}
}
