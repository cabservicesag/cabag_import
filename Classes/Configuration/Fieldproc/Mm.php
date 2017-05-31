<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class Mm implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Create a mm relation to many records';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	1n_relation_example {
		required = 1

		stack {
			1 = TEXT
			1.value = {$Ort}
			
			# 1 to n relation
			2 = relation
			2 {
				# table of the related records
				relationtable = tx_xyz
				
				# field to search for and where the value is saved 
				# if the searched one is missing
				relationfield = fieldxyz
				
				# additional condition for searching the relation record
				relationaddwhere = AND sys_language_uid=0
				
				# pid of the related records 
				# (if not set global import pid will be taken)
				relationpid = 208
				
				# add record if not found
				addIfMissing = 1
			}
		}
	}';
	}
}