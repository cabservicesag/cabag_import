<?php
namespace Cabag\CabagImport\Configuration\Mapping;
use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class Mapping extends AbstractAvailableConfiguration {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Data mapping';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
mapping {';
	}
	
	/**
	 * Define if an available configuration has child configuration parts to show
	 *
	 * @return boolean
	 */
	public function hasChildConfigurations() {
		return true;
	}
}