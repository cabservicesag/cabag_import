<?php
namespace Cabag\CabagImport\Fieldproc;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Backend\Utility\BackendUtility;
/***************************************************************
*  Copyright notice
*
*  (c) 2017 Lavinia Negru <ln@cabag.ch>
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
 * @author	Lavinia NEGRU <ln@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class AddToCommaSeparatedList implements FieldprocInterface  {
 /**
  * contains the configuration
	* @var array
	*/
	public $conf;

	/**
   * Handler object
	 * @var Cabag\CabagImport\Handler\ImportHandler
	 */
	public $objectHandler;

	/**
	* main()
	*
	* - Proove if the field is empty if the required option isset
	*
	* @param	array		mapping configuration for the current part of stack
	* @param	object		handler object
	* @return	bool/string	true if success/exception
	*/
	public function main($stackPartConf=false, $object_handler) {
		global $LANG;

		$newCurrentFieldValue = '';
		// check if configuration is defined
		if($stackPartConf != false) {
			$this->conf = $stackPartConf;
		} else {
			throw new \Exception($LANG->getLL('fieldProc.exception.noConfig'));
		}

		// save the handler object
		$this->objectHandler = $object_handler;



		// set relationglue to comma if empty
		if(empty($this->conf['relationglue'])) {
			$this->conf['relationglue'] = ',';
		}

		if($this->conf['split'] != '') {
			$splitConf = $this->conf['split'];
		} else {
			// default split setting is comma
			$splitConf =',';
		}

		//$relatedRecordWhere = $this->conf['tablekeyfield']." = '".$this->conf['rowParentIfExists']."' ";

		$currentFieldValues =  GeneralUtility::trimExplode($splitConf,$this->objectHandler->currentFieldValue, 1);

		$parentFieldValues = GeneralUtility::trimExplode(';',$currentFieldValues[0], 1);
		$childFieldValues = GeneralUtility::trimExplode(';',$currentFieldValues[1], 1);
		if(count($parentFieldValues) == count($childFieldValues)) {
		    $parentChildArray = array_combine($parentFieldValues,$childFieldValues);
		} else {
		    $this->objectHandler->currentFieldValue = '';
		}
		if(is_array($parentChildArray)) {
		    foreach($parentChildArray as $parent=>$child) {
			$relatedRecordWhereChild = $this->conf['tablekeyfield']." = '".$child."'";
			$childRecord = BackendUtility::getRecordRaw($this->conf['table'], $relatedRecordWhereChild);
			$relatedRecordWhereParent = $this->conf['tablekeyfield']." = '".$parent."'";
			$parentRecord = BackendUtility::getRecordRaw($this->conf['table'], $relatedRecordWhereParent);
			
			if(!in_array($childRecord['uid'], explode(',',$parentRecord['subgroup']))) {
				$this->newCurrentFieldValueString[$parent] .= $parentRecord['subgroup'].','.$childRecord['uid'];

			}
			$this->newCurrentFieldValueArray[$parent] = explode(',', $this->newCurrentFieldValueString[$parent]);
			$this->newCurrentFieldValueArray[$parent] = array_unique($this->newCurrentFieldValueArray[$parent]);
			$remove = array(0);
			$this->newCurrentFieldValueArray[$parent] = array_diff($this->newCurrentFieldValueArray[$parent], $remove);
			$this->newCurrentFieldValueString[$parent] = implode(',',$this->newCurrentFieldValueArray[$parent]);

			$combinedRecord = array_merge(array('subgroup'=>$this->newCurrentFieldValueString[$parent]),array('title'=>$parent));
			if(!$this->objectHandler->dryMode){
			    $this->objectHandler->db->writeRow($combinedRecord);
			    $this->objectHandler->countOfRecords++;
			}

		    }
		}
		$this->objectHandler->currentFieldValue = '';


		//debug($this->objectHandler->currentFieldValue);
		return true;
	}
}
