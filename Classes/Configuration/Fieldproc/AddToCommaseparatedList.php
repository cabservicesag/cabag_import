<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class AddToCommaseparatedList implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'can be used to add values to a commaseparated list';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	# can be used for usergroup relation in fe_users if they have to be generated
	add_to_commaseparated_list_example {
		required = 1
				
		stack {
			
			1 = TEXT
			1.value =  {$org1}+{$org2}
			
			3 = add_to_commaseparated_list
			3 {
				# glue between the relations (normaly commaseparated)
				split = +
				
				# add record if not found
				addIfMissing = 1
				
				# table to relate to
				table = fe_groups
								
				# field to relate to and add value if record is missing
				tablekeyfield = title
			}
		}
	}';
	}
}