<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class ComaseparatedMm implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'can be used for usergroup relation in fe_users if they have to be generated';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	# can be used for usergroup relation in fe_users if they have to be generated
	commaseparated_mm_relation_example {
		required = 1
		
		stack {
			1 = TEXT
			1.value = {$1}

			# m to m relation
			2 = commaseparated_mm
			2 {
				# split value for relation
				split = ,
				
				# split alternative for newline
				//split.newline = 1
				
				# possibility to restrict the relation to a position within 
				# the value
				//splitUseOnlyPosition = 1
				
				# glue between the relations (normaly commaseparated)
				relationglue = ,
				
				# table to relate to
				table = tx_cabagshop_category
				
				# field to relate to and add value if record is missing
				tablekeyfield = catalogkey
				
				# set to 1 so search in the tablekeyfield with LIKE %value%
				tablekeyfieldlike = 0
				
				# pid for the relation records
				# (if not set global import pid will be taken)
				tablepid = 109
				
				# add record if not found
				addIfMissing = 1
			}
		}
	}';
	}
}