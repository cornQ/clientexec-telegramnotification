<?php
/**
 * PluginTelegramnotification
 * This plugin is used to add notification functionality to send telegram messages.
 *
 * @package    clientexec-telegram-snapin
 * @license    GPLv3 or later
 * @author     Aryan Jasala
 * @link       https://github.com/aryanjasala/clientexec-telegram-snapin
 * @version    1.0.0
 */

require_once 'modules/admin/models/SnapinPlugin.php';
require_once 'plugins/snapin/telegramnotification/class-telegram.php';

class PluginTelegramnotification extends SnapinPlugin
{

	public $title = 'Telegram Notification';

	public $listeners = [
		[ 'Ticket-ReplyByCustomer', 'replyTicketCallback'],
		[ 'Invoice-Create', 'newInvoiceCallback' ],
		[ 'Invoice-Paid', 'invoicePaidCallback' ],
		[ 'Order-NewPackage', 'newOrderCallback' ],
		[ 'UserPackage-Activate', 'packageActivateCallback' ]
	];

	/**
	 * @return array
	 */
	public function getVariables()
	{
		$variables = [
			'Plugin Name' => [
				'type' => 'hidden',
				'description' => 'Used to add notification functionality to send telegram messages.',
				'value' => 'Telegram Notification'
			],
			'Telegram Token' => [
				'type' => 'text',
				'description' => 'Enter the telegram token here',
				'value' => '',
			],
			'Notify_Ticket_Reply' => [
				'type' => 'yesno',
				'description' => 'Notify when a ticket is replied by customer',
				'value' => '1',
			],
			'Notify_Invoice_Create' => [
				'type' => 'yesno',
				'description' => 'Notify when a new invoice is created',
				'value' => '1',
			],
			'Notify_Invoice_Paid' => [
				'type' => 'yesno',
				'description' => 'Notify when an invoice is paid',
				'value' => '1',
			],
			'Notify_Order_NewPackage' => [
				'type' => 'yesno',
				'description' => 'Notify when a new order is placed',
				'value' => '1',
			],
			'Notify_UserPackage_Activate' => [
				'type' => 'yesno',
				'description' => 'Notify when a new package is activated',
				'value' => '1',
			],

		];

		if ( ! empty( $this->settings->get( 'plugin_telegramnotification_Telegram Token' ) ) ) {
			$variables['Chat ID'] = [
				'type' => 'text',
				'description' => 'Enter the chat ID here. If you don\'t know send one message to your bot and check it <a href="https://api.telegram.org/bot' . $this->settings->get( 'plugin_telegramnotification_Telegram Token' ) . '/getUpdates" target="_blank">here</a>',
				'value' => '',
			];
		}

		return $variables;
	}

	/**
	 * New order callback.
	 *
	 * @param array $params Callback params.
	 *
	 * @return void
	 */
	public function newOrderCallback( $e ) {

		if ( empty( $this->settings->get( 'plugin_telegramnotification_Notify_Order_NewPackage' ) ) || 1 != $this->settings->get( 'plugin_telegramnotification_Notify_Order_NewPackage' ) ) {
			return;
		}

		if (is_array($e)) {
			$event = $e;
		} else {
			$event = $e->getParams();
		}

		$params = $event;

		$token = $this->settings->get( 'plugin_telegramnotification_Telegram Token' );
		$chat_id = $this->settings->get( 'plugin_telegramnotification_Chat ID' );

		if ( empty( $token ) || empty( $chat_id ) ) {
			return;
		}

		$telegram = new Telegram( $token, $chat_id );

		$telegram->send_message( 'New order received from user ID #: ' . $params['userid'] );

	}

	/**
	 * New invoice callback.
	 *
	 * @param array $e Callback params.
	 *
	 * @return void
	 */
	public function newInvoiceCallback( $e ) {

		if ( empty( $this->settings->get( 'plugin_telegramnotification_Notify_Invoice_Create' ) ) || 1 != $this->settings->get( 'plugin_telegramnotification_Notify_Invoice_Create' ) ) {
			return;
		}

		if (is_array($e)) {
			$event = $e;
		} else {
			$event = $e->getParams();
		}

		$params = $event;

		$invoice    = $params['invoice'];
		$invoice_id = $params['invoiceId'];
		$subtotal   = $invoice->m_SubTotal;
		$due		= $invoice->m_BalanceDue;
		$user_id    = $invoice->m_UserID;

		$token = $this->settings->get( 'plugin_telegramnotification_Telegram Token' );
		$chat_id = $this->settings->get( 'plugin_telegramnotification_Chat ID' );

		if ( empty( $token ) || empty( $chat_id ) ) {
			return;
		}

		$telegram = new Telegram( $token, $chat_id );

		$telegram->send_message( 'New invoice generated for user ID #: ' . $user_id . PHP_EOL . 'Invoice ID: ' . $invoice_id . PHP_EOL . 'Subtotal: ' . $subtotal . PHP_EOL . 'Due: ' . $due );

	}

