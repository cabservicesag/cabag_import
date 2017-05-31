<?php
namespace Cabag\CabagImport\Logger;
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Sonja Scholz <ss@cabag.ch>
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

/**
 * Field proccessor class for the 'cabag_import' extension.
 *
 * @author	Sonja Scholz <ss@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class MailLogger extends AbstractLogger {
	// source object
	private $sourceObject;

	// current configuration
	private $conf;

	// messages (text only)
	private $messages;

	// mail body
	private $mailbody;


	/**
	 * initialize
	 *
	 * @param object handler object
	 * @param array typoscript configuration for this loghandler
	 * @return boolean if logged
	 */
	public function main($source_object, $conf) {
		if(empty($conf['to'])) {
			throw new Exception('Mail Log recipient not set', 1438848019);
		}
		$this->sourceObject = $source_object;

		$this->conf = $conf;
	}

	/**
	 * Log string
	 *
	 * @param string message
	 * @param int type -> 0 error, 1 warning, 2 info
	 * @return boolean if logged
	 */
	public function setMessage($message, $type, $data = array()) {
		// only store messeges type < 2 (warning or errors)
		if($type < 2) {
			$this->messages[] = $message;

			$this->mailbody .= '
-------------------------------------
Text: '.strip_tags($message).'
'.print_r($data,true).'
=====================================

			';
		}
	}

	/**
	 * get messages
	 *
	 * @return array with all log messages
	 */
	public function getMessages() {
		return $this->messages;
	}

	/**
	 * finish everything
	 *
	 * @return boolean always true
	 */
	public function finish() {
		if($this->mailbody) {

			$mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Mail\MailMessage');

			// set recipient
			$recipients = explode(',', $this->conf['to']);

			forEach($recipients as $recipient) {
				$recipient = trim($recipient);
				// set sender
				if(empty($this->conf['from'])) {
					if(!empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAdress'])) {
						$sender = array($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAdress'] => $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName']);
					} else {
						$sender = array('cabag_import@cabag.ch' => 'Cabag Import');
					}
				} else {
					$sender = array($this->conf['from'] => '');
				}

				// set reply to
				if(!empty($this->conf['replyTo'])) {
					$replyTo = array($this->conf['replyTo'] => '');
				} else {
					$replyTo = $sender;
				}


				$mail->setFrom($sender);
				$mail->setSubject($this->conf['subject']);
				$mail->setReplyTo($replyTo);

				$mail->setTo($recipient);
				$mail->setBody($this->mailbody, 'text/plain');

				$mail->send();
			}

		}
	}
}
