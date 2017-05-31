<?php
namespace Cabag\CabagImport\Configuration;

interface AvailableConfigurationInterface extends \TYPO3\CMS\Core\SingletonInterface{

	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription();

	/**
	 * Get the configuration example
	 *
	 * @return string
	 */
	public function getConfigurationExample();

	/**
	 * Define if an available configuration has child configuration parts to show
	 *
	 * @return boolean
	 */
	public function hasChildConfigurations();
}
