<?php
namespace Cabag\CabagImport\Logger;
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Sonja Scholz <ss@cabag.ch>
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

use \Psr\Log\LogLevel;

abstract class AbstractLogger implements LoggerInterface {
	/** 
	 * initialize
	 *
	 * @param object handler object
	 * @param array typoscript configuration for this loghandler
	 * @return boolean if logged
	 */
	abstract public function main($source_object, $conf);
	
	/** 
	 * Log string
	 *
	 * @param string message
	 * @param int type -> 0 error, 1 warning, 2 info
	 * @return boolean if logged
	 */
	abstract public function setMessage($message, $type, $data = array());
	
	/** 
	 * get messages 
	 *
	 * @return array with all log messages
	 */
	abstract public function getMessages();
	
	/** 
	 * finish everything
	 *
	 * @return boolean state
	 */
	abstract public function finish();
	
	/**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array()) {
    	$this->setMessage($message, -3, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array()) {
    	$this->setMessage($message, -2, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array()) {
    	$this->setMessage($message, -1, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array()) {
    	$this->setMessage($message, 0, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array()) {
    	$this->setMessage($message, 1, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array()) {
    	$this->setMessage($message, 2, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array()) {
    	$this->setMessage($message, 2, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array()) {
    	$this->setMessage($message, 0, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array()) {
    	switch($level) {
    		case LogLevel::EMERGENCY:
    			$this->emergency($message, $context);
    			break;
    		case LogLevel::ALERT:
    			$this->alert($message, $context);
    			break;
    		case LogLevel::ERROR:
    			$this->error($message, $context);
    			break;
    		case LogLevel::WARNING:
    			$this->warning($message, $context);
    			break;
    		case LogLevel::NOTICE:
    			$this->notice($message, $context);
    			break;
    		case LogLevel::INFO:
    			$this->info($message, $context);
    			break;
    		case LogLevel::DEBUG:
    		default:
    			$this->debug($message, $context);
    			break;
    	}
    }
}
