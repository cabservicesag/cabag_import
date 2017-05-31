<?php
namespace Cabag\CabagImport\Domain\Repository\Source;

/***************************************************************
*  Copyright notice
*
*  (c) 2011 Tizian Schmidlin <st@cabag.ch>
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
 * Source class for the 'cabag_import' extension.
 *
 * @author	Tizian Schmidlin <st@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class  Ldap implements SourceInterface {
	// Array with the source part of the import configuration
	public $conf = false;
	
	// handler object
	public $objectHandler = false;
	
	// do no real changes, just check
	public $dryRun = false;
	
	// ldap resource
	public $ldapResource = false;
	
	// ldap search resource
	public $searchResource = false;
	
	// current entry of the ldap search
	public $currentEntry;
	
	/**
	* open()
	* 
	* @param	bool	flag to decide if a dryRun should be done(default) or
	* @param	array	source configuration
	* @param	object	class tx_cabagimport_handler object not
	*/
	function open($dryRun, $conf, $tx_cabagimport_handler) {
		global $LANG, $TYPO3_CONF_VARS;
		
		if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('init ldap source', 'cabag_import', -1, $conf);
		
		// reference to the handler object
		$this->objectHandler = $tx_cabagimport_handler;
		
		// do something or just check
		$this->dryRun = $dryRun;
		
		// set source configuration
		if(is_array($conf)) {
			$this->conf = $conf;
		} else {
			throw new Exception($LANG->getLL('source.exception.noConfig'));
		}
		
		
		// Go through the source configuration and check the source
		$this->checkConfiguration();
		
		// connect server
		if(!$this->ldapResource = ldap_connect($this->conf['server'], $this->conf['port'])) {
			throw new Exception($LANG->getLL('source.exception.ldapConnectionFailed').'server error');
		}
		
		// bind server
		if(!ldap_bind($this->ldapResource, $this->conf['rdn'], $this->conf['password'])) {
			ldap_get_option($this->ldapResource, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error);
			throw new Exception($LANG->getLL('source.exception.ldapConnectionFailed').$extended_error);
		}
		
		// search
		$this->searchResource = ldap_search($this->ldapResource, $this->conf['base_dn'], $this->conf['filter']);
		
		// get the id of the first entry
		$this->currentEntry = ldap_first_entry($this->ldapResource, $this->searchResource);
		
		if(ldap_errno($this->ldapResource) > 0) {
			ldap_get_option($this->ldapResource, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error);
			throw new Exception(ldap_error($this->ldapResource) . ' - '.$extended_error.'('. ldap_errno($this->ldapResource) .') (from line:'.__LINE__.')');
		}
		
		$count = ldap_count_entries($this->ldapResource, $this->searchResource);
		if($count < 1) {
			throw new Exception('no ldap records found');
		} else {
			if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('found serveral entries in ldap search', 'cabag_import', -1, array('count' => $count));
		}
		
		// debug
		if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('connected to ldap server', 'cabag_import', -1, array('conf' => $this->conf));
	}
	
	/**
	* checkConfiguration()
	* 
	* @return	bool/string	true for success or an exception
	*/
	function checkConfiguration() {
		global $LANG, $TYPO3_CONF_VARS;
		
		if(empty($this->conf['server'])){
			throw new Exception($LANG->getLL('source.exception.configValueMissingLDAPServer'));
		}
		
		if(empty($this->conf['port'])){
			throw new Exception($LANG->getLL('source.exception.configValueMissingLDAPPort'));
		}
		
		
		if(empty($this->conf['rdn'])){
			throw new Exception($LANG->getLL('source.exception.configValueMissingLDAPRDN'));
		}
		
		
		if(empty($this->conf['password'])){
			throw new Exception($LANG->getLL('source.exception.configValueMissingLDAPPassword'));
		}
		
		if(empty($this->conf['base_dn'])){
			throw new Exception($LANG->getLL('source.exception.configValueMissingLDAPBaseDN'));
		}
		
		if(empty($this->conf['filter'])){
			throw new Exception($LANG->getLL('source.exception.configValueMissingLDAPFilter'));
		}
		
	}
	
	/**
	* getNextRow()
	* 
	* - Calls the function getNextRow from tx_cabagimport_interpret class
	* - Returns the array with the current row/the keyfields or throw an exception
	* 
	* @param	bool			flag if the first Row is needed
	* @return	array/string	current Row from the file or an exception
	*/
	function getNextRow() {
		global $LANG, $TYPO3_CONF_VARS;
		
		if($this->currentEntry == false) {
			return false;
		}
		
		$row = ldap_get_attributes($this->ldapResource, $this->currentEntry);
        $this->currentEntry = ldap_next_entry($this->ldapResource, $this->currentEntry);
        
		// debug
		if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('ldap return array', 'cabag_import', -1, $row);
        
        $flattened = $this->getFlatArray($row);
        
        return $flattened;
    }
    
    
	/**
	* getFlatArray()
	*
	* - returns a flattened array
	*
	* @param array recursive array
	* @return array flat array
	*/
	function getFlatArray($array){
		
		do {
			$subArraysFound = false;
			
			foreach($array as $key => $value){
				if(is_array($value)){
					foreach($value as $subkey => $subvalue){
						if(is_array($subvalue)){
							$subArraysFound = true;
						}
						if(preg_match('/objectSid|objectGUID/i', $key)) {
							$array[$key.'_'.$subkey] = bin2hex($subvalue);
						}else {
							$array[$key.'_'.$subkey] = $subvalue;
						}
					}
					unset($array[$key]);
				}
			}
			
		} while($subArraysFound);
		
		return $array;
	}
	
	/**
	* close()
	*
	* - closes the source and archives the data if needed
	*
	*/
	function close(){
		global $LANG;
		
		ldap_close($this->ldapResource);
	}
}
