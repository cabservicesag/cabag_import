<?php
namespace Cabag\CabagImport\Fieldproc;
/***************************************************************
*  Copyright notice
*
*  (c) 2016 Tizian Schmidlin <st@cabag.ch>
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
 * @author	2016 Tizian Schmidlin <st@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class  JsonRange implements FieldprocInterface {
  /**
  * main()
  *
  * - Proove if the field is empty if the required option isset
  * - Loop the stack part and call for every part the right function in this class
  *
  * @param	array	mapping configuration for the current part includes required option and stack
  * @param	string	field with the data
  * @return	array	modificated field data/exception if a required field is empty for example
  */
  function main($stackPartConf=false, $object_handler) {
    $from = 0;
    $to = count($object_handler->keyFieldRow);

    if($stackPartConf['from']) {
      $from = $stackPartConf['from'];
    }

    if($stackPartConf['to']) {
      $to = $stackPartConf['to'];
    }
    $tmpArr = array();
    $i = 1;
    foreach($object_handler->currentRowRaw as $field) {
      if($i >= $from && $i <= $to) {
        $tmpArr[$object_handler->keyFieldRow[$i]] = $field;
      }
      $i++;
    }
    // str to time current value
    $object_handler->currentFieldValue = json_encode($tmpArr);

    return true;
  }
}
