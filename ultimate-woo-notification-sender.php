<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
* Plugin Name: Ultimate Woo Notification Sender
* Description: Sends new order notifications to Telegram.
* Plugin URI:        https://anothermonk.com/plugins/ultimate-woo-notification-sender
* Description:       The plugin Ultimate Woo Notification Sender, that will help you to sends Woocommerce Notifications to external Platforms like Telegram.
* Version:           1.0.0
* Author:            Masum Billah
* Author URI:        https://masum.anothermonk.com/
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       ultimate-woo-notification-sender
* Domain Path:       /languages
*/


// Define Telegram API token and chat ID
define('TELEGRAM_API_TOKEN', 'YOUR_TELEGRAM_API_TOKEN');
define('TELEGRAM_CHAT_ID', 'YOUR_TELEGRAM_CHAT_ID');

class Uwns_Telegram_Order_Notifications {
    // Telegram API token and chat ID
    private $api_token;
    private $chat_id;

    // Constructor
    public function __construct() {
        $options = get_option('uwns_settings');
        $this->api_token = esc_attr($options['telegram_api_token'] ?? '');
        $this->chat_id = esc_attr($options['telegram_chat_id'] ?? '');

        register_activation_hook(__FILE__, array($this, 'uwns_plugin_activation'));
        add_action('woocommerce_new_order', array($this, 'uwns_send_order_notification'));
        add_action('plugins_loaded', array($this, 'uwns_load_plugin_textdomain'));

        include_once('admin/ultimate-woo-admin-page.php');
    }

    public function uwns_load_plugin_textdomain() {
        load_plugin_textdomain('ultimate-woo-notification-sender', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public function uwns_plugin_activation(){
        if (!class_exists('WooCommerce')) {
            wp_redirect(admin_url('plugin-install.php?s=woocommerce&tab=search&type=term'));
            exit;
        }
    }

    // Send order notification
    public function uwns_send_order_notification($order_id) {
        // Get order object
        $order = wc_get_order($order_id);

        // Compose message
        $message = "New order received!\n";
        $message .= "Order ID: " . $order->get_id() . "\n";
        $message .= "Customer: " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . "\n";
        $message .= "Total: " . $order->get_total() . " " . $order->get_currency();

        // Send notification to Telegram
        $this->uwns_send_telegram_message($message);
    }

    private function uwns_send_telegram_message($message) {
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

new Uwns_Telegram_Order_Notifications();
