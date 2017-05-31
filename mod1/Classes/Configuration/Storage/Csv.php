<?php
namespace Cabag\CabagImport\Configuration\Storage;
use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class Csv extends AbstractAvailableConfiguration {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Here, no mm fieldproc and no update is possible!';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
storage = csv
storage {
	fields {
		# additional information needed for the fields
		
		# maps a field with a title that has spaces
		writeTitleRow = 1
	
		fields {
			doctorid.title = Doctor ID
			userid.title = User ID
		}
		
		# fills the field with the timestamp of the handler
		tstamp.isTstamp = 1
	}
	
	file {
		# path must exist and be relative to the TYPO3 directory
		path = fileadmin/user_uploads/tx_cabagimport/some_file.csv
		
		# if set to overwrite, the file (if it exists) will be cleared and overwritten
		mode = overwrite
		
		# if set to append, the file will have the rows appended
		mode = append
		
		# if neither overwrite nor append is set and the file exists, a new file will be created like \'some_file_1.csv\'
	}
	
	# csv delimiter between each field
	delimiter = ,
	// delimiter.chr = 9
	
	# enclosure for a field
	enclosure = "
	// enclosure.chr = 32
}';
	}
}