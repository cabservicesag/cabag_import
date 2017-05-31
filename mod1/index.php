<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Jonas Dübi / cab services ag <jd@cabag.ch>
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
require_once('conf.php');
require_once($BACK_PATH.'init.php');

// require additional files if older than 6.2
if (version_compare(TYPO3_branch, '6.2', '<')) {
	require_once($BACK_PATH.'template.php');
	include_once(PATH_t3lib.'class.t3lib_tcemain.php');
	require_once(PATH_t3lib.'class.t3lib_scbase.php');
}

$LANG->includeLLFile('EXT:cabag_import/mod1/locallang.xml');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

require_once(t3lib_extMgm::extPath('cabag_import').'lib/class.tx_cabagimport_handler.php');

/**
 * Module 'Import' for the 'cabag_import' extension.
 *
 * @author	Jonas Dübi / cab services ag <jd@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class  tx_cabagimport_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $extKey = 'cabag_import';
	var $extConf; // config from ext template
	var $tsConf; // config from tsconf
	var $id; // selected page
	var $access; // access information

	// uid of the import record
	var $importConfUid;

	// configuration array
	var $importConf;

	/**
	 * Default init function
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		$this->extConf = unserialize($TYPO3_CONF_VARS['EXT']['extConf'][$this->extKey]);

		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$this->access = is_array($this->pageinfo) ? 1 : 0;
		if($this->id && $this->access){
			$this->extConf['importpid'] = $this->id;
			$this->modconfarray = t3lib_BEfunc::getModTSconfig($this->extConf['importpid'], 'mod.cabag_import');
			$this->tsConf = $this->modconfarray;
		}
	}

	/**
	 *
	 * Generates the form to start the import and checks if any file exists...
	 *
	 */
	function moduleContent() {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;


		if(\TYPO3\CMS\Core\Utility\GeneralUtility::_POST('importConfUid')) {
			try {
				// Save the importConfUid for the really import
				$this->importConfUid = \TYPO3\CMS\Core\Utility\GeneralUtility::_POST('importConfUid');

				$this->importConf = tx_cabagimport_handler::getConf($this->importConfUid);

				// set default pid of the handler conf to the current pid if the defaultPid issn't set yet
				if(!empty($this->id) && !isset($this->importConf['handler.']['defaultPid'])){
					$this->importConf['handler.']['defaultPid'] = $this->id;
				}

				if(\TYPO3\CMS\Core\Utility\GeneralUtility::_POST('startImport')) {
					if(!empty($_REQUEST['importFile'])){
						// set the path for the file
						$this->importConf['source.']['filePath'] = $_REQUEST['importFile'];
					}

					// enable DLOG directly in this module if the checkbox is activated
					if(!empty($_REQUEST['activateDLOG'])){
						$TYPO3_CONF_VARS['SYS']['enable_DLOG'] = 1;
					}

					// Instanciate the handler object
					$this->import = new tx_cabagimport_handler($this->importConf);

					// Start the import process
					$this->import->main(false);
					$this->content .= '<strong>'.$LANG->getLL('importRunning').'</strong><br/>';
					$this->content .= implode('<br/>', $this->import->getMessages());
				} else if(\TYPO3\CMS\Core\Utility\GeneralUtility::_POST('dryRun')){
					if(!empty($_FILES['importFile']['tmp_name'])){
						// set the path for the file
						$this->importConf['source.']['filePath'] = PATH_site.'uploads/tx_cabagimport/importFile'.md5($_FILES['importFile']['tmp_name']);

						// Save the import file tmp name to use it later for the really import
						move_uploaded_file($_FILES['importFile']['tmp_name'], $this->importConf['source.']['filePath']);
					}


					if(!empty($_REQUEST['importURL'])){
						$this->importConf['source.']['filePath'] = $_REQUEST['importURL'];
					}

					// enable DLOG directly in this module if the checkbox is activated
					if(!empty($_REQUEST['activateDLOG'])){
						$TYPO3_CONF_VARS['SYS']['enable_DLOG'] = 1;
					}

					// Instanciate the handler object
					$this->import = new tx_cabagimport_handler($this->importConf);

					// Start the import process in dryMode
					$this->import->main();

					$this->showStartForm();

					$this->content .= "<strong>Import Vorschau</strong><br/>";
					$this->content .= implode('<br/>', $this->import->getMessages());
				} else {
					$this->showUploadForm();
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
				$this->showUploadForm();
			}
		} else {
			$this->showUploadForm();
		}
	}

	/**
	* Shows the uploadForm
	*/
	function showUploadForm(){
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		$this->content .= '
			<table>
				<tr>
					<td>
						<label for="importConfig">'.$LANG->getLL('importConfig').':</label>
					</td>
					<td>
						<select id="importConfig" name="importConfUid" size="1">
							'.$this->getConfigSelectOptions().'
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<label for="importFile">'.$LANG->getLL('importFile').':</label>
					</td>
					<td>
						<input type="file" id="importFile" name="importFile" size="50" />
					</td>
				</tr>
				<tr>
					<td>
						<label for="importURL">'.$LANG->getLL('importURL').':</label>
					</td>
					<td>
						<input type="text" id="importURL" name="importURL" size="50" />
					</td>
				</tr>';
				if($BE_USER->user['admin']) {
					$this->content .= '
						<tr>
							<td>
								<label for="importURL">'.$LANG->getLL('activateDLOG').':</label>
							</td>
							<td>
								<input type="checkbox" id="activateDLOG" name="activateDLOG" value="1" />
							</td>
						</tr>';
				}
			$this->content .= '
			</table>
			<input type="submit" name="dryRun" value="'.$LANG->getLL('preview').'" />
			';

			if($BE_USER->user['admin']) {
				$this->content .= '<input type="submit" name="startImport" value="'.$LANG->getLL('startImport').'" />';
			}

			$this->content .= '<br/>';
	}

	/**
	* Shows the uploadForm
	*/
	function showStartForm(){
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		$this->content .= '
			<input type="hidden" name="importFile" value="'.$this->importConf['source.']['filePath'].'">
			<input type="hidden" name="importConfUid" value="'.$this->importConfUid.'" />
			<input type="submit" name="startImport" value="'.$LANG->getLL('startImport').'" />
			<br/>';
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;


			// Draw the header.
			$this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("template");
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="post" enctype="multipart/form-data">';

			// 6.2 compatbility
			if (version_compare(TYPO3_branch, '6.2', '<')) {
				$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br>".$LANG->sL("LLL:EXT:lang/locallang_core.php:labels.path").": ".\TYPO3\CMS\Core\Utility\GeneralUtility::fixed_lgd_pre($this->pageinfo["_thePath"],50);
			} else {
				$headerSection = $this->doc->getHeader("pages",$this->pageinfo,$this->pageinfo["_thePath"])."<br>".$LANG->sL("LLL:EXT:lang/locallang_core.php:labels.path").": ".\TYPO3\CMS\Core\Utility\GeneralUtility::fixed_lgd_cs($this->pageinfo["_thePath"],50);
			}

			$this->content.=$this->doc->startPage($LANG->getLL("title"));
			$this->content.=$this->doc->header($LANG->getLL("title"));
			$this->content.=$this->doc->spacer(5);

			// Render content:
			$this->moduleContent();

			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section("",$this->doc->makeShortcutIcon("id",implode(",",array_keys($this->MOD_MENU)),$this->MCONF["name"]));
			}

			$this->content.=$this->doc->spacer(10);
	}

	/**
	 * Prints out the module HTML
	 */
	function printContent()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	* return mysql result of config select
	* @param	int			uid of the selected config
	* @param	string		where clause
	* @return	resource	mysql resource
	*/
	function getConfig($givenUid=false,$whereAdd=false) {
		$where = 'hidden=0 ';
		if($givenUid) {
			$uid = intval($givenUid);
			$where .= ' AND uid='.$uid;
		}
		if($whereAdd) {
			$where .= $whereAdd;
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_cabagimport_config', $where.t3lib_BEfunc::deleteClause('tx_cabagimport_config'), '', 'title ASC');

		return $res;
	}

	/**
	* return available config options
	* @param	int	uid of the selected config
	*/
	function getConfigSelectOptions($givenUid = FALSE) {
		$options = '';

		if (!$this->extConf['onlyConfigsFromFile']) {
			$res = $this->getConfig();
			while ($config = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				if (!$this->extConf['enablePermissionCheck'] || $GLOBALS['BE_USER']->isInWebMount($config['pid'])) {
					$options .= '<option value="' . $config['uid'] . '"';
					if ($givenUid == $config['uid']) {
						$options .= ' selected ';
					}
					$options .= '>'. $config['title'] .'</option>';
				}
			}
		}

		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['importConfig'])) {
			if (!empty($options)) {
				$options .= '<option disabled="disabled">------</option>';
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
				$options .= '<option value="' . $key . '"';
				if ($givenUid == $key) {
					$options .= ' selected ';
				}
				$options .= '>'. $config['title'] .'</option>';
			}
		}

		return $options;
	}
}


// Make instance:
$SOBE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_cabagimport_module1');
$SOBE->init();

$SOBE->main();
$SOBE->printContent();

?>
