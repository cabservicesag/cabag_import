<?php

namespace Cabag\CabagImport\Configuration;

interface AvailableFieldprocInterface extends \TYPO3\CMS\Core\SingletonInterface{
	/**
	 * Get the description of the available fieldproc
	 *
	 * @return string
	 */
	public function getDescription();

	/**
	 * Get the fieldproc example
	 *
	 * @return string
	 */
	public function getConfigurationExample();
}
