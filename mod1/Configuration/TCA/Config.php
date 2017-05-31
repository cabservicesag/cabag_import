<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cabag_import') . 'Classes/Utility/AvailableOptionsUtility.php';

$GLOBALS['tx_cabag_import-availableOptions'] = '

# loghandler for mails if not set oldschool will be used
#loghandler = mail
#loghandler {
#	to = jf@cabag.ch, bm@cabag.ch
#	subject = cabag_import: Bsp. Import: Fe_User -> tt_adress
#}


# storage which writes the rows
storage = tce
storage {
	dontUpdateFields = password
	dontUsePidForKeyField = 0

	# needed for ordering records in TYPO3
	// moveAfterField = myFunnyFieldWithUIDofpreviousRecord

	# if set to 1, deleted records will be reactivated when they get imported again
	reactivateDeletedRecords = 0
}

# Here, no mm fieldproc is possible!
storage = sql
storage {
	dontUpdateFields = password
	tablekeys = uid_local, uid_foreign

	# only update existing records but dont create new ones
	# dontAllowInserts = 1

	# if set, the tablekeys may be empty (for example 0)
	allowEmptyKeyfields = 0

	# if isset to 1, tstamp will be inserted, needed for deleteObsolete!
	setTstamp = 0

	# sets the pid if enabled (sql storage does not set any default fields)
	setPid = 0

	# if set to 1, the table will be truncated before import is started.
	# Be careful with this!
	truncateBeforeImport = 0

	# you can use a different database as storage (just mysql support for the moment)
	# Host of the mssql database
	//host = hostname,port
	# login user for the connection
	//user =
	# login password for the connection
	//password =
	# database to connect to
	//database =
}

# Here, no mm fieldproc and no update is possible!
storage = csv
storage {
	fields {
		# additional information needed for the fields

		# maps a field with a title that has spaces
		writeTitleRow = 1

		fields {
			doctorid.title = Doctor ID
			userid.title = User ID
		}

		# fills the field with the timestamp of the handler
		tstamp.isTstamp = 1
	}

	file {
		# path must exist and be relative to the TYPO3 directory
		path = fileadmin/user_uploads/tx_cabagimport/some_file.csv

		# if set to overwrite, the file (if it exists) will be cleared and overwritten
		mode = overwrite

		# if set to append, the file will have the rows appended
		mode = append

		# if neither overwrite nor append is set and the file exists, a new file will be created like \'some_file_1.csv\'
	}

	# csv delimiter between each field
	delimiter = ,
	// delimiter.chr = 9

	# enclosure for a field
	enclosure = "
	// enclosure.chr = 32
}

# mail storage which sends a mail per row
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

source = file
source {
	# in KB
	maxFileSize = 10000
	archivePath = uploads/tx_cabagimport/

	# path to the file (overwrithen by mod1 input)
	// filePath = fileadmin/user_upload/import/alwaysthesamename.csv

	# set to 1 to prevent file source from doing this in php: PATH_site.filePath
	// absoluteFilePath = 0

	# search for file within path (overwrithen by filePath)
	searchPath = fileadmin/user_upload/import
	searchPath {
		preg_match = .*importname.*
	}

	# interpreter which parses the data
	interpret = csv
	interpret {
		# csv options
		delimiter = ,

		# for ascii codes like tab
		// delimiter.chr = 9

		enclosure = "

		# don\'t use the native fgetcsv (stores the whole file in the RAM -> DON\'T USE IF THAT IS AN ISSUE)
		dontUsePHPFunction = 0

		# uses trim() on the values of the first row -> only usable in conjunction with dontUsePHPFunction
		trimFirstRow = 0
	}

	# executes sed for the importing file before importing it, this example fixes mac line endings
	preImportSedExpression = s/\r/\n/g

	interpret = xml
	interpret {
		recordPath = channel,0,ch,item

		utf8_decode = 1
	}
}

# select data from a mysql server
source = mysql
source {
	# if not access data is set the TYPO3 database connection is used (NO DBAL SUPPORT!)

	# Host of the mysql database
	host =
	# login user for the connection
	user =
	# login password for the connection
	password =
	# database to connect to
	database =
	# query for the import
	query =
}

