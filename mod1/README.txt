This is the cab services ag import extension.

- Check out manual.sxw for full documentation!
- Check out tca.php or create new configuration record to find all configuration options!!!

Add fieldprocs in your extensions by adding the class as hook in your ext_localconf.php

$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc'][FIELDPROCKEY] = 'PATHTOCLASS:CLASSNAME';

To use import configs from files, register an import config like this:
$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['importConfig']['importkey'] = array(
	'file' => 'fileadmin/...',
	'title' => 'My fancy Import',
	'accessForGroupOnly' => '2,4' // comma separated list of backend group ids which are allowed to access this config.
);