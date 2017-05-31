<?php
namespace Cabag\CabagImport\Logger;
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Manuel Bloch <bm@cabag.ch>
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
 * Interpret interface for the 'cabag_import' extension.
 *
 * @author	Manuel Bloch <bm@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
interface LoggerInterface extends \Psr\Log\LoggerInterface {
	/** 
	 * initialize
	 *
	 * @param object handler object
	 * @param array typoscript configuration for this loghandler
	 * @return boolean if logged
	 */
	public function main($source_object, $conf);
	
	/** 
	 * Log string
	 *
	 * @param string message
	 * @param int type -> 0 error, 1 warning, 2 info
	 * @return boolean if logged
	 */
	public function setMessage($message, $type, $data = array());
	
	/** 
	 * get messages 
	 *
	 * @return array with all log messages
	 */
	public function getMessages();
	
	/** 
	 * finish everything
	 *
	 * @return boolean state
	 */
	public function finish();
}

?>