# select records from a tree in the current typo3 system
source = recordsintree
source {
	# name of the record table
	table = tt_content

	# starting parent page uid
	pid = 44

	# additional where
	addWhere = AND hidden = 1
}


source = mssql
source {
	# Host of the mssql database
	host = hostname,port
	# login user for the connection
	user =
	# login password for the connection
	password =
	# database to connect to
	database =
	# do not select a database (database must be preselected by mssql server)
	noDatabase = 1
	# query for the import
	query =
	# The number of records to batch in the buffer.
	batchSize =
	# Optional minimum message serverity for mssql to fail (10 - Status Message:Does not raise an error but returns a string., 11, 12, 13 - Not Used, 14 - Informational Message, 15 - Warning Message, 16 - Critical Error: The Procedure Failed)
	minimumMessageSeverity =
	# do not use mssql_pconnect, use mssql_connect instead
	noPconnect = 0
}

# converts xls to csv
source = xls
source {
	# in KB
	maxFileSize = 10000
	archivePath = uploads/tx_cabagimport/

	# force charset if you have problems with umlaute
	forceCharset = en_US.UTF-8

	# interpreter which parses the data
	interpret = csv
	interpret {
		# csv options
		delimiter = ,

		enclosure = "
	}
}

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
}

handler {
	# table to import to
	table = tx_xy

	# row to start the import from
	// rangeFrom = 0

	# row to end the import
	// rangeTo = 1000

	# fields to identify records and rows
	keyFields = field_xy

	# delete records which are not within the import
	deleteObsolete = 0
	deleteObsolete.addWhere = sys_language_uid = 0 AND type = 0

	# define which fields should be updated for deleteObsolete
	updateObsolete = 0
	updateObsolete {
		hidden = 1
	}

	# if deleteObsolete deletes more than X records, it does not delete anything instead (only works for TCE storage)
	// deleteObsolete.deleteThreshold = 20

	# does the first row contain the labels or data
	firstRowAreKeys = 1

	# pid where to place the records
	# if not set the pid selected in the backend module will be used!
	defaultPid = 2

	# continue with the next row if a row is invalid
	continueAfterInvalidRow = 0

	# import source charset
	in_charset = CP1252

	# database charset
	out_charset = UTF-8

	# if set {$currentRowNumber} contains the current number of the row starting from 1
	addCurrentRowNumber = 0
}

