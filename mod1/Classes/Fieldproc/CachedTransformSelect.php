<?php
namespace Cabag\CabagImport\Fieldproc;
use Cabag\CabagImport\Exceptions\InvalidConfigurationException;
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
 * Configuration:
 * cachedtransformselect_example {
 *		# if set the value has to be != 0 and not empty otherwise the import will be stoped 
 *		required = 1
 *
 *		stack {
 *			# selects the table at the first run and reuses the result for transformation!
 *			1 = cachedtransformselect
 *			1.sql = SELECT field_from, field_to FROM table WHERE deleted=0
 *			# the result will be the field_to which is in the same row as the field_from that matches your {$yourfunnyfield}
 *			1.transform = {$yourfunnyfield}
 *			# you can define a cache id so you can use the same cache for several fieldprocs
 *			1.cacheid = funnyid
 *		}
 *		
 *	}
 *
 *
 * @author	Sonja Scholz <ss@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class  CachedTransformSelect implements FieldprocInterface {
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
		
		if($stackPartConf != false) {
			$this->conf = $stackPartConf;
		} else {
			throw new InvalidConfigurationException($LANG->getLL('fieldProc.exception.noConfig'));
		}
		
		if(empty($this->conf['cacheid'])) {
			throw new InvalidConfigurationException('no cachid set');
		}
		
		if(empty($this->conf['sql'])) {
			throw new InvalidConfigurationException('no sql query set');
		}
		
		if(empty($this->conf['transform'])) {
			throw new InvalidConfigurationException('no transform value set');
		}
		
		
		// if cache table does not exist fetch it
		if(!is_array($object_handler->cachedtransformselectTables[$this->conf['cacheid']])) {
			$cachedtransformselectRes = $GLOBALS['TYPO3_DB']->sql_query($this->conf['sql']);
						
			while($cachedtransformselectRow = $GLOBALS['TYPO3_DB']->sql_fetch_row($cachedtransformselectRes)) {
				$object_handler->cachedtransformselectTables[$this->conf['cacheid']][$cachedtransformselectRow[0]] = $cachedtransformselectRow[1];
			}
		}
		
		// transform by cache table
		if(!empty($object_handler->cachedtransformselectTables[$this->conf['cacheid']][$this->conf['transform']])) {
			$object_handler->currentFieldValue = $object_handler->cachedtransformselectTables[$this->conf['cacheid']][$this->conf['transform']];
		} else {
			$object_handler->currentFieldValue = 0;
		}
		
		return true;
	}
}