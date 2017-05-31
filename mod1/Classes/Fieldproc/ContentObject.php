<?php
namespace Cabag\CabagImport\Fieldproc;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Dimitri König <dk@cabag.ch>
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
 * @author Dimitri König <dk@cabag.ch>
 * @package TYPO3
 * @subpackage tx_cabagimport
 */
class ContentObject implements FieldprocInterface {
	/**
	 * Configuration
	 *
	 * @var array
	 */
	public $conf;

	/**
	 * Object Handler
	 *
	 * @var tx_cabagimport_handler
	 */
	public $objectHandler;

	/**
	 * Main method which returns cobject content
	 *
	 * @param array $conf Configuration
	 * @param tx_cabagimport_handler $objectHandler
	 * @return boolean
	 */
	public function main($conf = FALSE, $objectHandler) {
		if (is_array($conf)) {
			$this->conf = $conf;
		} else {
			throw new Exception($GLOBALS['LANG']->getLL('fieldProc.exception.noConfig'));
		}

		$this->objectHandler = $objectHandler;
		$contentObject = $this->initContentObject();

		$type = $this->conf['config'] ?: 'TEXT';
		$config = $this->conf['config.'];

		$content = $contentObject->cObjGetSingle($type, $config);
		unset($contentObject);

		$this->objectHandler->currentFieldValue = $content;

		return TRUE;
	}

	/**
	 * Return cobj
	 *
	 * @return tslib_cObj
	 */
	public function initContentObject() {
		$this->createFakeFrontEnd();

		$data = $this->objectHandler->currentRowRaw;
		$contentObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tslib_cObj');
		$contentObject->start($data);

		return $contentObject;
	}

	/**
	 * Creates Fake Frontend for cobj
	 *
	 * @return void
	 */
	public function createFakeFrontEnd() {
		if (isset($GLOBALS['TSFE'])) {
			return;
		}

		$pageUid = $this->conf['simulatePid'] ?: $this->objectHandler->currentPid;
		if ($pageUid <= 0) {
			throw new Exception('$pageUid must be > 0');
		}

		$GLOBALS['TT'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\TimeTracker\TimeTracker');
		$GLOBALS['TT']->start();

		$GLOBALS['TSFE'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], $pageUid, 0, 1);
		$GLOBALS['TSFE']->config['config']['language'] = 0;
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->sys_page = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Page\PageRepository');
		$GLOBALS['TSFE']->sys_page->init($GLOBALS['TSFE']->showHiddenPage);
		$GLOBALS['TSFE']->getPageAndRootline();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->forceTemplateParsing = 1;
		$GLOBALS['TSFE']->tmpl->start($GLOBALS['TSFE']->rootLine);
		$GLOBALS['TSFE']->getConfigArray();
	}
}
