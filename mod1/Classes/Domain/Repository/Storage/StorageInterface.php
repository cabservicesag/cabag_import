<?php
namespace Cabag\CabagImport\Domain\Repository\Storage;
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
 * Storage interface for the 'cabag_import' extension.
 *
 * @author	Sonja Scholz <ss@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
interface StorageInterface {
	
	/**
	 * Storage main function
	 *
	 * @param \Cabag\CabagImport\Handler\ImportHandler $tx_cabagimport_handler The current import handler
	 * @param array $conf The configuration array of the storage
	 */
	public function main($tx_cabagimport_handler, $conf = array());
	
	/**
	 * Write the row
	 * 
	 * - Buid keyFields array for the select query
	 * - Search for existing items by the keyFields
	 * - Insert or Update the Row
	 * 
	 * @param	array				row with data to write
	 * @return	integer|string		uid/exception
	 */
	public function writeRow($row=false);
	
	/**
	 * Insert the row to a table
	 * 
	 * - Insert a record in the DB and return the tablekeys of the new record
	 * 
	 * @param	array				row with data to write
	 * @return	integer|string		tablekeys/exception
	 */
	public function insertRow($row=false, $table=false);
	
	/**
	 * updateRow()
	 * 
	 * - Update a record in the DB and return the UID of the record
	 * 
	 * @param	array				row with data to write
	 * @return	integer|string		uid/exception
	 */
	public function updateRow($row=false, $table=false);
	
	/**
	 * Set all records with an old tstamp to deleted...
	 *
	 * @param string $table
	 * @param string $addWhere
	 * @return void
	 */
	public function deleteObsolete($table, $addWhere = '');
	
	/**
	 * set the timestamp for the table at the element with the given uid
	 *
	 * @param string $table The table to update
	 * @param integer $uid The record uid to update
	 */ 
	public function setTstamp($table, $uid);
	
	/**
	 * Create the MM relation
	 * 
	 * - Search for an existing mm relation
	 * - Create a mm relation if needed
	 * 
	 * @param	array		$relations mm relations
	 * @param	integer		$rowUid the row uid
	 * @return	integer		uid or 0
	 */
	public function createMMRelation($relations, $rowUid=false);
}
