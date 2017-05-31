<?php
namespace Cabag\CabagImport\Configuration\Source;
use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class File extends AbstractAvailableConfiguration {

	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Simple file source';
	}

	/**
	 * Get the configuration example
	 *
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
source = file
source {
	# in KB
	maxFileSize = 10000
	archivePath = uploads/tx_cabagimport/

	# path to the file (overwrithen by mod1 input)
	// filePath = fileadmin/user_upload/import/alwaysthesamename.csv

	# set to 1 to prevent file source from doing this in php: PATH_site.filePath
	// absoluteFilePath = 0

	# search for file within path (overwrithen by filePath)
	searchPath = fileadmin/user_upload/import
	searchPath {
		preg_match = .*importname.*
	}

	# interpreter which parses the data
	interpret = csv
	interpret {
		# csv options
		delimiter = ,

		# for ascii codes like tab
		// delimiter.chr = 9

		enclosure = "

		# don\'t use the native fgetcsv (stores the whole file in the RAM -> DON\'T USE IF THAT IS AN ISSUE)
		dontUsePHPFunction = 0

		# uses trim() on the values of the first row -> only usable in conjunction with dontUsePHPFunction
		trimFirstRow = 0
	}

	# interpreter which parses the data from json
	interpret = json
	interpret {
		# json options
		usearray = 1

	}

	# executes sed for the importing file before importing it, this example fixes mac line endings
	preImportSedExpression = s/\r/\n/g

	interpret = xml
	interpret {
		recordPath = channel,0,ch,item

		utf8_decode = 1
	}
}';
	}
}
