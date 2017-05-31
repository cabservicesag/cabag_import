<?php
namespace Cabag\CabagImport\Configuration\Source;
use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class Xls extends AbstractAvailableConfiguration {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'converts xls to csv';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
source = xls
source {
	# in KB
	maxFileSize = 10000 
	archivePath = uploads/tx_cabagimport/
	
	# force charset if you have problems with umlaute
	forceCharset = en_US.UTF-8
	
	# interpreter which parses the data
	interpret = csv
	interpret {
		# csv options
		delimiter = ,
		
		enclosure = "
	}
}';
	}
}