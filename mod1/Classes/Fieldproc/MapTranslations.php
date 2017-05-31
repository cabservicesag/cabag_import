<?php
namespace Cabag\CabagImport\Fieldproc;
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
 * Field proccessor class for the 'cabag_import' extension.
 *
 * @author	Jonas Duebi <jd@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class  MapTranslations implements FieldprocInterface {
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
			throw new Exception($LANG->getLL('fieldProc.exception.noConfig'));
		}
		
		// check if flexform fields are defined
		if(count($this->conf['fields.']) < 1) {
			throw new Exception($LANG->getLL('fieldProc.exception.noConfig'));
		}
		
		// convert flex data to array
		$xmlArray = \TYPO3\CMS\Core\Utility\GeneralUtility::xml2array($this->conf['flex']);		
		
		// go trough fields
		foreach($this->conf['fields.'] as $field) {
			// go through data of content field
			if(is_array($xmlArray) && !empty($xmlArray['data']['sDEF']['lDEF'][$field]['vDEF'])){
				
				// uids of current content uids
				$oldUids = $xmlArray['data']['sDEF']['lDEF'][$field]['vDEF'];
				
				// string with new uids
				$newUids = '';
				
				$contentElementUids = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $oldUids, 1);
				
				// go through every current element
				foreach($contentElementUids as $uid) {
					
					// check if element is default language
					$origElementRes = $GLOBALS['TYPO3_DB']->sql_query('SELECT sys_language_uid FROM tt_content WHERE uid = '.intval($uid).' AND deleted = 0');
					$origElementRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($origElementRes);
					if($origElementRow['sys_language_uid'] > 0 || $origElementRow['sys_language_uid'] == -1) {
						// if already a foreign language don't map it
						$newUids .= intval($uid).',';
					} else {
						// if default language map it
						$selectRes = $GLOBALS['TYPO3_DB']->sql_query('SELECT uid FROM tt_content WHERE l18n_parent = '.intval($uid).' AND deleted = 0');
						$selectRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($selectRes);
						if(!empty($selectRow['uid'])) {
							$newUids .= $selectRow['uid'].',';
						}
					}
				}
				
				if(preg_match('/.*,$/', $newUids)) {
					$newUids = substr($newUids, 0, -1);
				}
				
				$this->conf['flex'] = str_replace('>'.$oldUids.'<', '>'.$newUids.'<', $this->conf['flex']);
			} else {
				$object_handler->setMessage('No content in '.$field);
			}
		}
		
		$object_handler->currentFieldValue = $this->conf['flex'];
		
		return true;
	}
}