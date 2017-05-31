<?php
namespace Cabag\CabagImport\Configuration;

abstract class AbstractAvailableConfiguration implements AvailableConfigurationInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	abstract public function getDescription();
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	abstract function getConfigurationExample();
	
	/**
	 * Define if an available configuration has child configuration parts to show
	 *
	 * @return boolean
	 */
	public function hasChildConfigurations() {
		return false;
	}
}