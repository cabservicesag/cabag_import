<?php
namespace Cabag\CabagImport\Interpret;
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
 * Interpret interface for the 'cabag_import' extension.
 *
 * @author	Sonja Scholz <ss@cabag.ch>
 * @author	Tizian Schmidlin <st@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
interface InterpretInterface {
	
	/** 
	 * Initialisation function for the interpreter.
	 *
	 * @param array $conf The interpreter configuration
	 * @param \Cabag\CabagImport\Domain\Repositor\Source $source_object
	 * @return mixed
	 */
	public function init($conf=false, $source_object=false);
	
	/**
	 * Get the next interpreted row
	 *
	 * @return array|string
	 */
	public function getNextRow();
}
