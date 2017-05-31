<?php
namespace Cabag\CabagImport\Configuration\Handler;
use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class Import extends AbstractAvailableConfiguration {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Define how to handle the request';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
handler {
	# table to import to
	table = tx_xy
	
	# row to start the import from
	// rangeFrom = 0
	
	# row to end the import
	// rangeTo = 1000
	
	# fields to identify records and rows
	keyFields = field_xy
	
	# delete records which are not within the import
	deleteObsolete = 0
	deleteObsolete.addWhere = sys_language_uid = 0 AND type = 0
	
	# define which fields should be updated for deleteObsolete
	updateObsolete = 0
	updateObsolete {
		hidden = 1
	}
	
	# if deleteObsolete deletes more than X records, it does not delete anything instead (only works for TCE storage)
	// deleteObsolete.deleteThreshold = 20
	
	# does the first row contain the labels or data
	firstRowAreKeys = 1
	
	# pid where to place the records
	# if not set the pid selected in the backend module will be used!
	defaultPid = 2
	
	# continue with the next row if a row is invalid
	continueAfterInvalidRow = 0
	
	# import source charset
	in_charset = CP1252
	
	# database charset
	out_charset = UTF-8

	# if set {$currentRowNumber} contains the current number of the row starting from 1
	addCurrentRowNumber = 0
}';
	}
}