mapping {

	pid {
		# define dynamic pid here or use instead of defaultPid
	}

	if_preg_match_example {
		stack {
			1 = TEXT
			1.value = {$textfield}

			2 = if_preg_match
			2 {
				pattern = /(cabag)/i
				# optional returns specific match
				returnMatchPosition = 0
			}
		}
	}

	preg_match_keys_example {
		required = 1

		stack {
			2 = preg_match_keys
			2 {
				searchfor = /tx_cabag(.*)/
				# implode string must be between two #\'s
				implodeString = #, #
			}
		}
	}

	tx_templavoila_flex {
		stack {
			1 = maptranslations
			1 {
				flex = {$tx_templavoila_flex}
			}
		}
	}

	userhome_mkdir_example {
		stack {
			# creates directory from value or currentFieldValue and returns path as next currentFieldValue
			1 = mkdir
			1 {
				# creates all folders in the path if not existing
				deep = 1

				# path relative to PATH_site
				value = fileadmin/user_upload/users/{$username}
			}
		}
	}

	select_example {
		# if set the value has to be != 0 and not empty otherwise the import will be stoped
		required = 1

		stack {
			# sql statement -> first field of the first row will be taken
			1 = select
			1.sql = SELECT field_x FROM table WHERE field_y = \'{$ImportFieldX}\'
		}

	}

	cachedtransformselect_example {
		# if set the value has to be != 0 and not empty otherwise the import will be stoped
		required = 1

		stack {
			# selects the table at the first run and reuses the result for transformation!
			1 = cachedtransformselect
			1.sql = SELECT field_from, field_to FROM table WHERE deleted=0
			# the result will be the field_to which is in the same row as the field_from that matches your {$yourfunnyfield}
			1.transform = {$yourfunnyfield}
			# you can define a cache id so you can use the same cache for several fieldprocs
			1.cacheid = funnyid
		}

	}

	direct_selection = ImportFileX

	date_example {
		required = 1

		stack {
			1 = TEXT
			1.value = {$Startdatum}

			# perl regular expression replacement
			2 = preg_replace
			2 {
				from = (\d\d)\.(\d\d)\.(\d\d\d\d)
				to = $3-$2-$1
			}

			# strtotime for the current value
			3 = strtotime
			3.default = 0
			3.timezone =  UTC+0100
		}
	}

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
	}

	transform_example {
		stack {
			1 = transform
			1 {
				# use value like TEXT fieldproc or do a own stack part for TEXT fieldproc before
				value = {$Kategorie}

				transform {
					sourcevalue1 = destvalue1
					sourcevalue2 = destvalue2
				}

				# a default value is always needed
				default = defaultvaluexyz
			}
		}
	}

	files_example {
		required = 1

		stack {
			1 = TEXT
			1.value = {$Bildname}

			2 = files
			2 {
				# find the right file in the sourceFolder
				preg_match = /.*{$currentFieldValue}[^\/]*.jpg/

				sourceFolder = fileadmin/user_upload/shop_images/

				# Search recursive from the sourceFolder
				recursive = 1

				# move the founded file to the destination folder
				destinationFolder = uploads/tx_cabagshop/

				# rename the founded file in the destination folder
				rename = {$currentFieldValue}-{$fieldProcFilesNumber}.jpg

				# Clear the currentFieldValue if no image was found
				clearCurrentFieldValueIfNothingFound = 0

				# uses exec with find/grep instead of php functions
				useFindGrep = pattern...

				# custom shell cmd
				useCustomCmd = shell cmd

				# returns imploded filelist
				onlyReturnFoundFiles = 0

				# implode char
				onlyReturnFoundFilesImplodeChr = ,
			}
		}
	}

	copy_file_example {
		required = 1

		stack {
			1 = copyfile
			1 {
				# will not be used if not set
				sourcebasepath = http://www.domain.ch/

				# if source path is empty nothing will done
				sourcepath = {$pdfpath}

				# if set, the script will append PATH_site, is ignored when sourcebasepath is set
				sourceIsRelPath = 0

				dontCopyIfExists = 1

				# if set, the filename will be replaced by this
				createFilename = {$filenametouse}.jpg

				# filename will be trimmed
				trimFilename = 1

				# continue if filename is empty
				skipEmptyFilename = 1

				# if set then only this filetypes are allowed
				allowedFiletypes = jpg,jpeg,gif,png

				destinationpath = /fileadmin/user_upload/events/

				# return just the filename if you have ie. a TCA file field
				returnJustFilename = 0

				# slow but supports curl/proxy
				//useGetURL = 1

				# enables splitting
				// split = ,

				# overwrithe if exists
				overwrithe = 1

				# do not throw exception when file not found
				doNotThrowNotFound = 0
			}

			2 = TEXT
			2 {
				value = <LINK $currentFieldValue>{$pdftitle}</LINK>
			}
		}
	}

	fileexists {
		required = 1

		stack {
			1 = fileexists
			1 {
				value = {$fieldwithpathtofile}
				ifExists = Not found
				ifNotExists = File found
			}
		}
	}

	dam_copy_file_example {
		required = 1

		stack {
			1 = copyfile
			1 {
				sourcebasepath = http://www.domain.ch/

				# if source path is empty nothing will done
				sourcepath = {$pdfpath}

				dontCopyIfExists = 1

				createFilename = {$filenametouse}.jpg

				# filename will be trimmed
				trimFilename = 1

				# continue if filename is empty
				skipEmptyFilename = 1

				# if set then only this filetypes are allowed
				allowedFiletypes = jpg,jpeg,gif,png±

				destinationpath = /fileadmin/user_upload/events/

				# return just the filename if you have ie. a TCA file field
				returnJustFilename = 0

				# slow but supports curl/proxy
				//useGetURL = 1

				# enables splitting
				// split = ,

				# overwrithe if exists
				overwrithe = 1
			}

			# creates a dam realtion and index file
			# attention this has to be done in a secon import as you have the uid of the record in the storage
			2 = dam
			2 {
				# semicolon separated list of files
				singleFile = {$currentFieldValue}

				# field name to relate to
				fieldNameToRelateTo = picture

				selectUidToRelateTo = SELECT uid FROM tx_xxx WHERE somereferencefield = {$referencefromimportsomefieldXXX} AND deleted=0

				# table name to relate to
				tableNameToRelateTo = tx_xxx

				# data to put into DAM table
				damPresetData {
					copyright = {$img1_credit}
				}
			}
		}
	}

	1n_relation_example {
		required = 1

		stack {
			1 = TEXT
			1.value = {$Ort}

			# 1 to n relation
			2 = relation
			2 {
				# table of the related records
				relationtable = tx_xyz

				# field to search for and where the value is saved
				# if the searched one is missing
				relationfield = fieldxyz

				# additional condition for searching the relation record
				relationaddwhere = AND sys_language_uid=0

				# pid of the related records
				# (if not set global import pid will be taken)
				relationpid = 208

				# add record if not found
				addIfMissing = 1
			}
		}
	}

	mm_relation_example {
		required = 1

		stack {
			1 = TEXT
			1.value = {$1}

			# m to m relation
			2 = mm
			2 {
				# split value for relation
				split = ,

				# split alternative for newline
				//split.newline = 1

				# possibility to restrict the relation to a position within
				# the value
				//splitUseOnlyPosition = 1

				# table to relate to
				table = tx_cabagshop_category

				# field to relate to and add value if record is missing
				tablekeyfield = catalogkey

				# set to 1 so search in the tablekeyfield with LIKE %value%
				tablekeyfieldlike = 0

				# pid for the relation records
				# (if not set global import pid will be taken)
				tablepid = 109

				# ignore the pid
				ignoreTablepid = 0

				# add record if not found
				addIfMissing = 1

				# table for the mm relation records
				mmtable = tx_cabagshop_article_category_mm

				# field in the mm table which relates to the related table
				mmtablefield = uid_foreign
			}
		}
	}

	# can be used for usergroup relation in fe_users if they have to be generated
	commaseparated_mm_relation_example {
		required = 1

		stack {
			1 = TEXT
			1.value = {$1}

			# m to m relation
			2 = commaseparated_mm
			2 {
				# split value for relation
				split = ,

				# split alternative for newline
				//split.newline = 1

				# possibility to restrict the relation to a position within
				# the value
				//splitUseOnlyPosition = 1

				# glue between the relations (normaly commaseparated)
				relationglue = ,

				# table to relate to
				table = tx_cabagshop_category

				# field to relate to and add value if record is missing
				tablekeyfield = catalogkey

				# set to 1 so search in the tablekeyfield with LIKE %value%
				tablekeyfieldlike = 0

				# pid for the relation records
				# (if not set global import pid will be taken)
				tablepid = 109

				# add record if not found
				addIfMissing = 1
			}
		}
	}
	
	commaseparated_local_example {
		required = 1
		
		stack {
			1 = TEXT
			1.value = {$1}+{$2}+{$3}+{$4}

			# m to m relation
			2 = commaseparated_local
			2 {
				# split value for relation
				split1 = ;
				split2 = +
				
								
				# table to relate to
				table = fe_groups
				
				# field to relate to and add value if record is missing
				tablekeyfield = title
				
				tablekeyfields = title;title_fr;title_it;title_en
				
				
				
				# pid for the relation records
				# (if not set global import pid will be taken)
				tablepid = 157
				
			}
		}
	}

	save_to_raw_row_example {
		stack {

			1 = TEXT
			1.value = $someField

			2 = save_to_raw_row
			2.field = random_field_name

			3 = preg_replace
			3.from = /{$random_field_name}/
			3.to = {$someOtherField}
		}
	}

	copypage_example {
		stack {
			1 = select
			1.sql = SELECT uid FROM pages WHERE pid = 1212 AND deleted = 0 AND hidden = 0

			2 = copypage
			2 {
				# destinationpid, otherwise its {$currentFieldValue}
				# destinationpid = 1287

				sourcepid = 345

				# Sets the number of branches on a page tree to copy.
				copyTree = 0

				# clear page cache afterwards
				clearPageCache = 0
			}

			3 = copypage
			3 {
				# destinationpid, otherwise its {$currentFieldValue}
				# destinationpid = 1287

				sourcepid = 51

				# Sets the number of branches on a page tree to copy.
				copyTree = 0

				# clear page cache afterwards
				clearPageCache = 0
			}
		}
	}

	cobj_example {
		stack {
			1 = cobj

			# either simluatePid or defaultPid will be taken
			# needed for typoscript
			1.simulatePid = 196438

			1.config = TEXT
			1.config {
				typolink {
					parameter = {$uid}
					additionalParams = &L=3
					returnLast = url
				}
			}

			2.config = COA
			2.config {
				10 = TEXT
				10 {
					value = Hello
				}

				20 = TEXT
				20 {
					value = World!
				}
			}
		}
	}

	text_transform_example {
		stack {
			# These transfrmations do not need any special additional configuration, they will simply take the current value of the field and transform it

			1 = TEXT
			1.value = {$SomeValue}

			# bin2hex transformation
			2 = bintohex

			# bindec transformation
			3 = bindec

			# strtolower
			4 = strtolower

			# floatval example
			5 = floatval

			# convert field content with htmlspecialchars and flag ENT_NOQUOTES
			6 = htmlspecialchars

			# convert field content with htmlspecialchars_decode and flag ENT_NOQUOTES
			7 = htmlspecialcharsdecode

			# convert field content with html_entities_decode
			8 = htmlentitiesdecode
			8 {
				# directly assign some value
				value = {$SomeOtherValue}

				# html_entities_decode(..., <here>, ...)
				# default is ENT_COMPAT | ENT_HTML401
				options = ENT_COMPAT | ENT_HTML401

				# encoding
				encoding = UTF-8
			}
		}
	}

	# This fieldproc take the complete raw row and iterates over it to create a NoSQL field value
	jsonrange_example {
		stack {
			1 = jsonrange
			1 {
				# from where the fields should be stored as json key:value pairs
				from = 20

				# to where the fields should be stored as json key:value paires
				to =
			}
		}
	}
	
	#Transform value to SQL IN format with split option
	transform_to_sql_in_example {
		stack {
			
			1 = TEXT
			1.value = {$org1;lang-de};{$org2;lang-de} 
			
			2 = transform_to_sql_in
			2 {
				split = ; 
			}

			3 = select
			3.sql = SELECT  GROUP_CONCAT(uid) FROM fe_groups WHERE (title IN ({$currentFieldValue}) AND deleted = 0 

		}
	}
	
	# can be used for usergroup relation in fe_users if they have to be generated
	add_to_commaseparated_list_example {
		required = 1
				
		stack {
			
			1 = TEXT
			1.value =  {$org1}+{$org2}
			
			3 = add_to_commaseparated_list
			3 {
				# glue between the relations (normaly commaseparated)
				split = +
				
				# add record if not found
				addIfMissing = 1
				
				# table to relate to
				table = fe_groups
								
				# field to relate to and add value if record is missing
				tablekeyfield = title
			}
		}
	}
}
';

