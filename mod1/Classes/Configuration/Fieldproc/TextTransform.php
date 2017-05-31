<?php
namespace Cabag\CabagImport\Configuration\Fieldproc;
use Cabag\CabagImport\Configuration\AvailableFieldprocInterface;

class TextTransform implements AvailableFieldprocInterface {
	
	/**
	 * Get the description of the available configuration
	 *
	 * @return string
	 */
	public function getDescription() {
		return 'These transfrmations do not need any special additional configuration, they will simply take the current value of the field and transform it';
	}
	
	/**
	 * Get the configuration example
	 * 
	 * @return string
	 */
	public function getConfigurationExample() {
		return '
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
	}';
	}
}