<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class CommaSeparatedLocal implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'can be used to add new records from comma separated list';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '

	commaseparated_local_example {
		required = 1
		
		stack {
			1 = TEXT
			1.value = {$1}+{$2}+{$3}+{$4}

			# m to m relation
			2 = commaseparated_local
			2 {
				# split value for relation
				split1 = ;
				split2 = +
				
								
				# table to relate to
				table = fe_groups
				
				# field to relate to and add value if record is missing
				tablekeyfield = title
				
				tablekeyfields = title;title_fr;title_it;title_en
				
				
				
				# pid for the relation records
				# (if not set global import pid will be taken)
				tablepid = 157
				
			}
		}
	}';
	}
}