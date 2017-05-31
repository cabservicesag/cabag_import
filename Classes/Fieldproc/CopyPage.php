<?php
namespace Cabag\CabagImport\Fieldproc;
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Dimitri König <dk@cabag.ch>
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
 * Copy page field proccessor class for the 'cabag_import' extension.
 *
 * @author	Dimitri König <dk@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class CopyPage implements FieldprocInterface {
	// contains the configuration
	var $conf;

	/**
	* main()
	*
	* - Proove if the field is empty if the required option isset
	*
	* @param	array	mapping configuration for the current part includes required option and stack
	* @param	string	object handler
	* @return	array	modificated field data/exception if a required field is empty for example
	*/
	function main($stackPartConf=false, $object_handler) {
		global $LANG,$TYPO3_CONF_VARS;

		// check config
		if(is_array($stackPartConf)) {
			$this->conf = $stackPartConf;
		} else {
			throw new Exception($LANG->getLL('fieldProc.exception.noConfig'));
		}

		// use value or current field value
		if(!empty($this->conf['destinationpid'])){
			$object_handler->currentFieldValue = $this->conf['destinationpid'];
		}

		if(!empty($this->conf['sourcepid'])) {
			$cmd = array();
			$destinationPID = intval($object_handler->currentFieldValue);
			$sourcePID = intval($this->conf['sourcepid']);

			if ($destinationPID && $sourcePID) {
				$cmd['pages'][$sourcePID]['copy'] = $destinationPID;

				if (!empty($this->conf['dontCopyPageIfRecordFound'])) {
					$selectRes = $GLOBALS['TYPO3_DB']->sql_query($this->conf['dontCopyPageIfRecordFound']);
					if($GLOBALS['TYPO3_DB']->sql_num_rows($selectRes) > 0) {

						if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) {
							\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Do not copy page because sendIfNoResultSQLSelect returned record', 'cabag_import', -1, $GLOBALS['TYPO3_DB']->sql_fetch_row($selectRes));
						}

						// stop creating page if record found
						$object_handler->currentFieldValue = 0;
						return FALSE;
					}
				}

				$tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
				$tce->copyTree = intval($this->conf['copyTree']);
				$tce->stripslashes_values = 0;
				$tce->start(array(), $cmd);
				$tce->process_cmdmap();

				if ($this->conf['clearPageCache'] == '1') {
					$tce->clear_cacheCmd('pages');
				}

				if (isset($tce->copyMappingArray_merged['pages'][$sourcePID])) {
					$object_handler->currentFieldValue = $tce->copyMappingArray_merged['pages'][$sourcePID];
				} else {
					$object_handler->currentFieldValue = 0;
				}

				$object_handler->setMessage('Page copied from ' . $sourcePID . ' to ' . $destinationPID . '. New PID is: ' . $object_handler->currentFieldValue);
			} else {
				return FALSE;
			}
		}

		return TRUE;
	}
}
