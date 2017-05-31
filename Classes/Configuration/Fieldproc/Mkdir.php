<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class Mkdir implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'creates directory from value or currentFieldValue and returns path as next currentFieldValue';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	userhome_mkdir_example {
		stack {
			# creates directory from value or currentFieldValue and returns path as next currentFieldValue
			1 = mkdir
			1 {
				# creates all folders in the path if not existing
				deep = 1
				
				# path relative to PATH_site
				value = fileadmin/user_upload/users/{$username}
			}
		}
	}';
	}
}