<?php
namespace Cabag\CabagImport\Configuration\Storage;
use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class File extends AbstractAvailableConfiguration {
	
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'File storage to save one file per row';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
storage = file
storage {
	// this is not implemented yet
}
';
	}
}