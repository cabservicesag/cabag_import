<?php
namespace Cabag\CabagImport\Configuration\Storage;
use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class Tce extends AbstractAvailableConfiguration {
	
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'storage which writes the rows';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
storage = tce
storage {
	dontUpdateFields = password
	dontUsePidForKeyField = 0
	
	# only update existing records but dont create new ones
	# dontAllowInserts = 1
	
	# needed for ordering records in TYPO3
	// moveAfterField = myFunnyFieldWithUIDofpreviousRecord
	
	# if set to 1, deleted records will be reactivated when they get imported again
	reactivateDeletedRecords = 0
}';
	}
}