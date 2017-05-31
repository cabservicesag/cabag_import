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
class  Relation implements FieldprocInterface  {
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
	* @param	array		mapping configuration for the current part of the stack
	* @param	object		handler object
	* @return	bool/string	true if success/exception if error
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

		// Create the where clause for the search query
		$relatedRecordWhere = $this->conf['relationfield']." = '".$GLOBALS['TYPO3_DB']->quoteStr($this->objectHandler->currentFieldValue, 'tt_content')."' ";




		// dont add delete=0 to where if the option doNotCheckDeleted=1 set
		if(!empty($this->conf['doNotCheckDeleted'])) {
			// related records could also be on another pid, if its needed
			if(!empty($this->conf['relationpid'])) {
				$relatedRecordWhere .= 'AND pid = '.$this->conf['relationpid'].' ';
			} else {
				$relatedRecordWhere .= 'AND pid = '.$this->objectHandler->currentPid.' ';
			}
		} else {

			// related records could also be on another pid, if its needed
			if(!empty($this->conf['relationpid'])) {
				$relatedRecordWhere .= 'AND deleted=0 AND pid = '.$this->conf['relationpid'].' ';
			} else {
				$relatedRecordWhere .= 'AND deleted=0 AND pid = '.$this->objectHandler->currentPid.' ';
			}

		}



		// add custom part to the where clause
		if(!empty($this->conf['relationaddwhere'])) {
			$relatedRecordWhere .= $this->conf['relationaddwhere'];
		}

		// Search for the related record
		$relatedRecord = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecordRaw($this->conf['relationtable'], $relatedRecordWhere);

		if(!empty($relatedRecord)) {
			$this->objectHandler->setMessage($LANG->getLL('storage.status.newRelation').$this->objectHandler->currentFieldValue);
			$this->objectHandler->currentFieldValue = $relatedRecord['uid'];
		} else {
			if($this->conf['addIfMissing'] > 0) {
				$this->objectHandler->setMessage($LANG->getLL('storage.status.newRelationRecord').$this->objectHandler->currentFieldValue);
				$relationRecord = array($this->conf['relationfield'] => $this->objectHandler->currentFieldValue);

				// related records could also be on another pid, if its needed
				if(!empty($this->conf['relationpid'])) {
					$relationRecord['pid'] = $this->conf['relationpid'];
				} else {
					$relationRecord['pid'] = $this->objectHandler->currentPid;
				}

				$this->objectHandler->currentFieldValue = $this->objectHandler->db->insertRow($relationRecord, $this->conf['relationtable']);
			} else {
				$this->objectHandler->setMessage($LANG->getLL('fieldProc.exception.noRelatedRecord').$this->objectHandler->currentRowNumber.$value);
				// throw new Exception($LANG->getLL('fieldProc.exception.noRelatedRecord').' - '.$this->objectHandler->currentFieldValue);
			}
		}
		return true;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldprocs_relation.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldprocs_relation.php']);
}
?>
