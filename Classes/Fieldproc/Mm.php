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
class  Mm implements FieldprocInterface  {
	// contains the configuration
	var $conf;

	// handler object
	var $objectHandler;

	/**
	* main()
	*
	* - Proove if the field is empty if the required option isset
	* - Loop the stack part and call for every part the right function in this class
	*
	* @param	array		mapping configuration for the current part of stack
	* @param	object		handler object
	* @return	bool/string	true if success/exception
	*/
	function main($stackPartConf=false, $object_handler) {

		global $LANG;

		if($stackPartConf != false) {
			$this->conf = $stackPartConf;
		} else {
			throw new Exception($LANG->getLL('fieldProc.exception.noConfig'));
		}

		// save the handler object
		$this->objectHandler = $object_handler;
		
		if (isset($this->conf['split.']['newline']) && $this->conf['split.']['newline']) {
			$this->conf['split'] = "\n";
		}


		// split the value
		if(strlen($this->conf['split']) > 0) {
			$referenceRecords = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode($this->conf['split'],$this->objectHandler->currentFieldValue, 1);
		} else {
			// default split setting is komma
			$referenceRecords = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',',$this->objectHandler->currentFieldValue, 1);
		}


		$finischedReferenceRecords = array();
		// use only special parts of the splited array
		if(!empty($this->conf['splitUseOnlyPosition'])) {
			$finischedReferenceRecords[] = $referenceRecords[$this->conf['splitUseOnlyPosition']-1];
		} else {
			$finischedReferenceRecords = $referenceRecords;
		}

		$mmRelation = array();
		$counter = 0;
		foreach($finischedReferenceRecords as $key => $value) {
			// Create the where clause for the search query
			if(!empty($this->conf['tablekeyfieldlike'])) {
				$relatedRecordWhere = $this->conf['tablekeyfield']." LIKE '" . $GLOBALS['TYPO3_DB']->escapeStrForLike($GLOBALS['TYPO3_DB']->quoteStr($value, $this->conf['table']), $this->conf['table']) . "%'";
			} else {
				$relatedRecordWhere = $this->conf['tablekeyfield']." = '".$value."' ";
			}

			if (empty($this->conf['ignoreTablepid']) && $this->conf['ignoreTablepid']) {
				// related records could also be on another pid, if its needed
				if(is_numeric($this->conf['tablepid']) && intval($this->conf['tablepid']) >= 0) {
					$relatedRecordWhere .= 'AND pid = '.intval($this->conf['tablepid']).' AND deleted=0';
				} else {
					$relatedRecordWhere .= 'AND pid = '.$this->objectHandler->currentPid.' AND deleted=0';
				}
			}

			// Search for the related record
			$relatedRecord = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecordRaw($this->conf['table'], $relatedRecordWhere);
			
			if(!empty($relatedRecord)) {
				$this->objectHandler->setMessage($LANG->getLL('storage.status.newRelation').$value);
				$relatedRecordUid = $relatedRecord['uid'];
			} else {
				if($this->conf['addIfMissing'] > 0) {
					$this->objectHandler->setMessage($LANG->getLL('storage.status.newRelationRecord').$value);
					$relationRecord = array($this->conf['tablekeyfield'] => $value);

					// related records could also be on another pid, if its needed
					if(!empty($this->conf['tablepid'])) {
						$relationRecord['pid'] = $this->conf['tablepid'];
					} else {
						$relationRecord['pid'] = $this->objectHandler->currentPid;
					}
					
					$relatedRecordUid = $this->objectHandler->db->insertRow($relationRecord, $this->conf['table']);
				} else {
					$this->objectHandler->setMessage($LANG->getLL('fieldProc.exception.noRelatedRecord').$this->objectHandler->currentRowNumber.$value);
					continue;
					//throw new Exception($LANG->getLL('fieldProc.exception.noRelatedRecord').$this->objectHandler->currentRowNumber.$value);
				}
			}
			//echo 'UID of the new/selected related record from '.$this->conf['table']. ' :'.$relatedRecordUid;
			// Create a mm array to create the mm records after saving the row to the DB
			$mmRelation[$counter]['mmtable'] = $this->conf['mmtable'];
			$mmRelation[$counter][$this->conf['mmtablefield']] = $relatedRecordUid;
			$mmRelation[$counter]['where'] = $this->conf['mmtablefield'].' = '.$relatedRecordUid;
			$counter ++;

		}
		
		///debug($mmRelation);
		$this->objectHandler->currentFieldValue = array();
		$this->objectHandler->currentFieldValue = $mmRelation;
		return true;
	}
}
