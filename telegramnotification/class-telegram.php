<?php
/**
 * This will handle the telegram integration related functionalities.
 *
 * @package    clientexec-telegram-snapin
 * @license    GPLv3 or later
 * @version    1.0.0
 * @author     Aryan Jasala
 */

/**
 * Class Telegram will contain methods related to interact with telegram.
 */
class Telegram {

	/**
	 * Telegram API URL.
	 *
	 * @var string
	 */
	private $url = 'https://api.telegram.org/bot';

	/**
	 * Telegram token.
	 *
	 * @var string
	 */
	private $token;

	/**
	 * Telegram chat id.
	 *
	 * @var string
	 */
	private $chat_id;

	/**
	 * Telegram constructor.
	 *
	 * @param string $token Telegram token.
	 * @param string $chat_id Telegram chat id.
	 */
	public function __construct( $token, $chat_id ) {

		$this->token   = $token;
		$this->chat_id = $chat_id;
	}

	/**
	 * Send message to telegram.
	 *
	 * @param string $message Message to send.
	 * @return void
	 */
	public function send_message( $message ) {

		$url = $this->url . $this->token . '/sendMessage?chat_id=' . $this->chat_id . '&text=' . urlencode( $message );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_exec( $ch );
		curl_close( $ch );

	}


}
