<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class Select implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'sql statement -> first field of the first row will be taken';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	select_example {
		# if set the value has to be != 0 and not empty otherwise the import will be stoped 
		required = 1

		stack {
			# sql statement -> first field of the first row will be taken
			1 = select
			1.sql = SELECT field_x FROM table WHERE field_y = \'{$ImportFieldX}\'
		}
		
	}';
	}
}