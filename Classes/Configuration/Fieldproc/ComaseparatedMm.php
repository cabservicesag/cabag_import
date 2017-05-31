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

class ComaseparatedMm implements AvailableFieldprocInterface {

	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'can be used for usergroup relation in fe_users if they have to be generated';
	}

	/**
	 * Get the configuration example
	 *
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	# can be used for usergroup relation in fe_users if they have to be generated
	commaseparated_mm_relation_example {
		required = 1

		stack {
			1 = TEXT
			1.value = {$1}

			# m to m relation
			2 = commaseparated_mm
			2 {
				# split value for relation
				split = ,

				# split alternative for newline
				//split.newline = 1

				# possibility to restrict the relation to a position within
				# the value
				//splitUseOnlyPosition = 1

				# glue between the relations (normaly commaseparated)
				relationglue = ,

				# table to relate to
				table = tx_cabagshop_category

				# field to relate to and add value if record is missing
				tablekeyfield = catalogkey

				# set to 1 so search in the tablekeyfield with LIKE %value%
				tablekeyfieldlike = 0

				# pid for the relation records
				# (if not set global import pid will be taken)
				tablepid = 109

				# add record if not found
				addIfMissing = 1
			}
		}
	}';
	}
}
