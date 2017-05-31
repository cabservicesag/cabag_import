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
 * @author Jonas Duebi <jd@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class BinToHex implements FieldprocInterface {
	
	/**
	* Transform the current value form bin to hex
	* 
	* @param	array	mapping configuration for the current part includes required option and stack
	* @param	\Cabag\CabagImport\Handler\ImportHandler	$object_handler The object handler
	* @return	array	modificated field data/exception if a required field is empty for example
	*/
	function main($stackPartConf=false, $object_handler) {
		// bin2hex the current value
		$object_handler->currentFieldValue = bin2hex($object_handler->currentFieldValue);
		return true;
	}
}