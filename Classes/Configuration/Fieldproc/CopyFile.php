<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class CopyFile implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'Copy a file from a local or remote place to a local folder';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
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
	}';
	}
}