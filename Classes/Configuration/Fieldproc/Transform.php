<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class Transform implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Transform a text into another';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	transform_example {
		stack {
			1 = transform
			1 {
				# use value like TEXT fieldproc or do a own stack part for TEXT fieldproc before
				value = {$Kategorie}
				
				transform {
					sourcevalue1 = destvalue1
					sourcevalue2 = destvalue2
				}
				
				# a default value is always needed
				default = defaultvaluexyz
			}
		}
	}';
	}
}