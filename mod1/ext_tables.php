<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_cabagimport_config');

$TCA['tx_cabagimport_config'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:cabag_import/Resources/Private/Language/locallang_db.xml:tx_cabagimport_config',
		'label'     => 'title',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',
		'delete' => 'deleted',
		'enablecolumns' => array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY).'Configuration/TCA/Config.php',
		'iconfile'          => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY).'ext_icon.gif',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden, title, configuration',
	)
);


if (TYPO3_MODE == 'BE')	{
		// check for uploads/tx_cabagimport and add .htaccess security protection
	if (file_exists(PATH_site . 'uploads/tx_cabagimport') && !file_exists(PATH_site . 'uploads/tx_cabagimport/.htaccess')) {
		file_put_contents(PATH_site . 'uploads/tx_cabagimport/.htaccess', 'Deny from all');
	}

	$iconFile = 'EXT:' . $_EXTKEY . '/ext_icon.gif';

	if(defined('TYPO3_version') && version_compare(TYPO3_version, '7.6.0', '>=')) {
		$iconFile = 'EXT:cabag_import/Resources/Public/Icons/module-cabagimport.svg';
	}

	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Cabag.' . $_EXTKEY,
        'web',          // Main area
        'txcabagimportM1',         // Name of the module
        '',             // Position of the module
        array(          // Allowed controller action combinations
            'Import' => 'index,dry,import',
            'Batch' => 'index,dry,run,next',
        ),
        array(          // Additional configuration
            'access'    => 'user,group',
            'icon'      => $iconFile,
            'labels'    => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xml',
        )
    );
}
?>
