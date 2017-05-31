<?php
namespace Cabag\CabagImport\Command;

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
