<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class FileExists implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Check if the file exists';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	fileexists {
		required = 1
		
		stack {
			1 = fileexists
			1 {
				value = {$fieldwithpathtofile}
				ifExists = Not found
				ifNotExists = File found
			}
		}
	}';
	}
}