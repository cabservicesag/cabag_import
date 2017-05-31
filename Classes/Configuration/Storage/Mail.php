<?php
namespace Cabag\CabagImport\Configuration\Storage;
use Cabag\CabagImport\Configuration\AbstractAvailableConfiguration;

class Mail extends AbstractAvailableConfiguration {
	
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'mail storage which sends a mail per row';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
storage = mail
storage {
	# if usePHPMailFunction is set to 1 then phps mail function will be used. otherwise typo3 mailsettings found in $GLOBALS[TYPO3_CONF_VARS][MAIL]
	usePHPMailFunction = 0
	from = dk@cabag.ch
	to = dkatcabag@gmail.com
	cc = bm@cabag.ch
	bcc = jf@cabag.ch
	subject = Benachrichtigung über deaktivierte TYPO3 Backend Benutzer als Seitenverantwortliche
	bodytext (
Ciao

Folgende Seiten stehen in Verantwortung von zur Zeit deaktivierten TYPO3 Backend Benutzern:

{$listOfPages}

Freundliche Grüsse
Ihr Responsibility Checker
{$getIndpEnv:TYPO3_SITE_URL}
	)
}
';
	}
}