	/**
	 * Invoice paid callback.
	 *
	 * @param array $e Callback params.
	 *
	 * @return void
	 */
	public function invoicePaidCallback( $e ) {

		if ( empty( $this->settings->get( 'plugin_telegramnotification_Notify_Invoice_Paid' ) ) || 1 != $this->settings->get( 'plugin_telegramnotification_Notify_Invoice_Paid' ) ) {
			return;
		}

		if (is_array($e)) {
			$event = $e;
		} else {
			$event = $e->getParams();
		}

		$params = $event;

		$invoice_id = $params['invoiceId'];

		$token = $this->settings->get( 'plugin_telegramnotification_Telegram Token' );
		$chat_id = $this->settings->get( 'plugin_telegramnotification_Chat ID' );

		if ( empty( $token ) || empty( $chat_id ) ) {
			return;
		}

		$telegram = new Telegram( $token, $chat_id );

		$telegram->send_message( 'Invoice ID #' . $invoice_id . ' has been paid. ✅' );

	}

	/**
	 * Reply ticket callback.
	 *
	 * @param object $e Callback params.
	 *
	 * @return void
	 */
	public function replyTicketCallback( $e ) {

		if ( empty( $this->settings->get( 'plugin_telegramnotification_Notify_Ticket_Reply' ) ) || 1 != $this->settings->get( 'plugin_telegramnotification_Notify_Ticket_Reply' ) ) {
			return;
		}

		if (is_array($e)) {
			$event = $e;
		} else {
			$event = $e->getParams();
		}

		$params = $event;

		$user        = $params['user'];
		$user_fields = $user->fields;
		$firstname   = $user_fields['firstname'];
		$lastname    = $user_fields['lastname'];
		$email       = $user_fields['email'];
		$ticketid    = $params['ticketid'];
		$message     = $params['message'];

		$token = $this->settings->get( 'plugin_telegramnotification_Telegram Token' );
		$chat_id = $this->settings->get( 'plugin_telegramnotification_Chat ID' );

		if ( empty( $token ) || empty( $chat_id ) ) {
			return;
		}

		$telegram = new Telegram( $token, $chat_id );

		$telegram->send_message( '-- Ticket Reply --' . PHP_EOL . 'Ticket ID: ' . $ticketid . PHP_EOL . ' From: ' . $firstname . ' ' . $lastname . ' (' . $email . ' ) ' . PHP_EOL . ' Message: ' . $message );

	}

	/**
	 * Package activate callback.
	 *
	 * @param object $e Callback params.
	 *
	 * @return void
	 */
	public function packageActivateCallback( $e ) {

		if ( empty( $this->settings->get( 'plugin_telegramnotification_Notify_UserPackage_Activate' ) ) || 1 != $this->settings->get( 'plugin_telegramnotification_Notify_UserPackage_Activate' ) ) {
			return;
		}

		if (is_array($e)) {
			$event = $e;
		} else {
			$event = $e->getParams();
		}

		$params = $event;

		$userPackageId    = $params['userPackageId'];
		$userPackage      = $params['userPackage'];
		$packageGroupInfo = $userPackage->packageGroupInfo;
		$productname	  = $packageGroupInfo['productname'];
		$productgroupname = $packageGroupInfo['productgroupname'];

		$token = $this->settings->get( 'plugin_telegramnotification_Telegram Token' );
		$chat_id = $this->settings->get( 'plugin_telegramnotification_Chat ID' );

		if ( empty( $token ) || empty( $chat_id ) ) {
			return;
		}

		$telegram = new Telegram( $token, $chat_id );

		$telegram->send_message( 'New package activated with ID #: ' . $userPackageId . PHP_EOL . 'Package Name: ' . $productname . PHP_EOL . 'Package Group Name: ' . $productgroupname );
	}

}
