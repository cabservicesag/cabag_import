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
 * @author	Sonja Scholz <ss@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class File implements SourceInterface {
	// Array with the source part of the import configuration
	public $conf = false;

	// File resource
	public $resource = false;

	// handler object
	public $objectHandler = false;

	// do no real changes, just check
	public $dryRun = false;

	// if remote
	public $remoteFile = false;


	/**
	* open()
	*
	* @param	bool	flag to decide if a dryRun should be done(default) or
	* @param	array	source configuration
	* @param	object	class tx_cabagimport_handler object not
	*/
	public function open($dryRun, $conf, $tx_cabagimport_handler) {
	   
		global $LANG, $TYPO3_CONF_VARS;

		if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('init file source', 'cabag_import', -1, $conf);

		// reference to the handler object
		$this->objectHandler = $tx_cabagimport_handler;

		// do something or just check
		$this->dryRun = $dryRun;

		// set source configuration
		if(is_array($conf)) {
			$this->conf = $conf;
		} else {
			throw new Exception($LANG->getLL('source.exception.noConfig'));
		}

		// Go through the source configuration and check the source
		$this->checkConfiguration();

		// Open the file from the file path and saves the file resource to a global var
		if(!($this->resource = fopen($this->conf['filePath'], 'r'))){
			throw new Exception($LANG->getLL('source.exception.noFileExists').' - '.$this->conf['filePath']);
		}

		if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('found file ' . $this->conf['filePath'], 'cabag_import', -1, $conf);

		// Instanciates a new class tx_cabagimport_interpret object and saves the object
		$this->interpret = $tx_cabagimport_handler->interpret;
		$this->interpret->init($this->conf['interpret.'], $this);
	}

	/**
	* checkConfiguration()
	*
	* @return	bool/string	true for success or an exception
	*/
	public function checkConfiguration() {
		global $LANG;
		// try to search for path if filePath is empty
		if(empty($this->conf['filePath']) && !empty($this->conf['searchPath'])){
			$this->conf['filePath'] = $this->searchPath($this->conf['searchPath'], $this->conf['searchPath.']);
		}

		if(preg_match('/[\w]*:\/\/.*/', $this->conf['filePath'])){
			$this->remoteFile = 1;
		} else if(substr($this->conf['filePath'],0,1) != '/'){
			if($this->conf['absoluteFilePath'] == 1) {
				$this->conf['filePath'] = $this->conf['filePath'];
			} else {
				$this->conf['filePath'] = PATH_site.$this->conf['filePath'];
			}
		}

		if($this->conf['checkFileExists'] === NULL || $this->conf['checkFileExists'] == 1){
			if(!file_exists($this->conf['filePath'])){
				throw new Exception($LANG->getLL('source.exception.checkFileExists'));
			}
		}
		if($this->conf['checkFileEmpty'] === NULL || $this->conf['checkFileEmpty'] == 1){
			if(filesize($this->conf['filePath']) === 0){
				throw new Exception($LANG->getLL('source.exception.checkFileEmpty'));
			}
		}


		// set max file size
		if(!empty($this->conf['maxFileSize'])){
			$fileSize = @filesize($this->conf['filePath']);
			if($fileSize > ($this->conf['maxFileSize'] * 1024)){
				throw new Exception($LANG->getLL('source.exception.checkFileSize'));
			}
		}

		// check the archive path
		if(!empty($this->conf['archivePath'])){
			$this->checkArchivePath($this->conf['archivePath']);
		}

		// replace mac newline by unix linefeed
		if(!empty($this->conf['filePath'])) {
			if(!empty($this->conf['preImportSedExpression'])) {
				exec('sed -e \''.$this->conf['preImportSedExpression'].'\' '.$this->conf['filePath'].' > '.$this->conf['filePath'].'tmp');
				rename($this->conf['filePath'].'tmp', $this->conf['filePath']);
			}
		}
	}

	/**
	* searchPath()
	*
	* - searchs for the import file according to the filter options
	*
	* @param		string			path to the directory with the import files
	* @param		array			configuration
	* @return		false/string	filepath
	*/
	public function searchPath($path, $conf){
		global $LANG;
		// Use the path with as absolute path
		$path = PATH_site.$path;

		// add the / to the end of the path if there isn't already a /
		if(substr($path, -1) != '/') {
			$path .= '/';
		}

		if(is_dir($path)){
			$files = scandir($path);
			foreach($files as $key=>$fileName) {
				if(preg_match($conf['preg_match'],$fileName) > 0) {
					return $path.'/'.$fileName;
				}
			}
		}
		return false;
	}

	/**
	* checkArchivePath()
	*
	* - Writes the file to the archivePath
	*
	* @return	bool/string	true for success or an exception
	*/
	public function checkArchivePath($archivePath) {
		global $LANG;

		$this->conf['archivePath'] = preg_replace('/\/$/', '', PATH_site.$archivePath);

		// create archive path if not exists
		if(!is_dir($this->conf['archivePath'])){
			mkdir($this->conf['archivePath']);
			chmod($this->conf['archivePath'], 0777);
		}

		// create .htaccess in archive path to secure it
		if(!file_exists($this->conf['archivePath'].'/.htaccess')) {
			file_put_contents($this->conf['archivePath'].'/.htaccess', 'Deny from all');
		}

		if(!is_dir($this->conf['archivePath'])){
			unset($this->conf['archivePath']);
			throw new Exception($LANG->getLL('source.exception.checkArchivePath').' - '.PATH_site.$archivePath);
		}

		return true;
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
	public function getNextRow() {
		global $LANG;
		return $this->interpret->getNextRow();
	}

	/**
	* close()
	*
	* - closes the source and archives the data if needed
	*
	*/
	public function close(){
		global $LANG;

		// archive and delete source file if not dry run
		if(!empty($this->conf['archivePath']) && $this->dryRun == false){
			if(is_dir($this->conf['archivePath'])){
				copy($this->conf['filePath'], $this->conf['archivePath'].'/'.$this->objectHandler->time.'-importFile');

				if(!$this->remoteFile){
					chmod($this->conf['archivePath'].'/'.$this->objectHandler->time.'-importFile', 0777);
					unlink($this->conf['filePath']);
				}

				// Set message for the customer
				$this->objectHandler->setMessage($LANG->getLL('source.message.archiveFile'));
			}
		}
	}
}
