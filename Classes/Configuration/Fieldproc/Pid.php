<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class Pid implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'define dynamic pid here or use instead of defaultPid';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	pid {
		stack {
			1 = TEXT
			1.value = {$Field_PID}
		}
	}';
	}
}