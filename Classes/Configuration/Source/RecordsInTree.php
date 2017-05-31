<?php
namespace Cabag\CabagImport\Configuration\Source;
use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class RecordsInTree extends AbstractAvailableConfiguration {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'select records from a tree in the current typo3 system';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
source = recordsintree
source {
	# name of the record table
	table = tt_content
	
	# starting parent page uid
	pid = 44
	
	# additional where
	addWhere = AND hidden = 1
}';
	}
}