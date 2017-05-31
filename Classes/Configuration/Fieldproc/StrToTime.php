<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class StrToTime implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'How to modified a string to be saved as timestamp';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	date_example {
		required = 1
		
		stack {
			1 = TEXT
			1.value = {$Startdatum}
			
			# perl regular expression replacement
			2 = preg_replace
			2 {
				from = (\d\d)\.(\d\d)\.(\d\d\d\d)
				to = $3-$2-$1
			}
			
			# strtotime for the current value
			3 = strtotime
			3.default = 0
			3.timezone =  UTC+0100
		}
	}';
	}
}