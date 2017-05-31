<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class TransformToSqlIn implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Transform value to SQL IN format with split option';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	transform_to_sql_in_example {
		stack {
			
			1 = TEXT
			1.value = {$org1;lang-de};{$org2;lang-de} 
			
			2 = transform_to_sql_in
			2 {
				split = ; 
			}

			3 = select
			3.sql = SELECT  GROUP_CONCAT(uid) FROM fe_groups WHERE (title IN ({$currentFieldValue}) AND deleted = 0 

		}
	}';
	}
}