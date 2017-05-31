<?php
namespace Cabag\CabagImport\Configuration\Source;
use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class Mssql extends AbstractAvailableConfiguration {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'select data from a MS SQL server';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
source = mssql
source {
	# Host of the mssql database
	host = hostname,port
	# login user for the connection
	user =
	# login password for the connection
	password =
	# database to connect to
	database =
	# do not select a database (database must be preselected by mssql server)
	noDatabase = 1
	# query for the import
	query =
	# The number of records to batch in the buffer.
	batchSize = 
	# Optional minimum message serverity for mssql to fail (10 - Status Message:Does not raise an error but returns a string., 11, 12, 13 - Not Used, 14 - Informational Message, 15 - Warning Message, 16 - Critical Error: The Procedure Failed)
	minimumMessageSeverity =
	# do not use mssql_pconnect, use mssql_connect instead
	noPconnect = 0
}';
	}
}