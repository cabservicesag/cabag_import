<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class PasswordGen implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Generate a password and send to given email';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '	
	passwordgen_example {
		# unsets the field if it is empty (workaround for certain TCE defaults
		unsetIfEmpty = 1
		
		stack {
			1 = passwordgen
			1 {
				# how many chars do you want
				length = 8
				
				# if set alphanumeric password is generated, default is numeric
				alphanum = 1
			}
			
			2 = sendmail 
			2 {
				# the recipient adress (can be static)
				recipient = {$email}
				
				# from
				from = admin@cabag.ch
				
				# subject of the mail
				subject = This is a mail of the import system.
				
				# text for the mail
				bodytext (
Hello {$name}

This is your new password: {$currentFieldValue}

Best regards
Your cabag_import
				)
				
				# if this sql select returns a empty result the mail will be sent
				sendIfNoResultSQLSelect = SELECT uid FROM fe_users WHERE keyfield = {$keyfield}
			}
		}
	}';
	}
}