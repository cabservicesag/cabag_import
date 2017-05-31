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

class CachedTransformSelect implements AvailableFieldprocInterface {

	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'sql statement -> first field of the first row will be taken';
	}

	/**
	 * Get the configuration example
	 *
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	cachedtransformselect_example {
		# if set the value has to be != 0 and not empty otherwise the import will be stopped
		required = 1

		stack {
			# selects the table at the first run and reuses the result for transformation!
			1 = cachedtransformselect
			1.sql = SELECT field_from, field_to FROM table WHERE deleted=0
			# the result will be the field_to which is in the same row as the field_from that matches your {$yourfunnyfield}
			1.transform = {$yourfunnyfield}
			# you can define a cache id so you can use the same cache for several fieldprocs
			1.cacheid = funnyid
		}

	}';
	}
}
