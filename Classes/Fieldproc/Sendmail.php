<?php
namespace Cabag\CabagImport\Fieldproc;
use Cabag\CabagImport\Exceptions\InvalidConfigurationException;
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
class  Sendmail implements FieldprocInterface {
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
			throw new InvalidConfigurationException($LANG->getLL('fieldProc.exception.noConfig'));
		}
		
		if(empty($this->conf['recipient'])){
			throw new InvalidConfigurationException('No recipient address set for sendmail');
		}
		
		if(empty($this->conf['subject'])){
			throw new InvalidConfigurationException('No subject set for sendmail');
		}
		
		if(empty($this->conf['bodytext'])){
			throw new InvalidConfigurationException('No bodytext set for sendmail');
		}
		
		if(empty($this->conf['from'])){
			throw new InvalidConfigurationException('No from address set for sendmail');
		}
		
		if(!empty($this->conf['sendIfNoResultSQLSelect'])){
			$selectRes = $GLOBALS['TYPO3_DB']->sql_query($this->conf['sendIfNoResultSQLSelect']);
			if($GLOBALS['TYPO3_DB']->sql_num_rows($selectRes) > 0) {
				
				if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) {
					\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Do not send mail to '.$this->conf['recipient'].' because sendIfNoResultSQLSelect returned record', 'cabag_import', -1, $GLOBALS['TYPO3_DB']->sql_fetch_row($selectRes));
				}
				
				// stop sending mail if record found
				return false;
			}
		}
		
		if($object_handler->dryMode == false){
			mail(
				$this->conf['recipient'],
				$this->conf['subject'],
				$this->conf['bodytext'],
				"From: ".$this->conf['from']."\n"
				);
			if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('send mail to '.$this->conf['recipient'], 'cabag_import', -1, $this->conf);
		} else {
			if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('send mail to '.$this->conf['recipient'], 'cabag_import', -1, $this->conf);
		}
		
		return true;
	}
}