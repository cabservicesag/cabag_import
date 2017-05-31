<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class DirectSelection implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'direct value attribution';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	direct_selection = ImportFieledX';
	}
}