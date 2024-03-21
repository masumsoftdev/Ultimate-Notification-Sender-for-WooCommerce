<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
* Plugin Name: Ultimate Notification Sender for WooCommerce
* Plugin URI:        https://anothermonk.com/plugins/ultimate-notification-sender-for-woocommerce
* Description:       The plugin Ultimate Notification Sender for WooCommerce, that will help you to sends Woocommerce Notifications to external Platforms like Telegram. For the time being, sends new order notifications to Telegram for Pending Order.
* Version:           1.0.0
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
        $this->api_token = esc_attr($options['telegram_api_token'] ?? '');
        $this->chat_id = esc_attr($options['telegram_chat_id'] ?? '');

        add_action('woocommerce_order_status_pending', array($this, 'unsfw_send_order_notification'), 10, 1);
        add_action('plugins_loaded', array($this, 'unsfw_load_plugin_textdomain'));

        include_once('admin/ultimate-woo-admin-page.php');
    }

    public function unsfw_load_plugin_textdomain() {
        load_plugin_textdomain('unsfw', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    // Send order notification
    public function unsfw_send_order_notification($order_id) {
        // Get order object
        $order = wc_get_order($order_id);

        // Compose message
        $message = "New pending order received!\n";
        $message .= "Order ID: " . $order->get_id() . "\n";
        $message .= "Customer: " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . "\n";
        $message .= "Total: " . $order->get_total() . " " . $order->get_currency();
        // Send notification to Telegram
        $this->unsfw_send_telegram_message($message);
    }

    private function unsfw_send_telegram_message($message) {
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
    }
}

new Unsfw_Telegram_Order_Notifications();

