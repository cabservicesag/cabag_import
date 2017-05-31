<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class JsonRange implements AvailableFieldprocInterface {

	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'This fieldproc take the complete raw row and iterates over it to create a NoSQL field value';
	}

	/**
	 * Get the configuration example
	 *
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
		jsonrange_example {
			stack {
				1 = jsonrange
				1 {
					# from where the fields should be stored as json key:value pairs
					from = 20

					# to where the fields should be stored as json key:value paires
					to =
				}
			}
		}';
	}
}
