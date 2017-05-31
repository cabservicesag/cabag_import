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

class CleanupCommandController extends \TYPO3\CMS\Extbase\Mvc\Controller\CommandController {

  /**
   * @var string
   */
  protected $uploadsPath = 'uploads/tx_cabagimport';

  /**
   * This deletes all files that are older than X days
   *
   * @param int $x
   * @return void
   */
  public function cleanupCommand($x = 30) {
    $cmd = 'find ' . PATH_SITE . $this->uploadsPath . ' -mtime +' . intval($x) . ' -exec rm {} \;';
    $out = array();
    $return = exec($cmd, $out);
  }
}
