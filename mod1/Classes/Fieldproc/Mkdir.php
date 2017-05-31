<?php
namespace Cabag\CabagImport\Fieldproc;
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Jonas Dübi <jd@cabag.ch>
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
 * @author	Jonas Dübi <jd@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class  Mkdir implements FieldprocInterface {
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
		global $LANG, $TYPO3_CONF_VARS;
		
		if($stackPartConf != false) {
			$this->conf = $stackPartConf;
		} else {
			throw new Exception($LANG->getLL('fieldProc.exception.noConfig'));
		}
		
		if(!empty($this->conf['value'])){
			$object_handler->currentFieldValue = $this->conf['value'];
		}
		
		$path = PATH_site.$object_handler->currentFieldValue;
		
		if(!is_dir($path)){
			if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('mkdir: folder '.$path.' does not exist, will try to create it', 'cabag_import', -1, $path);
			}
			
			if(empty($this->conf['deep'])) {
				if($object_handler->dryMode == false){
					if(\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir($path)) {
						if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) {
							\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('mkdir: folder '.$path.' created', 'cabag_import', -1, $path);
						}
					} else {
						if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) {
							\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('mkdir: could not create folder '.$path, 'cabag_import', -1, $path);
						}
					}
				}
			} else {
				if($object_handler->dryMode == false){
					$error = \TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep(PATH_site, $object_handler->currentFieldValue);
					if(strlen($error) > 0) {
						if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) {
							\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('mkdir: folder '.$path.' created', 'cabag_import', -1, $path);
						}
					} else {
						if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) {
							\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('mkdir: could not create folder '.$path, 'cabag_import', -1, $path);
						}
					}
				}
			}
			if($object_handler->dryMode == false){	
				if(!is_dir($path)){
					throw new Exception($LANG->getLL('fieldProc.exception.couldNotCreateDirectory').$path);
				}
			}
		} else {
			if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('mkdir: folder '.$path.' exists', 'cabag_import', -1, $path);
			}
		}
		
		return true;
	}
}