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

class Files implements AvailableFieldprocInterface {

	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Add a picture to a record';
	}

	/**
	 * Get the configuration example
	 *
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	files_example {
		required = 1

		stack {
			1 = TEXT
			1.value = {$Bildname}

			2 = files
			2 {
				# find the right file in the sourceFolder
				preg_match = /.*{$currentFieldValue}[^\/]*.jpg/

				sourceFolder = fileadmin/user_upload/shop_images/

				# Search recursive from the sourceFolder
				recursive = 1

				# move the founded file to the destination folder
				destinationFolder = uploads/tx_cabagshop/

				# rename the founded file in the destination folder
				rename = {$currentFieldValue}-{$fieldProcFilesNumber}.jpg

				# Clear the currentFieldValue if no image was found
				clearCurrentFieldValueIfNothingFound = 0

				# uses exec with find/grep instead of php functions
				useFindGrep = pattern...

				# custom shell cmd
				useCustomCmd = shell cmd

				# returns imploded filelist
				onlyReturnFoundFiles = 0

				# implode char
				onlyReturnFoundFilesImplodeChr = ,
			}
		}
	}';
	}
}
