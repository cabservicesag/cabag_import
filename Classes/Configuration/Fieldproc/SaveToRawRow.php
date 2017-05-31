<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class SaveToRawRow implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Save a temporary value to a raw row';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	save_to_raw_row_example {
		stack {
			
			1 = TEXT
			1.value = $someField
			
			2 = save_to_raw_row
			2.field = random_field_name
			
			3 = preg_replace
			3.from = /{$random_field_name}/
			3.to = {$someOtherField}
		}
	}';
	}
}