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
 * Copy field proccessor class for the 'cabag_import' extension.
 *
 * @author	Sonja Scholz <ss@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class  CopyFile implements FieldprocInterface {
	// contains the configuration
	var $conf;
	
	/**
	* main()
	* 
	* - Proove if the field is empty if the required option isset
	* 
	* @param	array	mapping configuration for the current part includes required option and stack
	* @param	string	object handler
	* @return	array	modificated field data/exception if a required field is empty for example
	*/
	function main($stackPartConf=false, $object_handler) {
		global $LANG,$TYPO3_CONF_VARS;
		
		// check config
		if(is_array($stackPartConf)) {
			$this->conf = $stackPartConf;
		} else {
			throw new Exception($LANG->getLL('fieldProc.exception.noConfig'));
		}
		
		if(!empty($this->conf['sourcepath'])) {
			if(!empty($this->conf['destinationpath'])) {
				
				// split data if enabled
				if(!empty($this->conf['split'])){
					$sourcepathArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode($this->conf['split'],$this->conf['sourcepath'], 1);
					
					if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('splited source', 'cabag_import', -1, $sourcepathArray);
				} else {
					$sourcepathArray[] = $this->conf['sourcepath'];
				}
				
				// set currentFieldValue to zero
				$object_handler->currentFieldValue = '';
				
				// copy the sources
				foreach($sourcepathArray as $key => $this->conf['sourcepath']){
					if(!empty($this->conf['sourcebasepath'])){
						$this->conf['sourcepath'] = $this->conf['sourcebasepath'].$this->conf['sourcepath'];
					} elseif(!empty($this->conf['sourceIsRelPath'])) {
						$this->conf['sourcepath'] = PATH_site . $this->conf['sourcepath'];
					}
					
					if(!empty($this->conf['createFilename'])){
						$filename = $this->conf['createFilename'];
					} else {
						$filename = basename($this->conf['sourcepath']);
					}

					if ($this->conf['trimFilename']) {
						$filename = trim($filename);
					}

					if ($this->conf['skipEmptyFilename']) {
						if (empty($filename)) {
							return TRUE;
						}
					}

					if ($this->conf['allowedFiletypes']) {
						$allowedFiletypesArray = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->conf['allowedFiletypes'], 1);
						if (count($allowedFiletypesArray)) {
							$isAllowed = FALSE;

							foreach($allowedFiletypesArray as $allowedFiletype) {
								if (substr($filename, (strlen($allowedFiletype) * -1)) == $allowedFiletype) {
									$isAllowed = TRUE;
									break;
								}
							}

							if (!$isAllowed && !empty($filename)) {
								if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('not allowed filetype', 'cabag_import', 2, $filename);
								return FALSE;
							}
						}
					}
					
					$filenameAdditionalString = '';
					
					if(empty($this->conf['overwrithe'])){
						$filenameAdditionalNumber = 0;
						while(file_exists(PATH_site.$this->conf['destinationpath'].$filenameAdditionalString.$filename)){
							// Add a number to the filename
							$filenameAdditionalNumber++;
							$filenameAdditionalString = $filenameAdditionalNumber.'-';
							
						}
					}

					$copyto = PATH_site.$this->conf['destinationpath'].$filenameAdditionalString.$filename;
					$filename = $filenameAdditionalString.$filename;
					//$copyto = PATH_site.$this->conf['destinationpath'].$filename;
					
					if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('copy file', 'cabag_import', -1, array($this->conf['sourcepath'], $copyto));
					
					if(!file_exists($this->conf['sourcepath']) && strstr($this->conf['sourcepath'], '://') === false){
						throw new Exception($LANG->getLL('fieldProc.exception.fileNotFound').' - '.$this->conf['sourcepath']);
					}
					
					if($object_handler->dryMode == false){
						// don't download/copy if exists
						if($this->conf['dontCopyIfExists'] && file_exists($copyto)){
							$copyreturn = 1;
						} else {
							// download with php copy or use the typo3 function which supports proxy
							if(strstr($this->conf['sourcepath'], '://') !== false && $this->conf['useGetURL']) {
								$copyreturn = file_put_contents($copyto, \TYPO3\CMS\Core\Utility\GeneralUtility::getURL($this->conf['sourcepath']));
							} else {
								$copyreturn = copy($this->conf['sourcepath'], $copyto);
							}
						}
						// copy and return new path
						if($copyreturn){
							if(filesize($copyto) == 0){
								unlink($copyto);
								return false;
							}
							if($key > 0){
								if ($this->conf['split.']['newline']) {
									//added by fm
									$object_handler->currentFieldValue .= "\r\n";
								} else {
									$object_handler->currentFieldValue .= ",";
								}
							}
							
							$object_handler->setMessage($LANG->getLL('fieldProc.status.copiedFile').$this->conf['sourcepath'].' -> '.$copyto);
							
							if(!empty($this->conf['returnJustFilename'])){
								$object_handler->currentFieldValue .= $filename;
							} else {
								$object_handler->currentFieldValue .= $this->conf['destinationpath'].$filename;
							}
						} else {
							if($TYPO3_CONF_VARS['SYS']['enable_DLOG']) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('file not found', 'cabag_import', 3, array($this->conf['sourcepath'], $copyto));
							if(!$this->conf['doNotThrowNotFound']) {
								throw new Exception($LANG->getLL('fieldProc.exception.fileNotFound').' - '.$this->conf['sourcepath']);
							}
						}
					} else {
						// set value if dry run
						if($key > 0){
							$object_handler->currentFieldValue .= ",";
						}
						
						$object_handler->setMessage($LANG->getLL('fieldProc.status.copiedFile').$this->conf['sourcepath'].' -> '.$copyto);
						
						if(!empty($this->conf['returnJustFilename'])){
							$object_handler->currentFieldValue .= $filename;
						} else {
							$object_handler->currentFieldValue .= $this->conf['destinationpath'].$filename;
						}
					}
				}
			} else {
				throw new Exception($LANG->getLL('fieldProc.exception.noDestinationDefined'));
			}
		} 
		
		return true;
	}
}