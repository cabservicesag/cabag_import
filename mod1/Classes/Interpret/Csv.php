<?php
namespace Cabag\CabagImport\Interpret;
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
 * Interpreter class for the 'cabag_import' extension.
 *
 * @author	Sonja Scholz <ss@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class Csv implements InterpretInterface {
	// Resource of the file/db etc
	var $resource;
	
	// interpret configuration part
	var $conf;
	
	// source object
	var $source;
	
	// rows
	var $rows;
	
	/**
	* Constructor
	* 
	* - Throws an exception if no file resource is given, or no conf is given
	* - Checks if the file exists
	* 
	* @param	array		interpreter part of the import configuration
	* @param	resource	file resource of the source file
	*/
	function init($conf=false, $source=false) {
		global $LANG, $TYPO3_CONF_VARS;
		
		$this->resource = $source->resource;
		$this->source = $source;
		
		// Save the interpret configuration part
		if(is_array($conf)) {
			if(!empty($conf['delimiter.']['chr'])){
				$conf['delimiter'] = chr(trim($conf['delimiter.']['chr']));
			}
			
			$this->conf = $conf;
		} else {
			throw new Exception($LANG->getLL('interpret.exception.noConfig'));
		}
		
		if (!empty($conf['dontUsePHPFunction'])) {
			$data = '';
			while (!feof($this->resource)) {
				$data .= fread($this->resource, 8192);
			}
			$this->rows = $this->str_getcsv($data, $conf['delimiter'], trim($this->conf['enclosure']), '\\', '\n');
			if (count($this->rows) == 0) {
				throw new Exception('No rows found in the csv!');
			}
			
			if (!empty($conf['trimFirstRow'])) {
				foreach ($this->rows[0] as &$value) {
					$value = trim($value);
				}
			}
			
			array_unshift($this->rows, array());
			reset($this->rows);
		}
	}
	
	/**
	* getNextRow()
	* 
	* - Get the next row with the file resource
	* - If the row is the first row/consists the key fields save them to the array $key_fields
	* - Returns the array with the current row/the keyfields or throw an exception if there is no row any more
	* 
	* for example: array('SpecialKeyFieldName' => 'Scholz',
	*					 'Prename' => 'Sonja');
	* 
	* @return	array/string	current Row from the file or an exception
	*/
	function getNextRow() {
		global $LANG, $TYPO3_CONF_VARS;
		
		if (!empty($this->conf['dontUsePHPFunction'])) {
			return next($this->rows);
		}
		
		// if there is no enclosure set, the parameter has to be omited
		if(strlen(trim($this->conf['enclosure'])) > 0) {
			while (($arrayLine = fgetcsv($this->resource, 10000, $this->conf['delimiter'], trim($this->conf['enclosure']))) !== FALSE) {
				$imploded = trim(implode($arrayLine));
				if (strlen($imploded) > 1) {
					break;
				}
			}
		} else {
			while (($arrayLine = fgetcsv($this->resource, 10000, $this->conf['delimiter'])) !== FALSE) {
				$imploded = trim(implode($arrayLine));
				if (strlen($imploded) > 1) {
					break;
				}
			}
		}
			
		// return values from array with increased key-ids
		if($arrayLine !== FALSE) {
			foreach($arrayLine as $key => $value){
				if(substr(trim($value), - 1) == '"') {
					$value = str_replace('"', '', $value);
					// if the first characted becomes a "?", this means, it is related to this bug: https://bugs.php.net/bug.php?id=63433
					if(utf8_decode($value[0]) == '?') {
						// so strip the quotes in the beginning
						$value = substr($value, 3);
					}
				}
				
				$arrayLine[$key+1] = $value;
			}
			return $arrayLine;
		} else {
			return false;
		}
	}
	
	/**
	 * str_getcsv alternative
	 *
	 * @see str_getcsv
	 */
	function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = '\n') {
        if (is_string($input) && !empty($input)) {
            $output = array();
            $tmp    = preg_split("/".$eol."/",$input);
            if (is_array($tmp) && !empty($tmp)) {
                while (list($line_num, $line) = each($tmp)) {
                    if (preg_match("/".$escape.$enclosure."/",$line)) {
                        while ($strlen = strlen($line)) {
                            $pos_delimiter       = strpos($line,$delimiter);
                            $pos_enclosure_start = strpos($line,$enclosure);
                            if (
                                is_int($pos_delimiter) && is_int($pos_enclosure_start)
                                && ($pos_enclosure_start < $pos_delimiter)
                                ) {
                                $enclosed_str = substr($line,1);
                                $pos_enclosure_end = strpos($enclosed_str,$enclosure);
                                $enclosed_str = substr($enclosed_str,0,$pos_enclosure_end);
                                $output[$line_num][] = $enclosed_str;
                                $offset = $pos_enclosure_end+3;
                            } else {
                                if (empty($pos_delimiter) && empty($pos_enclosure_start)) {
                                    $output[$line_num][] = substr($line,0);
                                    $offset = strlen($line);
                                } else {
                                    $output[$line_num][] = substr($line,0,$pos_delimiter);
                                    $offset = (
                                                !empty($pos_enclosure_start)
                                                && ($pos_enclosure_start < $pos_delimiter)
                                                )
                                                ?$pos_enclosure_start
                                                :$pos_delimiter+1;
                                }
                            }
                            $line = substr($line,$offset);
                        }
                    } else {
                        $line = preg_split("/".$delimiter."/",$line);
   
                        /*
                         * Validating against pesky extra line breaks creating false rows.
                         */
                        if (is_array($line) && !empty($line[0])) {
                            $output[$line_num] = $line;
                        } 
                    }
                }
                return $output;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
