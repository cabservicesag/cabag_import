<?php
namespace Cabag\CabagImport\Domain\Repository\Storage;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Dimitri König <dk@cabag.ch>
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
 * Mail storage class for the 'cabag_import' extension.
 *
 * @author	Dimitri König <dk@cabag.ch>
 * @package	TYPO3
 * @subpackage	tx_cabagimport
 */
class Mail implements StorageInterface {
	// defined keyFields
	var $keyFields;

	// handler object
	var $objectHandler;

	// configuration array
	var $conf;

	function main($tx_cabagimport_handler, $conf=array()){
		$this->objectHandler = $tx_cabagimport_handler;
		$this->conf = $conf;

		if (empty($this->conf['to'])) {
			throw new \Exception('Storage mail config field "to" is empty', 1364305904);
		}

		if (empty($this->conf['from'])) {
			throw new \Exception('Storage mail config field "field" is empty', 1364305905);
		}

		if (empty($this->conf['subject'])) {
			throw new \Exception('Storage mail config field "subject" is empty', 1364305906);
		}

		if (empty($this->conf['bodytext'])) {
			throw new \Exception('Storage mail config field "bodytext" is empty', 1364305907);
		}
	}

	public function writeRow($row = FALSE) {
		$conf = $this->conf;
		foreach ($conf as $key => &$value) {
			$value = preg_replace_callback(
				'/\{\$(.*)\}/Uis',
				function($matches) use($row) {
					$key = $matches[1];
					if (stripos($key, 'getIndpEnv:') === 0) {
						$param = str_ireplace('getIndpEnv:', '', $key);
						return \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv($param);
					}

					if (isset($row[$key])) {
						return $row[$key];
					}

					return $matches[0];
				},
				$value
			);
			$value = trim($value);
		}

		if (empty($conf['to'])) {
			throw new \Exception('Parsed row mail config field "to" is empty', 1364306100);
		}

		if (empty($conf['from'])) {
			throw new \Exception('Parsed row mail config field "field" is empty', 1364306101);
		}

		if (empty($conf['subject'])) {
			throw new \Exception('Parsed row mail config field "subject" is empty', 1364306102);
		}

		if (empty($conf['bodytext'])) {
			throw new \Exception('Parsed row mail config field "bodytext" is empty', 1364306103);
		}

		if ($this->objectHandler->dryMode == FALSE) {
			if ($this->conf['usePHPMailFunction']) {
				$additionalHeader = 'From: ' . $conf['from'];
				if (!empty($conf['cc'])) {
					$additionalHeader .= "\r\n" . $conf['cc'];
				}
				if (!empty($conf['bcc'])) {
					$additionalHeader .= "\r\n" . $conf['bcc'];
				}
				$result = mail($conf['to'], $conf['subject'], $conf['bodytext'], $additionalHeader);
			} else {
				$mailMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Mail\MailMessage');
				$mailMessage->addTo($conf['to'])
						->addFrom($conf['from'])
						->setSubject($conf['subject'])
						->setBody($conf['bodytext']);

				if (!empty($conf['cc'])) {
					foreach(\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $conf['cc']) as $cc) {
						$mailMessage->addCc($cc);
					}
				}
				if (!empty($conf['bcc'])) {
					foreach(\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $conf['bcc']) as $bcc) {
						$mailMessage->addBcc($bcc);
					}
				}

				$result = $mailMessage->send();
			}

			if ($result) {
				$this->objectHandler->setMessage('[OK] Sending mail to ' . htmlspecialchars($conf['to']));
			} else {
				$this->objectHandler->setMessage('[ERROR] Sending mail to ' . htmlspecialchars($conf['to']));
			}
		} else {
			$content = "\n\n";
			foreach ($conf as $key => $val) {
				$content .= $key . ': ' . $val . "\n";
			}
			$content .= "\n\n";
			$this->objectHandler->setMessage('Would send the following mail: ' . \TYPO3\CMS\Core\Utility\DebugUtility::viewArray($conf));
		}
	}

	public function insertRow($row = FALSE, $table = FALSE) {
	}

	public function updateRow($row = FALSE, $table = FALSE) {
	}

	public function deleteObsolete($table, $addWhere = '') {
	}

	public function setTstamp($table, $uid) {
	}

	public function createMMRelation($relations, $rowUid = FALSE) {
	}
}
