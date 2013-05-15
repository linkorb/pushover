<?php

namespace LinkORB\Pushover;
use LinkORB\Pushover\Message;

class RunTest extends \PHPUnit_Framework_TestCase {

	private $config = array();
	private $message;


	/**
	 * Utility function for retrieving Pushover credentials from .ini file
	 */
	private function loadIni() {
		$ini_filename = __DIR__ . '/../../../config.ini';
		if (!file_exists($ini_filename)) {
			throw new \Exception('Ini file not found: ' . $ini_filename);
		}
		
		$this->config = parse_ini_file($ini_filename, true);

		// Sanity checks
		if ($this->config['pushover']['token'] == '') {
			throw new \Exception('No pushover token');
		}
		
		if ($this->config['pushover']['user_key'] == '') {
			throw new \Exception('No user key');
		}
	}

	/**
	 * Set up reusable test object $this->message
	 */
	protected function setUp() {
		$this->loadIni();
		$this->message = new Message(
			$this->config['pushover']['token'], 
			$this->config['pushover']['user_key'], 
			$this->config['pushover']['message']);
	}


	function providerSetPriorityInvalidArguments() {
		return array(
			array('abc', null, null),
			array(-3, null, null),
			array(3, null, null),
			array(100, null, null),
			array(2, 5, null),
			array(1, -60, null));
	}
	/**
	 * Test sanity checks by passing in different invalid arguments
	 *
	 * @dataProvider providerSetPriorityInvalidArguments
	 * @expectedException InvalidArgumentException
	 */
	function testSetPriorityInvalidArguments($priority, $retry, $expire) {
		$this->message->SetPriority($priority, $retry, $expire);
	}
	
	
	function providerSetPriorityValidArguments() {
		return array(
			array(-1, null, null),
			array(0, null, null),
			array(1, null, null),
			array(2, 60, 86400-1),
			array(2, 120, 120),
			array(1, 60, 120));
	}

	/**
	 * These should all pass, no exceptions
	 *
	 * @dataProvider providerSetPriorityValidArguments
	 */
	function testSetPriorityValidArguments($priority, $retry, $expire) {
		$this->message->SetPriority($priority, $retry, $expire);
	}

	/**
	 * @dataProvider providerSetPriorityValidArguments
	 * @expectedException InvalidArgumentException
	 */
	function testSetMessageInvalidArguments() {
		$this->message->setMessage(null, null);
		$this->message->setMessage(-1, 3);
	}
	
	function testSetMessageValidArguments() {
		$this->message->setMessage('test title', 'test message');
	}

	/**
	 * Test sending an actual message 
	 */
	function testSendMessage() {

		$this->message->setPriority(2, 60, 3600);
		$this->message->setUrl('http://www.linkorb.com/news', 'Cool blog');
		$this->assertTrue($this->message->send());
	}

	/**
	 * Test sending an actual message with wrong settings.
	 */
	function testSendFailedMessage() {

		$message = new Message(
			$this->config['pushover']['token'] . 'brokentoken',
			$this->config['pushover']['user_key'],
			$this->config['pushover']['message']);

		$message->setPriority(2, 60, 3600);
		$message->setUrl('http://www.linkorb.com/news', 'Cool blog');
		$this->assertFalse($message->send());
	}
}
