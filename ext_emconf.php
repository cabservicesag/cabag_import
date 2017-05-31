<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "cabag_import".
 *
 * Auto generated 08-12-2014 17:42
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'CAB AG Import',
	'description' => 'Extension to handle datatransfer between any given data interfaces.
Supports the following data sources:
	- Files, they can be interpreted as
		- CSV
		- XML
		- JSON
	- LDAP
	- MS SQL (old PHP API)
	- MS SQL (new PHP API)
	- MySQL
	- Records from a data tree
	- xls
Supports the following data storages:
	- CSV
	- Mail (for mailing, oviously)
	- SQL (MySQL storage)
	- MS SQL
	- TYPO3 TCE

This version is a coplete rewrite with a proper MVC pattern and PHP Namespaces in order to ensure the future developments with TYPO3 6.2+ and Symfony.',
	'category' => 'misc',
	'author' => 'Sonja Scholz / Jonas Felix / Tizian Schmidlin (And thanks to: Dr. Blattner, bm and dk)',
	'author_email' => 'ss@cabag.ch, jf@cabag.ch, st@cabag.ch',
	'state' => 'experimental',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'author_company' => '',
	'version' => '2.8.27',
	'constraints' =>
	array (
		'depends' =>
		array (
			'cms' => '6.2.0-0.0.0',
			'scheduler' => '6.2.0-0.0.0',
		),
		'conflicts' =>
		array (
		),
		'suggests' =>
		array (
		),
	),
	'_md5_values_when_last_written' => 'a:84:{s:12:"#Untitled-4#";s:4:"ca25";s:9:"ChangeLog";s:4:"2822";s:21:"ext_conf_template.txt";s:4:"08ef";s:12:"ext_icon.gif";s:4:"7047";s:15:"ext_icon__x.gif";s:4:"7047";s:17:"ext_localconf.php";s:4:"affa";s:14:"ext_tables.php";s:4:"b88c";s:14:"ext_tables.sql";s:4:"8c8c";s:16:"locallang_db.xml";s:4:"c005";s:10:"README.txt";s:4:"da6d";s:7:"tca.php";s:4:"1b51";s:8:"TODO.txt";s:4:"3c00";s:42:"Classes/AdditionalFieldProvider/Import.php";s:4:"6a5d";s:44:"Classes/AdditionalFieldProvider/Import62.php";s:4:"ec1d";s:48:"Classes/Exceptions/NoImportSelectedException.php";s:4:"964d";s:23:"Classes/Task/Import.php";s:4:"3db0";s:25:"Classes/Task/Import62.php";s:4:"1e04";s:45:"Resources/Private/Language/locallang_task.xml";s:4:"19de";s:12:"cli/conf.php";s:4:"0fdd";s:16:"cli/import.phpsh";s:4:"30e1";s:18:"doc/manual-doc.sxw";s:4:"b4a3";s:14:"doc/manual.sxw";s:4:"a2d5";s:19:"doc/wizard_form.dat";s:4:"1494";s:20:"doc/wizard_form.html";s:4:"5108";s:36:"lib/class.tx_cabagimport_handler.php";s:4:"ecac";s:58:"lib/fieldprocs/class.tx_cabagimport_fieldproc_bintodec.php";s:4:"5e21";s:58:"lib/fieldprocs/class.tx_cabagimport_fieldproc_bintohex.php";s:4:"f8f4";s:71:"lib/fieldprocs/class.tx_cabagimport_fieldproc_cachedtransformselect.php";s:4:"46ea";s:54:"lib/fieldprocs/class.tx_cabagimport_fieldproc_cobj.php";s:4:"a5d2";s:67:"lib/fieldprocs/class.tx_cabagimport_fieldproc_commaseparated_mm.php";s:4:"cf9f";s:58:"lib/fieldprocs/class.tx_cabagimport_fieldproc_copyfile.php";s:4:"d856";s:58:"lib/fieldprocs/class.tx_cabagimport_fieldproc_copypage.php";s:4:"870b";s:53:"lib/fieldprocs/class.tx_cabagimport_fieldproc_dam.php";s:4:"a081";s:60:"lib/fieldprocs/class.tx_cabagimport_fieldproc_fileexists.php";s:4:"63ab";s:55:"lib/fieldprocs/class.tx_cabagimport_fieldproc_files.php";s:4:"41a7";s:58:"lib/fieldprocs/class.tx_cabagimport_fieldproc_floatval.php";s:4:"312a";s:68:"lib/fieldprocs/class.tx_cabagimport_fieldproc_htmlentitiesdecode.php";s:4:"c63d";s:66:"lib/fieldprocs/class.tx_cabagimport_fieldproc_htmlspecialchars.php";s:4:"4b6c";s:72:"lib/fieldprocs/class.tx_cabagimport_fieldproc_htmlspecialcharsdecode.php";s:4:"c216";s:63:"lib/fieldprocs/class.tx_cabagimport_fieldproc_if_preg_match.php";s:4:"73b0";s:56:"lib/fieldprocs/class.tx_cabagimport_fieldproc_intval.php";s:4:"280d";s:65:"lib/fieldprocs/class.tx_cabagimport_fieldproc_maptranslations.php";s:4:"ae77";s:55:"lib/fieldprocs/class.tx_cabagimport_fieldproc_mkdir.php";s:4:"846a";s:52:"lib/fieldprocs/class.tx_cabagimport_fieldproc_mm.php";s:4:"8a10";s:61:"lib/fieldprocs/class.tx_cabagimport_fieldproc_passwordgen.php";s:4:"ec95";s:65:"lib/fieldprocs/class.tx_cabagimport_fieldproc_preg_match_keys.php";s:4:"07b3";s:62:"lib/fieldprocs/class.tx_cabagimport_fieldproc_preg_replace.php";s:4:"7f97";s:58:"lib/fieldprocs/class.tx_cabagimport_fieldproc_relation.php";s:4:"4000";s:65:"lib/fieldprocs/class.tx_cabagimport_fieldproc_save_to_raw_row.php";s:4:"297a";s:56:"lib/fieldprocs/class.tx_cabagimport_fieldproc_select.php";s:4:"9270";s:58:"lib/fieldprocs/class.tx_cabagimport_fieldproc_sendmail.php";s:4:"c506";s:60:"lib/fieldprocs/class.tx_cabagimport_fieldproc_strtolower.php";s:4:"42ba";s:59:"lib/fieldprocs/class.tx_cabagimport_fieldproc_strtotime.php";s:4:"8463";s:54:"lib/fieldprocs/class.tx_cabagimport_fieldproc_text.php";s:4:"d6e8";s:59:"lib/fieldprocs/class.tx_cabagimport_fieldproc_transform.php";s:4:"8d46";s:48:"lib/interfaces/int.tx_cabagimport_ifieldproc.php";s:4:"c0b8";s:48:"lib/interfaces/int.tx_cabagimport_iinterpret.php";s:4:"962c";s:49:"lib/interfaces/int.tx_cabagimport_iloghandler.php";s:4:"f2ed";s:45:"lib/interfaces/int.tx_cabagimport_isource.php";s:4:"eb9c";s:46:"lib/interfaces/int.tx_cabagimport_istorage.php";s:4:"00dd";s:54:"lib/interpreter/class.tx_cabagimport_interpret_csv.php";s:4:"959a";s:66:"lib/interpreter/class.tx_cabagimport_interpret_csv_alternative.php";s:4:"20d9";s:54:"lib/interpreter/class.tx_cabagimport_interpret_xml.php";s:4:"be17";s:55:"lib/loghandler/class.tx_cabagimport_loghandler_mail.php";s:4:"7948";s:48:"lib/sources/class.tx_cabagimport_source_file.php";s:4:"2351";s:48:"lib/sources/class.tx_cabagimport_source_ldap.php";s:4:"de3f";s:49:"lib/sources/class.tx_cabagimport_source_mssql.php";s:4:"2189";s:49:"lib/sources/class.tx_cabagimport_source_mysql.php";s:4:"1b7c";s:50:"lib/sources/class.tx_cabagimport_source_mysqli.php";s:4:"dfd8";s:57:"lib/sources/class.tx_cabagimport_source_recordsintree.php";s:4:"7afc";s:50:"lib/sources/class.tx_cabagimport_source_sqlsrv.php";s:4:"09bb";s:47:"lib/sources/class.tx_cabagimport_source_xls.php";s:4:"a446";s:48:"lib/storage/class.tx_cabagimport_storage_csv.php";s:4:"2417";s:49:"lib/storage/class.tx_cabagimport_storage_mail.php";s:4:"8086";s:48:"lib/storage/class.tx_cabagimport_storage_sql.php";s:4:"505a";s:51:"lib/storage/class.tx_cabagimport_storage_sqlsrv.php";s:4:"54f7";s:48:"lib/storage/class.tx_cabagimport_storage_tce.php";s:4:"d0b8";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"fae6";s:12:"mod1/err.txt";s:4:"d41d";s:14:"mod1/index.php";s:4:"6b29";s:18:"mod1/locallang.xml";s:4:"73ab";s:22:"mod1/locallang_mod.xml";s:4:"45d7";s:19:"mod1/moduleicon.gif";s:4:"7047";}',
);
