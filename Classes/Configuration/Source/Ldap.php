<?php
namespace Cabag\CabagImport\Configuration\Source;
use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class Ldap extends AbstractAvailableConfiguration {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Get data from LDAP folder';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
source = ldap
source {
	# IP Address of username
	server =
	# most commonly 389
	port = 389
	
	# username. This has to be the complete path to the user in the AD tree
	rdn = 
	# password
	password = 
	
	# path where to search in
	base_dn = OU=Switzerland,OU=Feintool,DC=ft,DC=feintool,DC=local
	
	# filter rules. See php ldap docu for further filters
	filter = (&(objectClass=user)(objectCategory=person))
}';
	}
}