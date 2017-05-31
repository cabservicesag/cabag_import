<?php
namespace Cabag\CabagImport\Utility;

/***************************************************************
*  Copyright notice
*
*  (c) 2017 Tizian Schmidlin <st@cabag.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

class RegistrationUtility {

	/**
	 * Register a fieldproc
	 *
	 * @param string $className the class where the fieldproc comes from
	 * @param string $fieldproc the name used in the configurations
	 */
	static public function registerFieldproc($className, $fieldproc) {
		self::register($className, $fieldproc, 'fieldproc');
	}
	/**
	 * Register an interpreter
	 *
	 * @param string $className the class where the interpret comes from
	 * @param string $interpret the name used in the configurations
	 */
	static public function registerInterpreter($className, $interpret) {
		self::register($className, $interpret, 'interpret');
	}

	/**
	 * Register a source
	 *
	 * @param string $className the class where the source comes from
	 * @param string $source the name used in the configurations
	 */
	static public function registerSource($className, $source) {
		self::register($className, $source, 'source');
	}

	/**
	 * Register a storage
	 *
	 * @param string $className the class where the source comes from
	 * @param string $storage the name used in the configurations
	 */
	static public function registerStorage($className, $storage) {
		self::register($className, $storage, 'storage');
	}

	/**
	 * Register a loghandler
	 *
	 * @param string $className the class where the source comes from
	 * @param string $loghandler the name used in the configurations
	 */
	static public function registerLoghandler($className, $loghandler) {
		self::registerLogger($className, $loghandler);
	}

	/**
	 * Register a logger
	 *
	 * @param string $className the class where the source comes from
	 * @param string $logger the name used in the configurations
	 */
	static public function registerLogger($className, $logger) {
		self::register($className, $logger, 'loghandler');
	}

	/**
	 * Register into $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']
	 *
	 * @param string $className the classname
	 * @param string $name the name in the configuration
	 * @param string $what what we want to configure
	 */
	static protected function register($className, $name, $what) {
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php'][$what][$name] = $className;
	}

	/**
	 * Add to $GLOBALS['SC_OPTIONS']['cabag_import']['ConfigurationExamples']
	 *
	 * @param string $className the classname
	 * @param string $name the name in the configuration
	 */
	 public static function addAvailableOption($className, $source) {
		 if(!is_array($GLOBALS['SC_OPTIONS']['cabag_import']['ConfigurationExamples'])) {
			 $GLOBALS['SC_OPTIONS']['cabag_import']['ConfigurationExamples'] = array();
		 }

		 if(!is_array($GLOBALS['SC_OPTIONS']['cabag_import']['ConfigurationExamples'][$source])){
			 $GLOBALS['SC_OPTIONS']['cabag_import']['ConfigurationExamples'][$source] = array();
		 }
		 	$GLOBALS['SC_OPTIONS']['cabag_import']['ConfigurationExamples'][$source][$className] = '';
	 }
}
