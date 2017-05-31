<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;

/***************************************************************
*  Copyright notice
*
*  (c) 2017 Tizian Schmidlin <st@cabag.ch>
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

use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class Mm implements AvailableFieldprocInterface {

	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Create a mm relation to many records';
	}

	/**
	 * Get the configuration example
	 *
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	1n_relation_example {
		required = 1

		stack {
			1 = TEXT
			1.value = {$Ort}

			# 1 to n relation
			2 = relation
			2 {
				# table of the related records
				relationtable = tx_xyz

				# field to search for and where the value is saved
				# if the searched one is missing
				relationfield = fieldxyz

				# additional condition for searching the relation record
				relationaddwhere = AND sys_language_uid=0

				# pid of the related records
				# (if not set global import pid will be taken)
				relationpid = 208

				# add record if not found
				addIfMissing = 1
			}
		}
	}';
	}
}