if(!function_exists('tx_cabagimport_showavailableOptions')) {
	function tx_cabagimport_showavailableOptions() {
		return '
			<div style="height:500px; width:600px; overflow:scroll;">
				<pre>'.htmlspecialchars(\Cabag\CabagImport\Utility\AvailableOptionsUtility::getAvailableOptions()).'</pre>
			</div>';
	}
}

$GLOBALS['TCA']['tx_cabagimport_config'] = array (
	'ctrl' => $TCA['tx_cabagimport_config']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,title,configuration'
	),
	'feInterface' => $TCA['tx_cabagimport_config']['feInterface'],
	'columns' => array (
		'hidden' => array (
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cabag_import/locallang_db.xml:tx_cabagimport_config.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'configuration' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:cabag_import/locallang_db.xml:tx_cabagimport_config.configuration',
			'config' => Array (
				'type' => 'text',
				'cols' => '300',
				'rows' => '500',
				'default' => \Cabag\CabagImport\Utility\AvailableOptionsUtility::getAvailableOptions(),
			),
			'defaultExtras' => 'fixed-font : enable-tab',
		),
		'availableOptions' => array(
			'exclude' => 0,
			'label' =>  'LLL:EXT:cabag_import/locallang_db.xml:tx_cabagimport_config.availableOptions',
			'config' => Array (
				'type' => 'user',
				'userFunc' => 'tx_cabagimport_showavailableOptions',
				'noTableWrapping' => 0,
			),
		)
	),
	'types' => array (
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, configuration, availableOptions')
	),
	'palettes' => array (
		'1' => array('showitem' => '')
	)
);
?>
