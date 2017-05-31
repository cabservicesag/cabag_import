<?php
namespace Cabag\CabagImport\Configuration\Source;

use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class Mysql extends AbstractAvailableConfiguration {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'select data from a mysql server';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
source = mysql
source {
	# if not access data is set the TYPO3 database connection is used (NO DBAL SUPPORT!)

	# Host of the mysql database
	host =
	# login user for the connection
	user =
	# login password for the connection
	password =
	# database to connect to
	database =
	# query for the import
	query =
}';
	}
}