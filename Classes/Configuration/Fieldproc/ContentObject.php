<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class ContentObject implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Use a cObj to generate content';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
	cobj_example {
		stack {
			1 = cobj

			# either simluatePid or defaultPid will be taken
			# needed for typoscript
			1.simulatePid = 196438

			1.config = TEXT
			1.config {
				typolink {
					parameter = {$uid}
					additionalParams = &L=3
					returnLast = url
				}
			}

			2.config = COA
			2.config {
				10 = TEXT
				10 {
					value = Hello
				}

				20 = TEXT
				20 {
					value = World!
				}
			}
		}
	}';
	}
}