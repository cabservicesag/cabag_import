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

class Mkdir implements AvailableFieldprocInterface {

	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'creates directory from value or currentFieldValue and returns path as next currentFieldValue';
	}

	/**
	 * Get the configuration example
	 *
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	userhome_mkdir_example {
		stack {
			# creates directory from value or currentFieldValue and returns path as next currentFieldValue
			1 = mkdir
			1 {
				# creates all folders in the path if not existing
				deep = 1

				# path relative to PATH_site
				value = fileadmin/user_upload/users/{$username}
			}
		}
	}';
	}
}
