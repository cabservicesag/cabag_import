<?php
namespace Cabag\CabagImport\Configuration\Storage;
use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class Sql extends AbstractAvailableConfiguration {
	
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Here, no mm fieldproc is possible! Also, this is available for sql, sqlsrv, mysql and mysqli';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
storage = sql
storage {
	dontUpdateFields = password
	tablekeys = uid_local, uid_foreign
	
	# if set, the tablekeys may be empty (for example 0)
	allowEmptyKeyfields = 0
	
	# if isset to 1, tstamp will be inserted, needed for deleteObsolete!
	setTstamp = 0
	
	# sets the pid if enabled (sql storage does not set any default fields)
	setPid = 0

	# if set to 1, the table will be truncated before import is started.
	# Be careful with this!
	truncateBeforeImport = 0
	
	# you can use a different database as storage (just mysql support for the moment)
	# Host of the mssql database
	//host = hostname,port
	# login user for the connection
	//user =
	# login password for the connection
	//password =
	# database to connect to
	//database =
	
	
	# insert threshold, when set to something > 0, no relations can be created!
	insertThreshold = 0
}';
	}
}