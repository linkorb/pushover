<?php
/*
 * This file is part of the LinkORB Pushover package.
 *
 * (c) LinkORB <j.faassen@linkorb.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace LinkORB\Pushover;

/**
 * Pushover Message class
 *
 * Instantiate this class to create a message for Pushover, and send it.
 *
 * @author Joost Faassen <j.faassen@linkorb.com>
 */

class Message {

	/**
	 * The application token, received after registering your application
	 */
	private $token = null;

	/**
	 * The key of the user you wish to send a message to
	 */
	private $userkey = null;

	/** 
	 * The actual message body (plain text)
	 */
	private $message = null;

	/**
	 * Optional devicename of the user to send messages to specifed device only
	 */
	private $devicename = null;

	/**
	 * The message title, as shown in the push message window
	 */
	private $title = null;

	/**
	 * An optional url to more information about this message
	 */
	private $url = null;

	/**
	 * An optional title for the $this->url
	 */
	private $url_title = null;
	
	/**
	 * Message priority:
	 *  
	 *  * -1 : Low priority (no sound / vibrarion)
	 *  *  0 : Normal priority (sound / vibration based on quiet hours)
	 *  * +1 : High priority (ignore quiet hours, always make sound + vibration)
	 *  * +2 : Emergency (like High, but additionally requires a user confirmation to stop repeating)
	 *
	 *  Full details here:
	 *  	https://pushover.net/api#priority
	 */
	private $priority = 0;

	/**
	 * a Unix timestamp of your message's date and time to display to the user, 
	 * rather than the time your message is received by our API
	 */
	private $timestamp = null;

	/**
	 * Which sound to play. 
	 *
	 * Options:
	 *	https://pushover.net/api#sounds
	 */

	private $sound = null;
	
	/**
	 * Url to the pushover_message api.
	 *
	 * Static, normally does not require to be changed
	 */
	private $pushover_messages_url = "https://api.pushover.net/1/messages.json";

	
	/**
	 * Message constructor:
	 *
	 * @param string $token		The application token
	 * @param string $userkey	Target user key
	 * @param string $message	Message content (plain text)
	 */
	public function __construct($token = null, $userkey = null, $message = null) {
		$this->token = $token;
		$this->userkey = $userkey;
		$this->message = $message;
	}

	/**
	 * Set message priority
	 *
	 * @param int $priority priority-level (-1, 0, 1, or 2)
	 * @param int $retry delay between retries in seconds (min 30, max 86400)
	 * @return boolean true on success
	 */
	public function setPriority($priority, $retry = null, $expire = null) {
		// Sanity checks
		if (!is_int($priority)) throw new \InvalidArgumentException('priority needs to be a valid integer between -1 and 2');
		if ($retry) {
			if (!is_int($retry)) return \InvalidArgumentException('retry is not a number');
			if (($retry<30) || ($retry>86400)) {
				throw new \InvalidArgumentException('retry needs to be between 30 and 86400');
			}
			$this->retry = $retry;
		}
		if ($expire) {
			if (!is_int($expire)) return \InvalidArgumentException('expire is not a number');
			if (($expire<0) || ($expire>86400)) {
				\InvalidArgumentException('expire between 0 and 86400');
			}
			$this->expire = $expire;
		}
		if ($priority == 2) {
			if ((!$retry) || (!$expire)) {
				throw new \InvalidArgumentException('retry and expire need to be provided for priority 2');
			}

		}

		if (($priority<-1) || ($priority>2)) {
			throw new \InvalidArgumentException('priority is outside of valid rangeneeds to be a valid integer between -1 and 2');
		}
		
		$this->priority = $priority;
		return true;
	}
	
	/**
	 * Set message priority
	 *
	 * @param int $priority priority-level (-1, 0, 1, or 2)
	 */
	public function setUrl($url, $url_title = null) {
		if ($url == '') throw new \InvalidArgumentException('url parameter is empty');
		$this->url = $url;
		$this->url_title = $url_title;
	}


	/**
	 * Set message 
	 *
	 * @param string $title title of the push message
	 * @param string $message actual message content (plain text)
	 */
	public function setMessage($title, $message) {
		if ($message == '') throw new \InvalidArgumentException('message parameter is empty');
		$this->title = $title;
		$this->message = $message;
	}

	/**
	 * Send this message
	 *
	 * @return bool Indicates the message was send successful.
	 */
	public function send() {
		$curl_handle = curl_init();

		$postfields=array();
		$postfields['token'] =  $this->token;
		$postfields['user'] = $this->userkey;
		$postfields['message'] = $this->message;
		$postfields['url'] = $this->url;
		$postfields['priority'] = $this->priority;
		$postfields['url_title'] = $this->url_title;
		$postfields['retry'] = $this->retry;
		$postfields['expire'] = $this->expire;

		curl_setopt_array($curl_handle,
			array(
				CURLOPT_URL => $this->pushover_messages_url,
				CURLOPT_POSTFIELDS => $postfields,
                CURLOPT_RETURNTRANSFER => true,
			));
		$response = curl_exec($curl_handle);
		curl_close($curl_handle);

		if (isset($response['status']) && $response['status'] == 1) {
			return true;
		}
		return false;
	}
}
