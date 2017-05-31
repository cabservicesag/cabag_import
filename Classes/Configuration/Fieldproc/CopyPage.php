<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class CopyPage implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Copy a page to another place';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	copypage_example {
		stack {
			1 = select
			1.sql = SELECT uid FROM pages WHERE pid = 1212 AND deleted = 0 AND hidden = 0

			2 = copypage
			2 {
				# destinationpid, otherwise its {$currentFieldValue}
				# destinationpid = 1287

				sourcepid = 345
				
				# Sets the number of branches on a page tree to copy.
				copyTree = 0

				# clear page cache afterwards
				clearPageCache = 0
			}
			
			3 = copypage
			3 {
				# destinationpid, otherwise its {$currentFieldValue}
				# destinationpid = 1287

				sourcepid = 51
				
				# Sets the number of branches on a page tree to copy.
				copyTree = 0

				# clear page cache afterwards
				clearPageCache = 0
			}
		}
	}';
	}
}