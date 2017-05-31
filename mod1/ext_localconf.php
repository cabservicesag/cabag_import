<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('
	options.saveDocNew.tx_cabagimport_config=1
');


use \Cabag\CabagImport\Utility\RegistrationUtility;
if(!class_exists('Cabag\CabagImport\Utility\RegistrationUtility')) {
	require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cabag_import') . 'Classes/Utility/RegistrationUtility.php';
}
// Available default loghandlers
// -----------------------------

// Mail
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['loghandler']['mail'] = 'EXT:cabag_import/lib/loghandler/class.tx_cabagimport_loghandler_mail.php:tx_cabagimport_loghandler_mail';
RegistrationUtility::registerLogger('Cabag\CabagImport\Logger\MailLogger', 'mail');

// Available default storages
// -----------------------------

// TCE
RegistrationUtility::registerStorage('Cabag\CabagImport\Domain\Repository\Storage\Tce', 'tce');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['storage']['tce'] = 'EXT:cabag_import/lib/storage/class.tx_cabagimport_storage_tce.php:tx_cabagimport_storage_tce';
// mysql connection - if 6.2 use mysqli in any case
// SQL
RegistrationUtility::registerStorage('Cabag\CabagImport\Domain\Repository\Storage\MySqli', 'sql');
RegistrationUtility::registerStorage('Cabag\CabagImport\Domain\Repository\Storage\MySqli', 'mysqli');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['storage']['sql'] = 'EXT:cabag_import/lib/storage/class.tx_cabagimport_storage_sql.php:tx_cabagimport_storage_sql';

// SQL
RegistrationUtility::registerStorage('Cabag\CabagImport\Domain\Repository\Storage\Sqlsrv', 'sqlsrv');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['storage']['sqlsrv'] = 'EXT:cabag_import/lib/storage/class.tx_cabagimport_storage_sqlsrv.php:tx_cabagimport_storage_sqlsrv';

// CSV
RegistrationUtility::registerStorage('Cabag\CabagImport\Domain\Repository\Storage\Csv', 'csv');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['storage']['csv'] = 'EXT:cabag_import/lib/storage/class.tx_cabagimport_storage_csv.php:tx_cabagimport_storage_csv';

// MAIL
RegistrationUtility::registerStorage('Cabag\CabagImport\Domain\Repository\Storage\Mail', 'mail');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['storage']['mail'] = 'EXT:cabag_import/lib/storage/class.tx_cabagimport_storage_mail.php:tx_cabagimport_storage_mail';


// Available default sources
// -----------------------------

// regular file
RegistrationUtility::registerSource('Cabag\CabagImport\Domain\Repository\Source\File', 'file');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['source']['file'] = 'EXT:cabag_import/lib/sources/class.tx_cabagimport_source_file.php:tx_cabagimport_source_file';

// xls file
RegistrationUtility::registerSource('Cabag\CabagImport\Domain\Repository\Source\Xls', 'xls');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['source']['xls'] = 'EXT:cabag_import/lib/sources/class.tx_cabagimport_source_xls.php:tx_cabagimport_source_xls';

// mysqli connection
RegistrationUtility::registerSource('Cabag\CabagImport\Domain\Repository\Source\Mysqli', 'mysqli');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['source']['mysqli'] = 'EXT:cabag_import/lib/sources/class.tx_cabagimport_source_mysqli.php:tx_cabagimport_source_mysqli';

// mysql connection - use mysqli in any case
RegistrationUtility::registerSource('Cabag\CabagImport\Domain\Repository\Source\Mysqli', 'mysql');
/*
if (version_compare(TYPO3_branch, '6.2', '>=')) {
	$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['source']['mysql'] =
	$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['source']['mysqli'];
} else {
	$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['source']['mysql'] = 'EXT:cabag_import/lib/sources/class.tx_cabagimport_source_mysql.php:tx_cabagimport_source_mysql';
} */

// recordsintree connection
RegistrationUtility::registerSource('Cabag\CabagImport\Domain\Repository\Source\Recordsintree', 'recordsintree');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['source']['recordsintree'] = 'EXT:cabag_import/lib/sources/class.tx_cabagimport_source_recordsintree.php:tx_cabagimport_source_recordsintree';

// mssql connection - always use sqlsrv, since TYPO3 6.2 needs at least PHP 5.3 and there is no mssql PHP extension for 5.3 (replaced by sql_srv)
RegistrationUtility::registerSource('Cabag\CabagImport\Domain\Repository\Source\Sqlsrv', 'mssql');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['source']['mssql'] = 'EXT:cabag_import/lib/sources/class.tx_cabagimport_source_mssql.php:tx_cabagimport_source_mssql';

// ldap connection
RegistrationUtility::registerSource('Cabag\CabagImport\Domain\Repository\Source\Ldap', 'ldap');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['source']['ldap'] = 'EXT:cabag_import/lib/sources/class.tx_cabagimport_source_ldap.php:tx_cabagimport_source_ldap';


// sql srv connection
RegistrationUtility::registerSource('Cabag\CabagImport\Domain\Repository\Source\Sqlsrv', 'sqlsrv');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['source']['sqlsrv'] = 'EXT:cabag_import/lib/sources/class.tx_cabagimport_source_sqlsrv.php:tx_cabagimport_source_sqlsrv';

// Available default interprets
// -----------------------------

// csv
RegistrationUtility::registerInterpreter('Cabag\CabagImport\Interpret\Csv', 'csv');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['interpret']['csv'] = 'EXT:cabag_import/lib/interpreter/class.tx_cabagimport_interpret_csv.php:tx_cabagimport_interpret_csv';

// csv with php and not with fgetcsv
RegistrationUtility::registerInterpreter('Cabag\CabagImport\Interpret\CsvAlternative', 'csv_alternative');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['interpret']['csv_alternative'] = 'EXT:cabag_import/lib/interpreter/class.tx_cabagimport_interpret_csv_alternative.php:tx_cabagimport_interpret_csv_alternative';

// xml
RegistrationUtility::registerInterpreter('Cabag\CabagImport\Interpret\Xml', 'xml');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['interpret']['xml'] = 'EXT:cabag_import/lib/interpreter/class.tx_cabagimport_interpret_xml.php:tx_cabagimport_interpret_xml';

//json
RegistrationUtility::registerInterpreter('Cabag\CabagImport\Interpret\Json', 'json');

// Available default fieldprocs
// -----------------------------

// maptranslations
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\MapTranslations', 'maptranslations');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['maptranslations'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_maptranslations.php:tx_cabagimport_fieldproc_maptranslations';

// relation
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\Relation', 'relation');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['relation'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_relation.php:tx_cabagimport_fieldproc_relation';

// text
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\Text', 'TEXT');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['TEXT'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_text.php:tx_cabagimport_fieldproc_text';

// mm relations
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\Mm', 'mm');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['mm'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_mm.php:tx_cabagimport_fieldproc_mm';

// commaseparated mm relations
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\CommaSeparatedMm', 'commaseparated_mm');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['commaseparated_mm'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_commaseparated_mm.php:tx_cabagimport_fieldproc_commaseparated_mm';

// commaseparated mm relations
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\CommaSeparatedLocal', 'commaseparated_local');


// ad to commaseparated list
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\AddToCommaSeparatedList', 'add_to_commaseparated_list');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['ad_to_commaseparated_list'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_commaseparated_mm.php:tx_cabagimport_fieldproc_ad_to_commaseparated_list';

// select
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\Select', 'select');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['select'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_select.php:tx_cabagimport_fieldproc_select';

// cachedtransformselect
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\CachedTransformSelect', 'cachedtransformselect');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['cachedtransformselect'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_cachedtransformselect.php:tx_cabagimport_fieldproc_cachedtransformselect';

// preg_replace
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\PregReplace', 'preg_replace');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['preg_replace'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_preg_replace.php:tx_cabagimport_fieldproc_preg_replace';

// strtotime
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\StrToTime', 'strtotime');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['strtotime'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_strtotime.php:tx_cabagimport_fieldproc_strtotime';

// strtolower
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\StrToLower', 'strtolower');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['strtolower'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_strtolower.php:tx_cabagimport_fieldproc_strtolower';

// intval
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\Intval', 'intval');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['intval'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_intval.php:tx_cabagimport_fieldproc_intval';

// fileexists
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\FileExists', 'fileexists');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['fileexists'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_fileexists.php:tx_cabagimport_fieldproc_fileexists';

// floatval
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\Floatval', 'floatval');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['floatval'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_floatval.php:tx_cabagimport_fieldproc_floatval';

// transform
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\Transform', 'transform');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['transform'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_transform.php:tx_cabagimport_fieldproc_transform';

// files
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\Files', 'files');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['files'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_files.php:tx_cabagimport_fieldproc_files';

// copyfile
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\CopyFile', 'copyfile');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['copyfile'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_copyfile.php:tx_cabagimport_fieldproc_copyfile';

// passwordgen
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\PasswordGen', 'passwordgen');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['passwordgen'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_passwordgen.php:tx_cabagimport_fieldproc_passwordgen';

// sendmail
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\Sendmail', 'sendmail');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['sendmail'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_sendmail.php:tx_cabagimport_fieldproc_sendmail';

// mkdir
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\Mkdir', 'mkdir');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['mkdir'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_mkdir.php:tx_cabagimport_fieldproc_mkdir';

// dam - does not exist anymore

// preg_match_keys
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\PregMatchKeys', 'preg_match_keys');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['preg_match_keys'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_preg_match_keys.php:tx_cabagimport_fieldproc_preg_match_keys';

// if_preg_match
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\IfPregMatch', 'if_preg_match');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['if_preg_match'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_if_preg_match.php:tx_cabagimport_fieldproc_if_preg_match';

// save_to_raw_row
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\SaveToRawRow', 'save_to_raw_row');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['save_to_raw_row'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_save_to_raw_row.php:tx_cabagimport_fieldproc_save_to_raw_row';

// copypage
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\CopyPage', 'copypage');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['copypage'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_copypage.php:tx_cabagimport_fieldproc_copypage';

// cobj
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\ContentObject', 'cobj');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['cobj'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_cobj.php:tx_cabagimport_fieldproc_cobj';

// bintohex
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\BinToHex', 'bintohex');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['bintohex'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_bintohex.php:tx_cabagimport_fieldproc_bintohex';

// bintodec
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\BinToDec', 'bintodec');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['bintodec'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_bintodec.php:tx_cabagimport_fieldproc_bintodec';

// htmlspecialchars
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\Htmlspecialchars', 'htmlspecialchars');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['htmlspecialchars'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_htmlspecialchars.php:tx_cabagimport_fieldproc_htmlspecialchars';

// htmlspecialcharsdecode
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\HtmlspecialcharsDecode', 'htmlspecialcharsdecode');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['htmlspecialcharsdecode'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_htmlspecialcharsdecode.php:tx_cabagimport_fieldproc_htmlspecialcharsdecode';

// htmlspecialcharsdecode
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\HtmlEntitiesDecode', 'htmlentitiesdecode');
//$TYPO3_CONF_VARS['SC_OPTIONS']['ext/cabag_import/lib/class.tx_cabagimport_handler.php']['fieldproc']['htmlentitiesdecode'] = 'EXT:cabag_import/lib/fieldprocs/class.tx_cabagimport_fieldproc_htmlentitiesdecode.php:tx_cabagimport_fieldproc_htmlentitiesdecode';

// htmlspecialcharsdecode
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\JsonRange', 'jsonrange');

// transformtosqlin
RegistrationUtility::registerFieldproc('Cabag\CabagImport\Fieldproc\TransformToSqlIn', 'transform_to_sql_in');

// Configuration Examples
$GLOBALS['SC_OPTIONS']['cabag_import']['ConfigurationExamples'] = array(
	'Source' => array(
		'Cabag\CabagImport\Configuration\Source\File' => false,
		'Cabag\CabagImport\Configuration\Source\Ldap' => false,
		'Cabag\CabagImport\Configuration\Source\Mssql' => false,
		'Cabag\CabagImport\Configuration\Source\Mysql' => false,
		'Cabag\CabagImport\Configuration\Source\RecordsInTree' => false,
		'Cabag\CabagImport\Configuration\Source\Xls' => false,
	),
	'Storage' => array(
		'Cabag\CabagImport\Configuration\Storage\Csv' => false,
		'Cabag\CabagImport\Configuration\Storage\File' => false,
		'Cabag\CabagImport\Configuration\Storage\Mail' => false,
		'Cabag\CabagImport\Configuration\Storage\Sql' => false,
		'Cabag\CabagImport\Configuration\Storage\Tce' => false,
	),
	'Handler' => array(
		'Cabag\CabagImport\Configuration\Handler\Import' => false,
	),
	'Mapping' => array(
		'Cabag\CabagImport\Configuration\Mapping\Mapping' => array(
			'ChildConfigurations' => array(
				'Cabag\CabagImport\Configuration\Fieldproc\CachedTransformSelect' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\ComaseparatedMm' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\CommaSeparatedLocal' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\AddToCommaseparatedList' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\ContentObject' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\CopyFile' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\CopyPage' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\DirectSelection' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\Files' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\FileExists' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\IfPregMatch' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\MapTranslations' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\Mkdir' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\Mm' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\PasswordGen' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\Pid' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\PregMatchKeys' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\Relation' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\SaveToRawRow' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\Select' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\StrToTime' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\TextTransform' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\Transform' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\JsonRange' => false,
				'Cabag\CabagImport\Configuration\Fieldproc\TransformToSqlIn' => false,
			),
		),
	),
);

// -------------------------------------------- TYPO3 Scheduler Task Defitinion -----------------------------------------

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Cabag\CabagImport\Scheduler\Task\Import'] = array(
	'extension'        => 'cabag_import',
	'title'            => 'LLL:EXT:' . 'cabag_import' . '/Resources/Private/Language/locallang_task.xml:name',
	'description'      => 'Execute a cabag_import Configuration automatically',
	'additionalFields' => 'Cabag\CabagImport\Scheduler\AdditionalFieldProvider\Import',
);
require_once TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cabag_import') . 'Classes/Scheduler/Task/Import.php';

// -------------------------------------------- TYPO3 Scheduler Task Definition end -------------------------------------


$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Cabag\CabagImport\Command\ImportCommandController';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 'Cabag\CabagImport\Command\CleanupCommandController';
