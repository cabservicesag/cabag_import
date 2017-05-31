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
 * Source class for the 'cabag_import' extension.
 *
 * @author	Jonas Duebi <jd@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class Xls extends File {
	var $remoteFile = false;


	/**
	* function checkConfiguration()
	* converts the xls to a csv file
	*/
	function checkConfiguration() {
		global $TYPO3_CONF_VARS;

		$return = parent::checkConfiguration();

		if (!empty($this->conf['windows.']['xls2csv'])) {
			// convert to csv (windows)

			if (!file_exists($this->conf['filePath']) && file_exists(PATH_site . $this->conf['filePath'])) {
				$this->conf['filePath'] = PATH_site . $this->conf['filePath'];
			}

			$command = '';

			if (!empty($this->conf['windows.']['preCommand'])) {
				$command .= preg_replace('/%file/', $this->conf['filePath'], $this->conf['windows.']['preCommand']) . ' & ';
			}

			$command .= 'cd "' . dirname($this->conf['filePath']) . '" & "' . $this->conf['windows.']['xls2csv'] . '" ' . basename($this->conf['filePath']) . ' 2> shell_error.txt';

			@exec($command, $out);

			//print_r(array($command, $out));
			$newFile = basename($this->conf['filePath']);
			$this->conf['filePath'] = dirname($this->conf['filePath']) . '/' . preg_replace('#\.xls$#i', '', $newFile);
			$this->conf['filePath'] .= '_' . $this->conf['windows.']['table'] . '.csv';

			//print_r(array($this->conf['filePath']));

			$this->conf['interpret.']['delimiter'] = ';';
			unset($this->conf['interpret.']['delimiter.']);
		} else if (!empty($this->conf['windows.']['unixVersionPath'])) {

			// convert to csv (*nix way on windows)
			$command = 'cd "' . $this->conf['windows.']['unixVersionPath'] . '" & xls2csv.exe -c , -q 3 -d UTF-8 "' . $this->conf['filePath'] . '" > "' . $this->conf['filePath'] . '.csv"';

			@exec($command, $out);

			$this->conf['filePath'] = $this->conf['filePath'].'.csv';
		} else {
			// convert to csv (*nix)
			$command = $TYPO3_CONF_VARS['SYS']['binPath'].'xls2csv -c , -q 3 -d UTF-8 '.$this->conf['filePath'].' > '.$this->conf['filePath'].'.csv';

			if (!empty($this->conf['forceCharset'])) {
				$command = 'export LANG=' . escapeshellarg($this->conf['forceCharset']) . '; ' . $command;
			}

			@exec($command, $out);

			$this->conf['filePath'] = $this->conf['filePath'].'.csv';
		}

		// devlog
		if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('convert xls', 'cabag_import', -1, array($command, $out, $this->conf['filePath'], file_exists($this->conf['filePath'])));

		return $return;
	}

	/**
	* getNextRow()
	*
	* - Calls the function getNextRow from tx_cabagimport_interpret class
	* - Returns the array with the current row/the keyfields or throw an exception
	*
	* @param	bool			flag if the first Row is needed
	* @return	array/string	current Row from the file or an exception
	*/
	function getNextRow() {
		global $LANG;
		$arrayLine = $this->interpret->getNextRow();

		// in this source, there is the problem with the last row which is always empty
		$somethingIsNotEmpty = 0;
		// go through the line and check if one of the columns isn't empty
		foreach($arrayLine as $key => $value){
			if(!empty($value)){
				$somethingIsNotEmpty = 1;
			}
		}

		// if there is at least one column filled out return the hole line, false otherwise
		if($somethingIsNotEmpty == 1) {
			return $arrayLine;
		} else {
			return false;
		}
	}
}
