<?php
namespace Cabag\CabagImport\Domain\Repository\Storage;
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
 * TCE storage class for the 'cabag_import' extension.
 *
 * @author	Sonja Scholz <ss@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class Sqlsrv implements StorageInterface {
	// defined keyFields
	var $keyFields;

	// handler object
	var $objectHandler;

	// configuration array
	var $conf;

	/**
	 * The insert cache
	 * @var array
	 */
	protected $insertCache = array();

	/**
	 * The insert cache count
	 * @var integer
	 */
	protected $insertCacheCount = 0;

	/**
	 * The table name where we've last written to
	 * @var string
	 */
	protected $insertCacheTable = '';

	function main($tx_cabagimport_handler, $conf=array()){
		global $LANG;
		// save the handler object
		$this->objectHandler = $tx_cabagimport_handler;

		$this->conf = $conf;

		if(!empty($this->objectHandler->conf['handler.']['keyFields'])){
			$this->keyFields = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->objectHandler->conf['handler.']['keyFields'], 1);
		} else {
			throw new \Exception($LANG->getLL('storage.exception.noKeyFields'));
		}

		if(!empty($this->conf['tablekeys'])){
			$this->tablekeys = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->conf['tablekeys'], 1);
		} else {
			throw new \Exception($LANG->getLL('storage.exception.noTableKeys'));
		}

		// connect separate db if is specified
		if(!empty($this->conf['database'])){
			// check the configuration, that no needed parameter is empty
			if(!empty($this->conf['host']) && !empty($this->conf['user']) && !empty($this->conf['database'])){
				//
			} else {
				throw new \Exception($LANG->getLL('storage.exception.sqlConnectionParameter'));
			}

			$connectionInfo = array("Database"=>$this->conf['database'],'UID'=>$this->conf['user'],'PWD'=>$this->conf['password']);
			//if ($this->debug) error_log("<hr>_connectionID before: ".serialize($this->_connectionID));

			if(!($this->resource = sqlsrv_connect($this->conf['host'],$connectionInfo))) {
				throw new \Exception($LANG->getLL('storage.exception.noConnectionPossible').sqlsrv_errors());
			}
		}
	}

	/**
	* writeRow()
	*
	* - Buid keyFields array for the select query
	* - Search for existing items by the keyFields
	* - Insert or Update the Row
	*
	* @param	array				row with data to write
	* @return	integer/string		uid/exception
	*/
	function writeRow($row=false) {
		global $LANG;

		if($row==false) {
			throw new \Exception($LANG->getLL('storage.exception.emptyRow'));
		}

		// set the pid field
		if(!empty($this->conf['setPid'])){
			$row['pid'] = $this->objectHandler->currentPid;
		}

		if(!empty($this->conf['setTstamp'])) {
			// Do only something, if there is a tstamp field
			$row['tstamp'] = $this->objectHandler->time;
		}

		$keyFieldsQuery = $this->getKeyFieldsQuery($row);

		// Search for existing items by the keyFields
		if(!$this->resource) {
			$dbRes = $GLOBALS['TYPO3_DB']->exec_SELECTQuery(
					'*',
					$this->objectHandler->conf['handler.']['table'],
					$keyFieldsQuery
					);

			$this->existingRecord = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbRes);
		} else {
			$query = $GLOBALS['TYPO3_DB']->SELECTQuery(
					'*',
					$this->objectHandler->conf['handler.']['table'],
					$keyFieldsQuery
					);
			$dbRes = sqlsrv_query($this->resource, $query);
			$this->existingRecord = @sqlsrv_fetch_array($dbRes,SQLSRV_FETCH_ASSOC);;
		}


		// Insert or Update the Row
		if(!empty($this->existingRecord)){
			$this->objectHandler->setMessage($LANG->getLL('storage.status.updateRecord').$keyFieldsQuery);
			if($this->objectHandler->dryMode == false){

				// remove unwanted update fields
				if(!empty($this->conf['dontUpdateFields'])){
					$dontUpdateFields = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->conf['dontUpdateFields']);

					foreach($dontUpdateFields as $dontUpdateField){
						unset($row[$dontUpdateField]);
					}
				}

				$tablekeys = $this->updateRow($row, $this->objectHandler->conf['handler.']['table']);
			} else {
				foreach($this->tablekeys as $key => $value) {
					$tablekeys .= $key .': '.$row[$value];
				}
			}
		} else {
			$this->objectHandler->setMessage($LANG->getLL('storage.status.newRecord').$keyFieldsQuery);
			if($this->objectHandler->dryMode == false){
				$tablekeys = $this->insertRow($row,$this->objectHandler->conf['handler.']['table']);
			}
		}

		return $tablekeys;
	}

	function getKeyFieldsQuery($row){

		// build keyField query
		if(is_array($this->keyFields)){
			$keyFieldsQueryParts = [];
			foreach($this->keyFields as $fieldName){
				$keyFieldsQueryParts[] = $fieldName . ' = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($row[$fieldName],'pages');
			}
			return '(' . implode(' AND ', $keyFieldsQueryParts) . ') ';
		}
		return $this->keyFields . ' = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($row[$this->keyFields], 'pages') . ' ';
		}
	}

	/**
	 * dummy function
	 */
	function setTstamp($table, $uid){
	}

	/**
	* insertRow()
	*
	* - Insert a record in the DB and return the tablekeys of the new record
	*
	* @param	array				row with data to write
	* @return	integer/string		tablekeys/exception
	*/
	function insertRow($record=false, $table=false) {
		global $LANG;

		if($record==false) {
			throw new \Exception($LANG->getLL('storage.exception.emptyRow'));
		}
		if($table==false) {
			throw new \Exception($LANG->getLL('storage.exception.emptyTable'));
		}

		$this->insertCache[] = $record;
		if(false !== $table) {
			$this->insertCacheTable = $table;
		}
		if(!$this->conf['insertThreshold'] || $this->insertCacheCount++ % $this->conf['insertThreshold'] == 0) {

			if(!$this->resource) {
				// Insert the new row to the DB
				$dbRes = $GLOBALS['TYPO3_DB']->exec_INSERTmultipleRows($this->insertCacheTable, $this->insertCache);

				$newID = $GLOBALS['TYPO3_DB']->sql_insert_id();
			} else {
				$query = $GLOBALS['TYPO3_DB']->INSERTmultipleRows($this->insertCacheTable, $this->insertCache);
				$dbRes = sqlsrv_query($this->resource,$query);
				sqlsrv_next_result($dbRes);
				sqlsrv_fetch($dbRes);
				$newID = sqlsrv_get_field($dbRes, 0);
				//$newID = mysql_insert_id($this->resource);
			}
			unset($this->insertCache);
			$this->insertCache = array();
		}

		return $newID;
	}

	/**
	* updateRow()
	*
	* - Update a record in the DB and return the UID of the record
	*
	* @param	array				row with data to write
	* @return	integer/string		uid/exception
	*/
	function updateRow($record=false, $table=false) {
		global $LANG;

		if($record==false) {
			throw new \Exception($LANG->getLL('storage.exception.emptyRow'));
		}
		if($table==false) {
			throw new \Exception($LANG->getLL('storage.exception.emptyTable'));
		}

		$where = '';

		if(is_array($this->tablekeys)){
			$where = '(';
			foreach($this->tablekeys as $tablekey) {
				if(empty($this->existingRecord[$tablekey])) {
					throw new \Exception($LANG->getLL('storage.exception.keyFieldEmpty').'-'.$tablekey);
				}
				$where .= $tablekey."='".mysql_escape_string($this->existingRecord[$tablekey])."' AND ";
			}
			$where = substr($where, 0, -5).') ';
		} else {
			if(empty($record[$this->tablekeys])) {
				throw new \Exception($LANG->getLL('storage.exception.keyFieldEmpty').'-'.$this->tablekeys);
			}
			$where = $this->tablekeys." = '".mysql_escape_string($this->existingRecord[$this->tablekeys])."' ";
		}

		// compose update query (creates just the sql string)
		$query = $GLOBALS['TYPO3_DB']->UPDATEquery($table, $where, $record);

		// debug
		if($GLOBALS['TYPO3_CONF_VARS']['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('sql storage - update query', 'cabag_import', 1, array('query' => $query, 'record' => $record));

		// check if TYPO3 DB or external DB
		if(!$this->resource) {
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, $where, $record);
		} else {
			$dbRes = sqlsrv_query($this->resource,$query);
		}

		// return the first tablekeys of the record
		return $record[$this->tablekeys[0]];
	}

	/**
	* createMMRelation()
	*
	* - Search for an existing mm relation
	* - Create a mm relation if needed
	*
	* @param	array		mm relations
	* @return	integer		uid or 0
	*/
	function createMMRelation($relations, $rowUid=false) {
		// not yet implemented

		return false;
	}

	/**
	 * Set all records with an old tstamp to deleted...
	 */
	function deleteObsolete($table, $addWhere = '') {
		global $LANG;

		if(empty($addWhere)) {
			$addWhere = ' 1=1 '; // since this is MS SQL, this is the way to say "TRUE"
		}

		if($this->objectHandler->dryMode == false && !empty($this->conf['setTstamp'])){
			if(!$this->resource) {$update['deleted'] = 1;
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
						$table,
						'tstamp < '.($this->objectHandler->time-10).'
							AND '.$addWhere,
						$update);

				$this->objectHandler->setMessage(
						$LANG->getLL('storage.deleteObsolete')
						.' - '.$GLOBALS['TYPO3_DB']->sql_affected_rows());
			} else {
				$query = $GLOBALS['TYPO3_DB']->DELETEquery($table, 'tstamp != '.$this->objectHandler->time.'
							AND '.$addWhere);
				$dbRes = sqlsrv_query($this->resource,$query);

				$this->objectHandler->setMessage(
						$LANG->getLL('storage.deleteObsolete')
						.' - '.sqlsrv_affected_rows($this->resource));
			}
		} else {
			$this->objectHandler->setMessage($LANG->getLL('storage.deleteObsolete'));
		}
	}

	/**
	 * When unloading, some things might not have been written yet.
	 */
	public function __destruct() {
		if(!empty($this->insertCache)) {
			if(!$this->resource) {
				// Insert the new row to the DB
				$dbRes = $GLOBALS['TYPO3_DB']->exec_INSERTmultipleRows($this->insertCacheTable, $this->insertCache);

				$newID = $GLOBALS['TYPO3_DB']->sql_insert_id();
			} else {
				$query = $GLOBALS['TYPO3_DB']->INSERTmultipleRows($this->insertCacheTable, $this->insertCache);
				$dbRes = sqlsrv_query($this->resource,$query);
				sqlsrv_next_result($dbRes);
				sqlsrv_fetch($dbRes);
				$newID = sqlsrv_get_field($dbRes, 0);
			}
		}
	}
}
