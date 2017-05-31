<?php
namespace Cabag\CabagImport\Domain\Repository\Source;

/***************************************************************
*  Copyright notice
*
*  (c) 2011 Jonas Duebi <jd@cabag.ch>
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
 * @author	Jonas Duebi <jd@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class  Recordsintree implements SourceInterface {
	// Array with the source part of the import configuration
	var $conf = false;
	
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
		
		$this->checkConfiguration();
		
		// get TCE to get a clean pagetree with permission check
		$this->pidList(intval($this->conf['pid']));
		$this->pidListArray[] = $this->conf['pid'];
		$pidListString = implode(',', $this->pidListArray);
		
		$tx_cabagimport_handler->setMessage('tree:'.$pidListString);
		
		if($this->conf['table'] == 'pages') {
			$this->queryResource = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					'pages',
					'(pid IN ('.$pidListString.') OR uid = '.intval($this->conf['pid']).') AND deleted = 0 '.$this->conf['addWhere']
				);
		} else {
			$this->queryResource = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					$this->conf['table'],
					'pid IN ('.$pidListString.') AND deleted = 0 '.$this->conf['addWhere']
				);
		}
		
		$sqlError = $GLOBALS['TYPO3_DB']->sql_error();
		if($sqlError) {
			throw new Exception($LANG->getLL('source.exception.noQueryResult').$sqlError);
		}
		
		// debug
		if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('selected records from tree', 'cabag_import', -1, array('conf' => $this->conf));
	}
	
	/** 
	* pidList()
	*
	* You won't believe it but there is no existing clean way to get a recursive pidList of a tree.
	* 
	* @param int pid of the parent page
	*/
	function pidList($pid) {
		if ($pid > 0) {
			$mres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'uid',
				'pages',
				'pid=' . intval($pid) . ' AND deleted = 0',
				'',
				'sorting'
			);
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($mres)) {
				$this->pidListArray[] = $row['uid'];
				$this->pidList($row['uid']);
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($mres);
		}
	}
	
	/**
	* checkConfiguration()
	* 
	* @return	bool/string	true for success or an exception
	*/
	function checkConfiguration() {
		global $LANG;
		// check the configuration, that no needed parameter is empty
		if(empty($this->conf['table'])){
			throw new Exception($LANG->getLL('source.exception.recordsintreeNoTable'));
		} 
		
		if(empty($this->conf['pid'])) {
			throw new Exception($LANG->getLL('source.exception.recordsintreeNoPid'));
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
		
		if($resultArray = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($this->queryResource)) {
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
		
		$GLOBALS['TYPO3_DB']->sql_free_result($this->queryResource);
	}
}
