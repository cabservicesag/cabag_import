<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class PregMatchKeys implements AvailableFieldprocInterface {
	
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Do something with regular expression matches';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	preg_match_keys_example {
		required = 1
	
		stack {
			2 = preg_match_keys
			2 {
				searchfor = /tx_cabag(.*)/
				# implode string must be between two #\'s
				implodeString = #, #
			}
		}
	}';
	}
}