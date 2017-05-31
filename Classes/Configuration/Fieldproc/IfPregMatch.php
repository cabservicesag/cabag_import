<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class IfPregMatch implements AvailableFieldprocInterface {
	
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
	if_preg_match_example {
		stack {
			1 = TEXT
			1.value = {$textfield}
			
			2 = if_preg_match
			2 {
				pattern = /(cabag)/i
				# optional returns specific match
				returnMatchPosition = 0
			}
		}
	}';
	}
}