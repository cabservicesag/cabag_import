<?php
namespace Cabag\CabagImport\Domain\Model;

/***************************************************************
*  Copyright notice
*
*  (c) 2017 Tizian Schmidlin <st@cabag.ch>
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

class Config extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * The configuration title
	 * @var string
	 */
	protected $title;

	/**
	 * The configuration itself
	 * @var string
	 */
	protected $configuration;

	/**
	 * Set the title and return self
	 * @var string $title
	 * @return $this
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	/**
	 * Return the title
	 * @return @sting $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Set the configuration and return self
	 * @var string $configuration
	 * @return $this
	 */
	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
		return $this;
	}

	/**
	 * Return the configuration
	 * @return @sting
	 */
	public function getConfiguration() {
		return $this->configuration;
	}
}
