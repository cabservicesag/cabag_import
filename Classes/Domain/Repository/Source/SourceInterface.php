<?php
namespace Cabag\CabagImport\Domain\Repository\Source;
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
 * Source interface for the 'cabag_import' extension.
 *
 * @author	Sonja Scholz <ss@cabag.ch>
 * @author	Tizian Schmidlin <st@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
interface SourceInterface {
	
	/**
	 * Open the source.
	 *
	 * @param boolean $dryRun	Only run this dry
	 * @param array   $conf		The source configuration
	 * @param \Cabag\CabagImport\Handler\ImportHandler $tx_cabagimport_handler
	 * @return void
	 */
	public function open($dryRun=true, $conf, $tx_cabagimport_handler);
	
	/**
	 * Check the given configuration.
	 * @throws \Cabag\CabagImport\Exceptions\InvalidConfigurationException
	 * @return void
	 */
	public function checkConfiguration();
	
	/**
	 * Get the next row of the source
	 *
	 * @return array|string The next entry in the source
	 */
	public function getNextRow();
	
	/**
	 * Close the source.
	 */
	public function close();
}

?>
