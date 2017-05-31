<?php
namespace Cabag\CabagImport\Domain\Model;

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