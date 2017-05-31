<?php
namespace Cabag\CabagImport\Domain\Repository\Source;

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
 * Source class for the 'cabag_import' extension.
 *
 * @author	Sonja Scholz <ss@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class  Mysql implements SourceInterface {
	// Array with the source part of the import configuration
	var $conf = false;
	
	// SQL resource
	var $resource = false;
	
	// SQL Query
	var $queryResource = false;
	
	// handler object
	var $objectHandler = false;
	
	// do no real changes, just check
	var $dryRun = false;
	
	
	/**
	* open()
	* 
	* @param	bool	flag to decide if a dryRun should be done(default) or
	* @param	array	source configuration
	* @param	object	class tx_cabagimport_handler object not
	*/
	function open($dryRun, $conf, $tx_cabagimport_handler) {
		global $LANG, $TYPO3_CONF_VARS;
		
		if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('init sql source', 'cabag_import', -1, $conf);
		
		// reference to the handler object
		$this->objectHandler = $tx_cabagimport_handler;
		
		// do something or just check
		$this->dryRun = $dryRun;
		
		// set source configuration
		if(is_array($conf)) {
			$this->conf = $conf;
		} else {
			throw new Exception($LANG->getLL('source.exception.noConfig'));
		}
		
		// connect separate db if is specified
		if(!empty($this->conf['database'])){
		
			// Go through the source configuration and check the source
			$this->checkConfiguration();
			
			if($this->resource = mysql_connect($this->conf['host'],$this->conf['user'],$this->conf['password'],true)) {
				if(mysql_select_db($this->conf['database'],$this->resource)) {
					mysql_query('SET SESSION group_concat_max_len = 1000000',$this->resource);
					$this->queryResource = mysql_query($this->conf['query'],$this->resource);
					if(!$this->queryResource) {
						throw new Exception($LANG->getLL('source.exception.noQueryResult').mysql_error());
					}
				} else {
					throw new Exception($LANG->getLL('source.exception.noDBselected').mysql_error());
				}
			} else {
				throw new Exception($LANG->getLL('source.exception.noConnectionPossible').mysql_error());
			}
		} else {
			// use regular typo3 db
			mysql_query('SET SESSION group_concat_max_len = 1000000', $GLOBALS['TYPO3_DB']->link);
			$this->queryResource = mysql_query($this->conf['query'], $GLOBALS['TYPO3_DB']->link);
			if(!$this->queryResource) {
				throw new Exception($LANG->getLL('source.exception.noQueryResult').mysql_error());
			}
		}
		
		// debug
		if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('connected to mysql host/db', 'cabag_import', -1, array('conf' => $this->conf));
	}
	
	/**
	* checkConfiguration()
	* 
	* @return	bool/string	true for success or an exception
	*/
	function checkConfiguration() {
		global $LANG;
		// check the configuration, that no needed parameter is empty
		if(!empty($this->conf['host']) && !empty($this->conf['user']) && !empty($this->conf['database'])){
			if(!empty($this->conf['query'])) {
				return true;
			} else {
				throw new Exception($LANG->getLL('source.exception.sqlQuery'));
			}
		} else {
			throw new Exception($LANG->getLL('source.exception.sqlConnectionParameter'));
		}
	}
	
	/**
	* getNextRow()
	* 
	* - Calls the function getNextRow from tx_cabagimport_interpret class
	* - Returns the array with the current row/the keyfields or throw an exception
	* 
	* @param	bool			flag if the first Row is needed
	* @return	array/string	current Row from the file or an exception
	*/
	function getNextRow() {
		global $LANG;
		
		if($resultArray = mysql_fetch_assoc($this->queryResource)) {
			return $resultArray;
		} else {
			return false;
		}
	}
	
	/**
	* close()
	*
	* - closes the source and archives the data if needed
	*
	*/
	function close(){
		global $LANG;
		
		// Close the sql connection
		if(!empty($this->conf['database'])){
			if(mysql_close($this->resource)) {
				return true;
			} else {
				throw new Exception($LANG->getLL('source.exception.sqlClose'));
			}
		} else {
			return true;
		}
	}
}
