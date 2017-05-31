<?php
namespace Cabag\CabagImport\Fieldproc;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Nils Blattner <nb@cabag.ch>
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
 * @author Nils Blattner <nb@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class HtmlEntitiesDecode implements FieldprocInterface, TYPO3\CMS\Core\SingletonInterface {

	protected static $options = array(
		'ENT_COMPAT' => ENT_COMPAT,
		'ENT_QUOTES' => ENT_QUOTES,
		'ENT_NOQUOTES' => ENT_NOQUOTES,
		'ENT_HTML401' => ENT_HTML401,
		'ENT_XML1' => ENT_XML1,
		'ENT_XHTML' => ENT_XHTML,
		'ENT_HTML5' => ENT_HTML5,
	);

	/**
	 * Convert html entities to the respective characters.
	 *
	 * @param	array	mapping configuration for the current part includes required option and stack
	 * @param	string	field with the data
	 * @return	boolean	worked or not
	 */
	function main($conf=false, $object_handler) {
		// init field value
		$conf = is_array($conf) ? $conf : array();
		if(!empty($conf['value'])){
			$object_handler->currentFieldValue = $conf['value'];
		}

		if (!empty($conf['options'])) {
			$options = 0;
			foreach (\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('|', $conf['options']) as $option) {
				if (isset(self::$options[$option])) {
					$options |= self::$options[$option];
				}
			}
		} else {
			$options = ENT_COMPAT | ENT_HTML401;
		}

		$encoding = !empty($conf['encoding']) ? $conf['encoding'] : 'UTF-8';

		// convert entities
		$object_handler->currentFieldValue = html_entity_decode($object_handler->currentFieldValue, $options, $encoding);

		return true;
	}
}
