<?php

namespace Cabag\CabagImport\Fieldproc;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Backend\Utility\BackendUtility;

/* * *************************************************************
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
 * ************************************************************* */

/**
 * Field proccessor class for the 'cabag_import' extension.
 *
 * @author	Lavinia Negru <ln@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class CommaSeparatedLocal implements FieldprocInterface {

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
     * - Loop the stack part and call for every part the right function in this class
     *
     * @param	array		mapping configuration for the current part of stack
     * @param	object		handler object
     * @return	bool/string	true if success/exception
     */
    public function main($stackPartConf = false, $object_handler) {
	global $LANG;

	// check if configuration is defined
	if ($stackPartConf != false) {
	    $this->conf = $stackPartConf;
	} else {
	    throw new \Exception($LANG->getLL('fieldProc.exception.noConfig'));
	}

	// save the handler object
	$this->objectHandler = $object_handler;

	// split by newline
	if (isset($this->conf['split.']['newline']) && $this->conf['split.']['newline']) {
	    $this->conf['split'] = "\n";
	}

	// split the value
	if ($this->conf['split2'] != '') {
	    $recordValue = GeneralUtility::trimExplode($this->conf['split2'], $this->objectHandler->currentFieldValue, 1);
	} else {
	    // default split setting is comma
	    $recordValue = GeneralUtility::trimExplode(',', $this->objectHandler->currentFieldValue, 1);
	}

	// split the value
	if ($this->conf['split1'] != '') {
	    $recordKey = GeneralUtility::trimExplode($this->conf['split1'], $this->conf['tablekeyfields'], 1);
	} else {
	    // default split setting is comma
	    $recordKey = GeneralUtility::trimExplode(',', $this->conf['tablekeyfields'], 1);
	}


	// Search for the related record
	$relatedRecord = BackendUtility::getRecordRaw($this->conf['table'], $relatedRecordWhere);
	if (!empty($recordValue) && !empty($recordKey)) {

	    if (count($recordKey) == count($recordValue)) {
		$combinedRecordsComma = array_combine($recordKey, $recordValue);
		if (is_array($combinedRecordsComma)) {
		    //$mmRelation = array();
		    foreach ($combinedRecordsComma as $key => &$values) {
			if (strlen($this->conf['split1']) != '') {
			    $values = GeneralUtility::trimExplode($this->conf['split1'], $values, 1);
			} else {
			    // default split setting is comma
			    $values = GeneralUtility::trimExplode(',', $values, 1);
			}
		    }

		    foreach ($combinedRecordsComma as $key => $vals) {
			$i = 0;
			foreach ($vals as $value) {
			    $combinedRecords[$i][$key] = $value;
			    $i++;
			}
		    }

		    if ($combinedRecords) {
			foreach ($combinedRecords as $combinedRecord) {
			    if (!empty($this->conf['tablekeyfield'])) {
				$recordWhere = $this->conf['tablekeyfield'] . " = '" . $combinedRecord[$this->conf['tablekeyfield']] . "'";
			    }

			    $relatedRecord = BackendUtility::getRecordRaw($this->conf['table'], $recordWhere);


			    if (!$this->objectHandler->dryMode) {
				$this->objectHandler->db->writeRow($combinedRecord);
				$this->objectHandler->countOfRecords++;
			    }
			}
		    }
		}
	    }
	}
	$this->objectHandler->currentFieldValue = '';
	return true;
    }

}
