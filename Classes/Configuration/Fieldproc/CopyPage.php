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

class CopyPage implements AvailableFieldprocInterface {

	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Copy a page to another place';
	}

	/**
	 * Get the configuration example
	 *
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	copypage_example {
		stack {
			1 = select
			1.sql = SELECT uid FROM pages WHERE pid = 1212 AND deleted = 0 AND hidden = 0

			2 = copypage
			2 {
				# destinationpid, otherwise its {$currentFieldValue}
				# destinationpid = 1287

				sourcepid = 345

				# Sets the number of branches on a page tree to copy.
				copyTree = 0

				# clear page cache afterwards
				clearPageCache = 0
			}

			3 = copypage
			3 {
				# destinationpid, otherwise its {$currentFieldValue}
				# destinationpid = 1287

				sourcepid = 51

				# Sets the number of branches on a page tree to copy.
				copyTree = 0

				# clear page cache afterwards
				clearPageCache = 0
			}
		}
	}';
	}
}
