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
class  PasswordGen implements FieldprocInterface {
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
	function main($stackPartConf, $object_handler) {
		global $LANG;
		
		$this->conf = $stackPartConf;
		
		if(empty($this->conf['length'])){
			$this->conf['length'] = 6;
		}
		
		// if alphanum is not set, just numeric passwords are generated
		if(empty($this->conf['alphanum'])) { 
			$randomPW = "";
			for ($i = 0; $i < $this->conf['length']; $i++) {
				$numberOrChar = rand(1,2);
				if ($numberOrChar) {
					$randomPW .= rand(1,9);
				} else {
					$randomPW .= chr(rand(65,122));
				}
			}
			
			$object_handler->currentFieldValue = $randomPW;
		} else {
			$object_handler->currentFieldValue = $this->alphanumericPassword($this->conf['length']);
		}
		
		return true;
	}
	
	/** 
	 * alphanumericPassword()
	 *
	 * - generates alphanumeric password with length x
	 *
	 * @param int $length of requested password
	 * @return string random aplhanumeric string 
	 */
	function alphanumericPassword($length){
		$password = '';
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		
		for ($i = 0; $i < $length; $i++) {
			$num = mt_rand(0, strlen($chars) - 1);
			$password .= $chars{$num};
		}
		
		return $password;
	}
}