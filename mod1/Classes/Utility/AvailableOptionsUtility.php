<?php

namespace Cabag\CabagImport\Utility;
use Cabag\CabagImport\Configuration\AvailableConfigurationInterface;
use Cabag\CabagImport\Exceptions;

class AvailableOptionsUtility {

	/**
	 * Get the options for cabag_import from either the global variable containing the examples
	 * or the new school configuration examples defined in "Classes/Configuration".
	 *
	 * @return string
	 */
	static public function getAvailableOptions() {
		if(is_array($GLOBALS['SC_OPTIONS']['cabag_import']['ConfigurationExamples'])) {
			return $GLOBALS['tx_cabag_import-availableOptions'] = self::getAvailableOptionsFromArray($GLOBALS['SC_OPTIONS']['cabag_import']['ConfigurationExamples']);
	 }elseif(is_string($GLOBALS['tx_cabag_import-availableOptions'])) {
 			return $GLOBALS['tx_cabag_import-availableOptions'];
 		}
		return '';
	}

	/**
	 * Process any given array and try to create a meaningful description text.
	 *
	 * @param array $optionsArray the array with the options for cabag_import
	 * @return string
	 */
	static public function getAvailableOptionsFromArray(array $optionsArray) {
		$output = '';
		foreach($optionsArray as $part => $availableConfigurations) {
			$output .=
'-------------------------------------------------------------------------------
' . $part . '
-------------------------------------------------------------------------------';
			foreach($availableConfigurations as $availableConfiguration => $moreInfo) {
				$currentConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($availableConfiguration);

				if($currentConfiguration instanceof AvailableConfigurationInterface) {
					if(!$currentConfiguration->hasChildConfigurations()) {
						$output .= '# ' . $currentConfiguration->getDescription() . $currentConfiguration->getConfigurationExample() . PHP_EOL . PHP_EOL;
					} else {
						$output .= $currentConfiguration->getDescription();
						if(array_key_exists('ChildConfigurations', $moreInfo)) {
							foreach($moreInfo['ChildConfigurations'] as $childConfiguration => $val) {
								if(!empty($childConfiguration)) {
									$currentChildConfiguration =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($childConfiguration);
									$output .= '# ' . $currentChildConfiguration->getDescription() . $currentChildConfiguration->getConfigurationExample() . PHP_EOL;
								}
							}
						} else {
							$output .= '!!! No child configurations defined for ' . $availableConfiguration;
						}
						$output .= '}

';
					}
				}
			}
		}

		return $output . '
-------------------------------------------------------------------------------';
	}
}
