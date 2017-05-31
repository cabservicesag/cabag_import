<?php

	// DO NOT REMOVE OR CHANGE THESE 3 LINES:
define('TYPO3_MOD_PATH', '../typo3conf/ext/cabag_import/mod1/');
$BACK_PATH='../../../../typo3/';
$MCONF['name']='web_txcabagimportM1';


$MCONF['access']='user,group';
$MCONF['script']='index.php';

if(defined('TYPO3_version') && version_compare(TYPO3_version, '7.6.0', '>=')) {
	$MLANG['default']['tabs_images']['tab'] = 'EXT:cabag_import/Resources/Public/Icons/module-cabagimport.svg';
} else {
	$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
}

$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MLANG['default']['ll_ref']='LLL:EXT:cabag_import/mod1/locallang_mod.xml';
