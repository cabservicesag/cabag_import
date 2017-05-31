<?php
/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
/**
 * This is the base class for all Scheduler tasks
 * It's an abstract class, not designed to be instantiated directly
 * All Scheduler tasks should inherit from this class
 *
 * @author 	Tizian Schmidlin <st@cabag.ch>
 */
namespace Cabag\CabagImport\Scheduler\Task;

class Import extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

	/**
	 * The task settings
	 * @var array
	 */
	protected $settings;

	/**
	 * The setter for the settings
	 * @param array $settings the settings for the task
	 * @return void
	 */
	public function setSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * The getter for the settings
	 * @return array
	 */
	public function getSettings() {
		return $this->settings;
	}

	/**
	 * This is the main method that is called when a task is executed
	 * It MUST be implemented by all classes inheriting from this one
	 * Note that there is no error handling, errors and failures are expected
	 * to be handled and logged by the client implementations.
	 * Should return TRUE on successful execution, FALSE on error.
	 *
	 * @return boolean Returns TRUE on successful execution, FALSE on error
	 */
	public function execute() {

		if(empty($this->settings['importUid'])) {
			throw new \CabagImport\Exceptions\ImportTaskException(
				'No import has been selected!',
				1411459548
				);
		}

		if(!class_exists('tx_cabagimport_handler')) {
			require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cabag_import') . 'lib/class.tx_cabagimport_handler.php';
		}

		$importConf = \Cabag\CabagImport\Handler\ImportHandler::getConf($this->settings['importUid']);

		if (empty($importConf)){
			throw new \CabagImport\Exceptions\ImportTaskException(
				'No import configuration found!',
				1411459524
			);
		}

		try {
			$cabagImportHandler = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_cabagimport_handler', $importConf);
			$cabagImportHandler->main(false);
		} catch(Exception $e) {
			return false;
		}

		return TRUE;
	}

	/**
	 * This method adds the root pid/page to the title.
	 *
	 * @return	string	Information to display
	 */
	public function getAdditionalInformation() {
		return $this->settings['importUid'];
	}
}
