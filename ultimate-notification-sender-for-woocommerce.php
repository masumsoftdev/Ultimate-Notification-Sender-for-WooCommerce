<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/*
* Plugin Name: Ultimate Notification Sender for WooCommerce
* Plugin URI:        https://anothermonk.com/plugins/ultimate-notification-sender-for-woocommerce
* Description:       The Ultimate Notification Sender for WooCommerce allows store owners to receive instant notifications on Telegram for various order statuses, including New Orders, Order Processing, Order Completed, Order Cancelled, and Order Refunded. Stay updated on your store's activities in real-time with customizable message formats.
* Version:           1.0.1
* Author:            Masum Billah
* Author URI:        https://masum.anothermonk.com/
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       unsfw
* Domain Path:       /languages
*/

class Unsfw_Telegram_Order_Notifications {
    // Telegram API token and chat ID
    private $api_token;
    private $chat_id;

    // Constructor
    public function __construct() {
        $options = get_option('unsfw_settings');
        $this->api_token = isset($options['telegram_api_token']) ? esc_attr($options['telegram_api_token']) : '';
        $this->chat_id = isset($options['telegram_chat_id']) ? esc_attr($options['telegram_chat_id']) : '';

        // Check if both API token and chat ID are set
        if (!empty($this->api_token) && !empty($this->chat_id)) {
            add_action('plugins_loaded', array($this, 'unsfw_load_plugin_textdomain'));
            // Include notification files
            $this->include_notification_files();
        } else {
            // Log a warning or set a default action if the API token or chat ID is not set
            error_log("Telegram API token or chat ID is not set. Notifications will not be sent.");
        }
    }

    public function unsfw_load_plugin_textdomain() {
        load_plugin_textdomain('unsfw', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    private function include_notification_files() {
        include_once('inc/unsfw-order-pending-notification.php'); // Pending Order Notification
        include_once('inc/unsfw-new-order-notification.php'); // New Order Notification
        include_once('inc/unsfw-order-processing-notification.php'); // Order Processing Notification
        include_once('inc/unsfw-order-completed-notification.php'); // Order Completed Notification
        include_once('inc/unsfw-order-cancelled-notification.php'); // Order Cancelled Notification
        include_once('inc/unsfw-order-refunded-notification.php'); // Order Refunded Notification
    }

    // Send Telegram message
    protected function unsfw_send_telegram_message($message) {
        // Check if both API token and chat ID are available before sending a message
        if (!empty($this->api_token) && !empty($this->chat_id)) {
            $payload = array(
                'chat_id' => $this->chat_id,
                'text' => $message
            );

            $response = wp_remote_post("https://api.telegram.org/bot{$this->api_token}/sendMessage", array(
                'method' => 'POST',
                'body' => $payload,
            ));

            if (is_wp_error($response)) {
                error_log("Telegram API request failed: " . $response->get_error_message());
            }
        } else {
            error_log("Unable to send Telegram message. API token or chat ID is missing.");
        }
    }
}

new Unsfw_Telegram_Order_Notifications();
