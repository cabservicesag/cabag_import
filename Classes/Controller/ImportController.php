<?php
namespace Cabag\CabagImport\Controller;
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Jonas Dübi / cab services ag <jd@cabag.ch>
*  (c) 2017 Tizian Schmidlin / cab services ag <st@cabag.ch>
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


// turn up max execution time
ini_set('max_execution_time', 60*60*10);
ini_set('max_input_time', 60*60*10);
set_time_limit(60*60*10);

	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
//require_once('conf.php');
//require_once($BACK_PATH.'init.php');

// require additional files if older than 6.2
if (version_compare(TYPO3_branch, '6.2', '<')) {
	require_once($BACK_PATH.'template.php');
	include_once(PATH_t3lib.'class.t3lib_tcemain.php');
	require_once(PATH_t3lib.'class.t3lib_scbase.php');
}

//$LANG->includeLLFile('EXT:cabag_import/mod1/locallang.xml');
//$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

//require_once(t3lib_extMgm::extPath('cabag_import').'lib/class.tx_cabagimport_handler.php');

/**
 * Module 'Import' for the 'cabag_import' extension.
 *
 * @author	Jonas Dübi / cab services ag <jd@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 * @vendor Cabag
 */
class ImportController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	protected $pageinfo;
	protected $extKey = 'cabag_import';
	protected $extConf; // config from ext template
	protected $tsConf; // config from tsconf
	public $id; // selected page
	protected $access; // access information

	// uid of the import record
	protected $importConfUid;

	// configuration array
	protected $importConf;

	/**
	 * Default init function
	 */
	public function initializeAction()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		$this->extConf = unserialize($TYPO3_CONF_VARS['EXT']['extConf'][$this->extKey]);

		$this->pageinfo = \TYPO3\CMS\Backend\Utility\BackendUtility::readPageAccess($this->id,$this->perms_clause);
		$this->access = is_array($this->pageinfo) ? 1 : 0;
		if($this->id && $this->access){
			$this->extConf['importpid'] = $this->id;
			$this->modconfarray = \TYPO3\CMS\Backend\Utility\BackendUtility::getModTSconfig($this->extConf['importpid'], 'mod.cabag_import');
			$this->tsConf = $this->modconfarray;
		}
	}

	/**
	 *
	 * Generates the form to start the import and checks if any file exists...
	 *
	 */
	public function moduleContent() {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		if($this->request->hasArgument('importConfig')) {
			try {
				// Save the importConfUid for the really import
				$this->importConfUid = $this->request->getArgument('importConfig');

				$this->importConf = \Cabag\CabagImport\Handler\ImportHandler::getConf($this->importConfUid);

				// set default pid of the handler conf to the current pid if the defaultPid issn't set yet
				if(!empty($this->id) && !isset($this->importConf['handler.']['defaultPid'])){
					$this->importConf['handler.']['defaultPid'] = $this->id;
				}

				if($this->request->hasArgument('startImport')) {
					//echo 'start import';

					if($this->request->hasArgument('importURL') && !empty($this->request->getArgument('importURL') && !empty($this->request->getArgument('importUrl')['name']))){
						$this->importConf['source.']['filePath'] = $this->request->getArgument('importURL');
					}
					if($this->request->hasArgument('importFile') && !empty($this->request->getArgument('importFile')) && !empty($this->request->getArgument('importFile')['name'])) {
						// set the path for the file
						$this->importConf['source.']['filePath'] = $this->request->getArgument('importFile');
					}

					// enable DLOG directly in this module if the checkbox is activated
					if($this->request->hasArgument('activateDLOG')) {
						$TYPO3_CONF_VARS['SYS']['enable_DLOG'] = 1;
					}

					// Instanciate the handler object
					$this->import = new \Cabag\CabagImport\Handler\ImportHandler($this->importConf);

					// Start the import process
					$this->import->main(false);
					$this->content .= '<strong>'.$LANG->getLL('importRunning').'</strong><br/>';
					$this->content .= implode('<br/>', $this->import->getMessages());
					$this->view->assign('importMessages', $this->content);
					$this->view->assign('importStarted', true);
				} elseif($this->request->hasArgument('dryRun')) {
					//echo 'dry run';
					if(!empty($_FILES['tx_cabagimport_web_cabagimporttxcabagimportm1']['tmp_name']['importFile'])){
					    // set the path for the file
					    $this->importConf['source.']['filePath'] = PATH_site.'uploads/tx_cabagimport/importFile'.md5($_FILES['tx_cabagimport_web_cabagimporttxcabagimportm1']['tmp_name']['importFile']);

					    // Save the import file tmp name to use it later for the really import
					    move_uploaded_file($_FILES['tx_cabagimport_web_cabagimporttxcabagimportm1']['tmp_name']['importFile'], $this->importConf['source.']['filePath']);
					}


					if($this->request->hasArgument('importURL') && !empty($this->request->getArgument('importURL') && !empty($this->request->getArgument('importUrl')['name']))){
						$this->importConf['source.']['filePath'] = $this->request->getArgument('importURL');
					}

					// enable DLOG directly in this module if the checkbox is activated
					if($this->request->hasArgument('activateDLOG')) {
						$TYPO3_CONF_VARS['SYS']['enable_DLOG'] = 1;
					}

					// Instanciate the handler object
					$this->import = new \Cabag\CabagImport\Handler\ImportHandler($this->importConf);

					// Start the import process in dryMode
					$this->import->main();

					$this->showStartForm();

					$this->view->assign('messages', implode('<br/>', $this->import->getMessages()));
				} else {
					// echo 'do notin';
					//$this->showUploadForm();
				}

			} catch (Exception $ex) {
				// invoke loghandler
				if($this->import->loghandler instanceOf tx_cabagimport_iloghandler){
					$this->import->loghandler->setMessage($ex->getMessage(), 0, $ex);
					$this->import->loghandler->finish();
				}

				// show error if exception thrown
				if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('exception', 'cabag_import', 3, (array) $ex);
				$this->content .= '<br/><div style="color:red">'.$ex->getMessage().'</div><br/>';
				//$this->showUploadForm();
			}
		} else {
			//$this->showUploadForm();
		}
	}

	/**
	* Shows the uploadForm
	*/
	public function showStartForm(){
		$this->view->assign('isAdmin', true);
		$this->view->assign('importFile', $this->importConf['source.']['filePath']);
		$this->view->assign('importConfig', $this->importConfUid);
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 */
	public function indexAction()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = \TYPO3\CMS\Backend\Utility\BackendUtility::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

			//$this->content.=$this->doc->startPage($LANG->getLL("title"));
			//$this->content.=$this->doc->header($LANG->getLL("title"));
			//$this->content.=$this->doc->spacer(5);

			// Render content:
			$this->moduleContent();

			//$this->content.=$this->doc->spacer(10);

			$this->view->assign('configSelectOptions', $this->getConfigSelectOptions());
			$this->view->assign('isAdmin',  $BE_USER->user['admin']);
			//echo $this->printContent();
			return;
	}

	/**
	 * Prints out the module HTML
	 */
	public function printContent()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		//$this->content.=$this->doc->endPage();
		return $this->content;
	}

	/**
	* return mysql result of config select
	* @param	int			uid of the selected config
	* @param	string		where clause
	* @return	resource	mysql resource
	*/
	public function getConfig($givenUid=false,$whereAdd=false) {
		$where = 'hidden=0 ';
		if($givenUid) {
			$uid = intval($givenUid);
			$where .= ' AND uid='.$uid;
		}
		if($whereAdd) {
			$where .= $whereAdd;
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_cabagimport_config', $where.\TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('tx_cabagimport_config'), '', 'title ASC');

		return $res;
	}

	/**
	* return available config options
	* @param	int	uid of the selected config
	*/
	public function getConfigSelectOptions($givenUid = FALSE) {
		$options = array();

		if (!$this->extConf['onlyConfigsFromFile']) {
			$res = $this->getConfig();
			while ($config = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				if (!$this->extConf['enablePermissionCheck'] || $GLOBALS['BE_USER']->isInWebMount($config['pid'])) {
					$options[$config['uid']] = $config['title'];
				}
			}
		}

		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['importConfig'])) {
			if (!empty($options)) {

				$options[-1] = '---------------------';
			}

			ksort($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['importConfig']);
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['importConfig'] as $key => $config) {
				if ($config['accessForGroupOnly'] && !$GLOBALS['BE_USER']->isAdmin()) {
					$groups = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $config['accessForGroupOnly'], TRUE);
					$access = FALSE;
					foreach ($groups as $groupId) {
						if ($GLOBALS['BE_USER']->isMemberOfGroup($groupId)) {
							$access = TRUE;
						}
					}
					if (!$access) {
						continue;
					}
				}
				$options[$key] = $config['title'];			}
		}

		return $options;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_import/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cabag_import/mod1/index.php']);
}


/*

// Make instance:
$SOBE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_cabagimport_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();
*/
?>
