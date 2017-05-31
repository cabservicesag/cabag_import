<?php
namespace Cabag\CabagImport\Domain\Repository\Storage;
use TYPO3\CMS\Core\Utility\MathUtility;
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
class Tce implements StorageInterface {
	// defined keyFields
	var $keyFields;

	// handler object
	var $objectHandler;

	// configuration array
	var $conf;

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

		$keyFieldsQuery = $this->getKeyFieldsQuery($row);

		// Search for existing items by the keyFields
		$existingRecord = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecordRaw($this->objectHandler->conf['handler.']['table'], $keyFieldsQuery, 'uid, deleted');

		// UPDATE ROW
		if(!empty($existingRecord)){
			$this->objectHandler->setMessage($LANG->getLL('storage.status.updateRecord').$keyFieldsQuery);
			if($this->objectHandler->dryMode == false){
				// remove unwanted update fields

				if (!empty($existingRecord['deleted']) && !empty($this->conf['reactivateDeletedRecords'])){
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
						$this->objectHandler->conf['handler.']['table'],
						'uid = ' . intval($existingRecord['uid']),
						array('deleted' => 0)
					);
				}

				if(!empty($this->conf['dontUpdateFields'])){
					$dontUpdateFields = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->conf['dontUpdateFields']);

					foreach($dontUpdateFields as $dontUpdateField){
						unset($row[$dontUpdateField]);
					}
				}

