<?php
namespace Cabag\CabagImport\Fieldproc;
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
 * Field proccessor class for the 'cabag_import' extension.
 *
 * @author	Sonja Scholz <ss@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class  Files implements FieldprocInterface {
	// contains the configuration
	var $conf;
	var $filesInSource; // contains a list with matching files in source
	
	/**
	* main()
	* 
	* - Proove if the field is empty if the required option isset
	* 
	* @param	array	mapping configuration for the current part includes required option and stack
	* @param	string	object handler
	* @return	array	modificated field data/exception if a required field is empty for example
	*/
	function main($stackPartConf, $object_handler) {
		global $LANG,$TYPO3_CONF_VARS;
		
		// check config
		if(is_array($stackPartConf)) {
			$this->conf = $stackPartConf;
		} else {
			throw new Exception($LANG->getLL('fieldProc.exception.noConfig'));
		}
		
		// call the function which searchs the preg_matches in the sourceFolder
		if (!empty($this->conf['useCustomCmd'])) {
			exec($this->conf['useCustomCmd'], $this->filesInSource);
			$object_handler->setMessage($this->conf['useCustomCmd'] . ' -> ' . implode($this->filesInSource));

			// debug
			if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('search file '.$cmd, 'cabag_import', -1, $this->filesInSource);
		} else if (!empty($this->conf['useFindGrep'])){
			$cmd = 'find '.PATH_site.$this->conf['sourceFolder'].' | grep \''.$this->conf['useFindGrep'].'\' 2>&1';
			exec($cmd, $this->filesInSource);
			
			// debug
			if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('search file '.$cmd, 'cabag_import', -1, $this->filesInSource);
		} else {
			$this->searchRecursive(PATH_site.$this->conf['sourceFolder']);
		}
	
		$fileList = '';
		$number = 0;
		
		// proove if there were found any files in the sourceFolder
		if(is_array($this->filesInSource) && count($this->filesInSource) > 0) {
			if ($this->conf['onlyReturnFoundFiles']) {
				$object_handler->currentFieldValue = implode($this->conf['onlyReturnFoundFilesImplodeChr'], $this->filesInSource);
			} else if(!empty($this->conf['destinationFolder'])) {
				// proove if a destinationFolder was defined
				// use the path as absolute path
				$destinationFolder = PATH_site.$this->conf['destinationFolder'];
				// create the directory if it doesn't exists
				if(!is_dir($destinationFolder)){
					mkdir($destinationFolder);
					chmod($destinationFolder, 0777);
				}
				// check if the directory exists
				if(is_dir($destinationFolder)){
					// check id the rename scheme was defined
					if(!empty($this->conf['rename'])) {
						// loop the matchResult
						foreach($this->filesInSource as $key=>$path) {
							$number ++;
							
							// replace the constant $number in the rename definition
							$newFileName = str_replace('{$fieldProcFilesNumber}', $number, $this->conf['rename']);
							
							// replace {$fieldProcFilesEnding} with the file ending
							$matches = array();
							preg_match('/\.([^\.\/]+)$/', $path, $matches);
							$newFileName = str_replace('{$fieldProcFilesEnding}', $matches[1], $newFileName);
							
							if($object_handler->dryMode == false){
								// move the matched file to the destination folder
								copy($path, $destinationFolder.'/'.$newFileName);
								chmod($destinationFolder.'/'.$newFileName, 0777);
							}
							
							// Add the file name to the files list
							$fileList .= $newFileName.',';
						}
						// remove the last , from the fileList
						if(substr($fileList, -1) == ',') {
							substr_replace($fileList,'', -1, 1);
						}
						// save the fileList as current
						$object_handler->currentFieldValue = $fileList;
					} else {
						throw new Exception($LANG->getLL('fieldProc.exception.noRenameDefined'));
					}
				} else {
					throw new Exception($LANG->getLL('fieldProc.exception.noDestinationExists'));
				}
			} else {
				throw new Exception($LANG->getLL('fieldProc.exception.noDestinationDefined'));
			}
		} else {
			// Clear the currentFieldValue if the additional option isset
			if(!empty($this->conf['clearCurrentFieldValueIfNothingFound'])) {
				$object_handler->currentFieldValue = '';
			}
			
			$object_handler->setMessage($LANG->getLL('fieldProc.exception.noFilesFound').$this->conf['preg_match'].' '.PATH_site.$this->conf['sourceFolder']);
		}
		
		return true;
	}
	
	
	/**
	* scanDir()
	*
	* @param string $path of the directory to scan
	* @return array file list
	*/
	function scanDir($path){
		if(empty($this->object_handler->fieldprocFilesPathCache[$path])){
			$this->object_handler->fieldprocFilesPathCache[$path] = scandir($path);
		}
		return $this->object_handler->fieldprocFilesPathCache[$path];
	}
	
	
	/**
	* searchRecursive()
	* 
	* @param string $path of the directory to scan
	* @return bool always true
	*/
	function searchRecursive($path){
		// add the / to the end of the path if there isn't already a /
		if(substr($path, -1) != '/') {
			$path .= '/';
		}
		
		// go throught files
		if(is_dir($path)){
			$files = $this->scanDir($path);
			
			foreach($files as $key=>$fileName) {
				if(is_file($path.$fileName)) {
					if(preg_match($this->conf['preg_match'], $path.$fileName) > 0) {
						$this->filesInSource[] = $path.$fileName;
					}
				} elseif(is_dir($path.$fileName) && $fileName != '.' && $fileName != '..') {
					$this->searchRecursive($path.$fileName);
				}
			}
		}
		
		return true;
	}
}