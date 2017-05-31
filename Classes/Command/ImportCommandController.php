<?php
namespace Cabag\CabagImport\Command;

/***************************************************************
*  Copyright notice
*
*  (c) 2017 Tizian Schmidlin <st@cabag.ch>
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

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImportCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController {

	/**
	 * Execute one or more imports.
	 *
	 * @param string $importUid Comma Separated List of UIDs (Config Names if strored in an Extension )
	 * @param boolean $dryRun Dry run
	 * @param string $memoryLimit The memory limit to use for this call
	 * @return void
	 */
	public function importCommand($importUid, $dryRun = false, $memoryLimit = '512M') {

		ini_set('memory_limit', $memoryLimit);
		$importUids= GeneralUtility::trimExplode(',',$importUid);
		if(is_array($importUids)){
		    foreach($importUids as $importUid){
			// get config
			$importConf = \Cabag\CabagImport\Handler\ImportHandler::getConf($importUid);
			//$importConf = \tx_cabagimport_handler::getConf($importUid);

			if (!$importConf){
				exit('1 import configuration not found');
			}

			// gets the tx_cabagimport_handler object
			//$import = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_cabagimport_handler', $importConf);
			$import = new \Cabag\CabagImport\Handler\ImportHandler($importConf);

			// Start the import process
			$import->main($dryRun);

			//echo '<br/><hr/><br/><h1>'.$LANG->getLL('importRunning').'</h1><br/>';
			echo implode('<br/>', $import->getMessages());
		    }
		}
	}
}
