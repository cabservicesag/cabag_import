<?php
namespace \Cabag\CabagImport\Scheduler\AdditionalFieldProvider\Import;

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

/**
 * This is the base class for all Scheduler tasks
 * It's an abstract class, not designed to be instantiated directly
 * All Scheduler tasks should inherit from this class
 *
 * @author 	Tizian Schmidlin <st@cabag.ch>
 */

class Import implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface {

	/**
	 * additional fields
	 * @var
	 */
	protected $additionalFields = array();
	/**
	 * Constructor for the additional field provider
	 * @return void
	 */
	public function __construct() {
		$this->additionalParameters = array(
			'importUid' => array(
				'type' => 'select',
				'default' => '-1',
				'label' => 'LLL:EXT:cabag_import/Resources/Private/Language/locallang_task.xml:importUid',
			),
			'override' => array(
				'type' => 'text',
				'default' => true,
				'label' => 'LLL:EXT:cabag_import/Resources/Private/Language/locallang_task.xml:override',
			),
		);
	}

	/**
	 * Gets additional fields to render in the form to add/edit a task
	 *
	 * @param array $taskInfo Values of the fields from the add/edit task form
	 * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task The task object being edited. Null when adding a task!
	 * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule Reference to the scheduler backend module
	 * @return array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
	 */
	public function getAdditionalFields(array &$taskInfo, $task, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule) {
		$return = array(
		);

		if ($task != null) {
			$settings = $task->getSettings();
		} else {
			$settings = array();
		}
		$class = get_class($this);
		foreach ($this->additionalParameters as $key => $def) {
			// every class needs their own variables
			$setKey = $key;
			$key = $class . '_' . $key;

			// Initialize field value
			if (empty($taskInfo[$key])) {
				if ($schedulerModule->CMD == 'add') {
						// In case of new task and if field is empty, set default sleep time
					$taskInfo[$key] = $def['default'];
				} else if ($schedulerModule->CMD == 'edit') {
						// In case of edit, set to internal value if no data was submitted already
					$taskInfo[$key] = $settings[$setKey];
				} else {
						// Otherwise set an empty value, as it will not be used anyway
					$taskInfo[$key] = '';
				}
			}

			$entry = array();

			switch ($def['type']) {
				case 'select' :
					$entry = array(
						'code'     => $this->getListOfImportsAsSelect($taskInfo, $key),
						'label'    => $def['label'],
						'cshKey'   => '_MOD_tools_txschedulerM1',
						'cshLabel' => $key
					);
					break;
				case 'text' :
					$entry = array(
						'code'     => '<textarea name="tx_scheduler[' . $key . ']" id="' . $key . '">' . $taskInfo[$key] . '</textarea>',
						'label'    => $def['label'],
						'cshKey'   => '_MOD_tools_txschedulerM1',
						'cshLabel' => $key
					);
					break;
			}

			$return[$key] = $entry;
		}

		return $return;
	}

	protected function getListOfImportsAsSelect($taskInfo, $key) {

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'uid, title',
			'tx_cabagimport_config',
			'deleted = 0 and hidden = 0' // since only admins access scheduler, no further checks have to be made
		);

		$returnSelect = '<select name="tx_scheduler[' . $key . ']" id="' . $key . '"><option value="">None</option>';

		$returnSelect .= '<optgroup label="Database located imports">';
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$returnSelect .= '<option value="' . $row['uid'] . '"' . ($row['uid'] == $taskInfo[$key] ? ' selected="selected"' : '') . '>' . $row['title'] . '</option>';
		}

		$returnSelect .='</optgroup><optgroup label="File based imports">';
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['importConfig'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['importConfig'] as $k => $data) {
				$returnSelect .= '<option value="' . $k . '"' . ($k == $taskInfo[$key] ? ' selected="selected"' : '') . '>' . $data['title'] . '</option>';
			}
		}

		return $returnSelect . '</optgroup></select>';

	}

	/**
	 * Validates the additional fields' values
	 *
	 * @param array $submittedData An array containing the data submitted by the add/edit task form
	 * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule Reference to the scheduler backend module
	 * @return boolean TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule) {

		$class = get_class($this);

		foreach ($this->additionalParameters as $key => $def) {
			$key = $class . '_' . $key;

			switch ($def['type']) {
				case 'int' :
					$submittedData[$key] = intval($submittedData[$key]);
					break;
				case 'string' :
					$submittedData[$key] = empty($submittedData[$key]) ? '' : $submittedData[$key];
					// TODO: add some regex validation
					break;
				case 'boolean' :
					// we accept anything as a boolean
					break;
				case 'select':
					// we accept everything
					$submittedData[$key] = $submittedData[$key];
					break;
				case 'text':
					// we accept all text
					break;
			}
		}
		return true;
	}

	/**
	 * Takes care of saving the additional fields' values in the task's object
	 *
	 * @param array $submittedData An array containing the data submitted by the add/edit task form
	 * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task Reference to the scheduler backend module
	 * @return void
	 */
	public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task) {
		$settings = array();

		$class = get_class($this);
		foreach ($this->additionalParameters as $key => $def) {
			$setKey = $key;
			$key = $class . '_' . $key;

			switch ($def['type']) {
				case 'select' :
					$settings[$setKey] = $submittedData[$key]; // we must accept everything, since this could also be keys to files
					break;
				case 'text':
					$settings[$setKey] = $submittedData[$key];
					break;
			}
		}

		$task->setSettings($settings);
	}
}
