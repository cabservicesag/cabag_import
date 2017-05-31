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

class CommaSeparatedLocal implements AvailableFieldprocInterface {

	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'can be used to add new records from comma separated list';
	}

	/**
	 * Get the configuration example
	 *
	 * @return string
	 */
	public function getConfigurationExample() {
		return '

	commaseparated_local_example {
		required = 1

		stack {
			1 = TEXT
			1.value = {$1}+{$2}+{$3}+{$4}

			# m to m relation
			2 = commaseparated_local
			2 {
				# split value for relation
				split1 = ;
				split2 = +


				# table to relate to
				table = fe_groups

				# field to relate to and add value if record is missing
				tablekeyfield = title

				tablekeyfields = title;title_fr;title_it;title_en



				# pid for the relation records
				# (if not set global import pid will be taken)
				tablepid = 157

			}
		}
	}';
	}
}