				$uid = $this->updateRow($row, $this->objectHandler->conf['handler.']['table'], $existingRecord['uid']);
			} else {
				$uid = $row['uid'];
			}
		// INSERT ROW
		}  else {
			$this->objectHandler->setMessage($LANG->getLL('storage.status.newRecord').$keyFieldsQuery);

			if ($this->conf['dontAllowInserts']) {
				throw new Exception($LANG->getLL('storage.status.unallowedImport') . ' ' . implode(', ', $row));
			} elseif($this->objectHandler->dryMode == false){
				$uid = $this->insertRow($row,$this->objectHandler->conf['handler.']['table']);
			}
		}

		return $uid;
	}

	function getKeyFieldsQuery($row){
		// build keyField query
		if(is_array($this->keyFields)){
			$keyFieldsQuery = '';
			$keyFieldsQueryParts = [];
			foreach($this->keyFields as $fieldName){
			    
				if(MathUtility::canBeInterpretedAsInteger($row[$fieldName])){
					$keyFieldsQueryParts[] = $fieldName.' = '.$GLOBALS['TYPO3_DB']->quoteStr($row[$fieldName], 'pages');
				}else{
					$keyFieldsQueryParts[] = $fieldName.' = '.$GLOBALS['TYPO3_DB']->fullQuoteStr($row[$fieldName], 'pages');
				}
			}
			$keyFieldsQuery = '(' . implode(' AND ', $keyFieldsQueryParts) . ')';
		} else {
			$keyFieldsQuery = $this->keyFields . ' = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($row[$this->keyFields], 'pages') . ' ';
		}

		// in some special cases (negative position pids -> read Core Doc) pid must not be keyfield
		if(empty($this->conf['dontUsePidForKeyField'])){
			$keyFieldsQuery .= ' AND pid='.$this->objectHandler->currentPid;
		}

		if(empty($this->conf['reactivateDeletedRecords'])){
			$keyFieldsQuery .= ' AND deleted=0';
		}

		return $keyFieldsQuery;
	}

	/**
	* insertRow()
	*
	* - Insert a record in the DB and return the UID of the new record
	*
	* @param	array				row with data to write
	* @return	integer/string		uid/exception
	*/
	function insertRow($record=false, $table=false) {
		global $LANG;

		if($record==false) {
			throw new \Exception($LANG->getLL('storage.exception.emptyRow'));
		}
		if($table==false) {
			throw new \Exception($LANG->getLL('storage.exception.emptyTable'));
		}

		if(empty($record['pid'])){
			$record['pid'] = $this->objectHandler->currentPid;
		}

		// if the important typo3 fields aren't set, set them
		$record['tstamp'] = $this->objectHandler->time;

		if(!isset($record['deleted'])){
			$record['deleted'] = 0;
		}

		if(!isset($record['hidden'])){
			$record['hidden'] = 0;
		}

		$md5Key = substr(md5(time().$this->keyFields), 3, 8);


		// identifier for insert (position after, or pid of page)
		if(!empty($this->conf['moveAfterField'])) {
			unset($record[$this->conf['moveAfterField']]);
			$identifier = '-'.$record[$this->conf['moveAfterField']];
		} else {
			$identifier = 'NEW'.$md5Key;
		}

		// Loop the fields of the row and part in the modify array
		foreach($record as $field => $value) {
			if(is_array($value)) {
				$mm = $this->createMMRelation($value);
				if($mm){
					$data[$table][$identifier][$field] = $mm;
				}
			} else {
				$data[$table][$identifier][$field] = $value;
			}
		}

		$tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
		$tce->stripslashes_values = 0;
		$tce->start($data,array());
		$tce->process_datamap();

		$uid = $tce->substNEWwithIDs['NEW'.$md5Key];

		if($uid){
			$this->setTstamp($table, $uid);
		}

		return $uid;
	}

	function setTstamp($table, $uid){
		// if the important typo3 fields aren't set, set them
		$record['tstamp'] = $this->objectHandler->time;
		return $GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, 'uid = '.$uid, $record);
	}

	/**
	* updateRow()
	*
	* - Update a record in the DB and return the UID of the record
	*
	* @param	array				row with data to write
	* @return	integer/string		uid/exception
	*/
	function updateRow($record=false, $table=false, $existingRecordUid=false) {
		global $LANG;


		// identifier for insert (position after, or pid of page)
		if(!empty($this->conf['moveAfterField'])) {

			$cmd[$table][$existingRecordUid]['move'] = '-'.$record[$this->conf['moveAfterField']];
			unset($record[$this->conf['moveAfterField']]);

			// process the modify array by TCE
			$tceMove = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
			$tceMove->stripslashes_values=0;
			$tceMove->start(array(), $cmd);
			$tceMove->process_cmdmap();
		}

		if($record==false) {
			throw new \Exception($LANG->getLL('storage.exception.emptyRow'));
		}
		if($table==false) {
			throw new \Exception($LANG->getLL('storage.exception.emptyTable'));
		}
		if($existingRecordUid==false) {
			throw new \Exception($LANG->getLL('storage.exception.existingRecordUid'));
		}

		if(!isset($record['deleted'])){
			$record['deleted'] = 0;
		}

		if(!isset($record['hidden'])){
			$record['hidden'] = 0;
		}

		// Loop the fields of the row and part in the modify array
		foreach($record as $field => $value) {
			if(is_array($value)) {
				$mm = $this->createMMRelation($value, $existingRecordUid);

				if ($mm === false) {
					$mm = '';
				}

				$data[$table][$existingRecordUid][$field] = $mm;
			} else {
				$data[$table][$existingRecordUid][$field] = $value;
			}
		}

		// process the modify array by TCE
		$tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
		$tce->stripslashes_values=0;
		$tce->start($data,array());
		$tce->process_datamap();

		$this->setTstamp($table, $existingRecordUid);

		return $existingRecordUid;
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
		// Save the number of mm relations to save it in the field 'xyz' of the row
		$numOfRelations = 0;
		$return = false;
		foreach($relations as $relation) {
			// Create the where clause
			if(array_key_exists('uid_local', $relation) && $rowUid) {
				die('foreign to local mm is not yet implemented');

				// insert separately if import row is foreign (tce not possible)
				$where .= $relation['where'].' AND uid_foreign='.$rowUid.' ';

				// Search for existing items
				$existingRecord = array();
				$existingRecord = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecordRaw($relation['mmtable'],$where);

				$insertArray['uid_local'] = $relation['uid_local'];
				$insertArray['uid_foreign'] = $rowUid;

				if(!empty($existingRecord)) {
					$numOfRelations ++;
				} else {
					$insertArray['sorting'] = $numOfRelations;

					$mmInsertQuery = $GLOBALS['TYPO3_DB']->INSERTquery(
						$relation['mmtable'],
						$insertArray
					);

					$GLOBALS['TYPO3_DB']->sql_query($mmInsertQuery);
				}
			} else {
				// insert with tce if import row is local
				$return .= $relation['uid_foreign'].',';
			}
		}

		return $return;
	}


	/**
	 * Set all records with an old tstamp to deleted...
	 *
	 * @param string $table
	 * @param string $addWhere
	 * @return void
	 */
	function deleteObsolete($table, $addWhere = '', $updateValues = array()) {
		$pidWhere = $this->objectHandler->conf['handler.']['deleteObsolete.']['ignorePid'] ? '' : ' AND pid= ' . intval($this->objectHandler->currentPid);
		$addWhere = empty($addWhere) ? '' : ' AND ' . $addWhere;
		$whereClause = 'tstamp < ' . ($this->objectHandler->time-10) . $pidWhere . $addWhere;

		$deletedRecords = 0;
		if ($this->objectHandler->dryMode == FALSE) {
			$deleteThreshold = intval($this->objectHandler->conf['handler.']['deleteObsolete.']['deleteThreshold']);
			if ($deleteThreshold > 0) {
				$numberOfDeleteRecords = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
					'COUNT(*) as c',
					$table,
					$whereClause
				);
				$deletedRecords = $numberOfDeleteRecords['c'];

				if ($deletedRecords > $deleteThreshold) {
					// the update query would delete more records than allowed

					throw new \Exception($GLOBALS['LANG']->getLL('storage.deleteObsoleteOverThreshold') . ' - ' . $deletedRecords . ' > ' . $deleteThreshold);
				}
			}

			// set updateValues to deleted=1 if there are non provided
			if(count($updateValues) == 0) {
				$updateValues = array('deleted' => 1);
			}

			$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = TRUE;
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				$table,
				$whereClause,
				$updateValues
			);

			$this->objectHandler->setMessage($GLOBALS['TYPO3_DB']->debug_lastBuiltQuery);
			$deletedRecords = $GLOBALS['TYPO3_DB']->sql_affected_rows();
		} else {
			$numberOfDeleteRecords = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
				'COUNT(*) as c',
				$table,
				$whereClause
			);
			$deletedRecords = $numberOfDeleteRecords['c'];
		}

		$this->objectHandler->setMessage($GLOBALS['LANG']->getLL('storage.deleteObsolete') . ' - ' . $deletedRecords);
	}

	/**
	 * update deleted
	 *
	 * @param array $conf
	 * @return void
	 */
	public function updateObsolete($table, $addWhere = '', $conf) {
		// use deleteObsolete
		$this->deleteObsolete($table, $addWhere, $conf);
	}
}
