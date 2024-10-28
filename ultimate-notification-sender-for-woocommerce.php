<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
* Plugin Name: Ultimate Notification Sender for WooCommerce
* Plugin URI: https://anothermonk.com/plugins/ultimate-notification-sender-for-woocommerce
* Description: The Ultimate Notification Sender for WooCommerce allows store owners to receive instant notifications on Telegram for various order statuses, including New Orders, Order Processing, Order Completed, Order Cancelled, and Order Refunded. Stay updated on your store's activities in real-time with customizable message formats.
* Version: 1.0.1
* Author: Masum Billah
* Author URI: https://masum.anothermonk.com/
* License: GPL-2.0+
* License URI: http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain: unsfw
* Domain Path: /languages
*/

class Unsfw_Base_Init {

    // Constructor
    public function __construct() {
        include_once('admin/ultimate-woo-admin-page.php'); // Settings Page
        $options = get_option('unsfw_settings');

        // Check if both API token and chat ID are set
        if (isset($options['enable_notifications']) && $options['enable_notifications'] == 1) {
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
    protected function Send_Telegram_Notification($message) {
        // Retrieve settings option
        $options   = get_option('unsfw_settings');
        $api_token = $options['telegram_api_token'] ?? '';
        $chat_id   = $options['telegram_chat_id'] ?? '';
    
        // Check if notifications are enabled
        if (isset($options['enable_notifications']) && isset($api_token) && isset($chat_id)) {
            $payload = array(
                'chat_id' => $chat_id,
                'text' => $message
            );

            $response = wp_remote_post("https://api.telegram.org/bot{$api_token}/sendMessage", array(
                'method' => 'POST',
                'body' => $payload,
            ));

            if (is_wp_error($response)) {
                error_log("Telegram API request failed: " . $response->get_error_message());
            }
        } else {
            error_log("Unable to send Telegram message. Notifications are disabled or API token/chat ID is missing.");
        }
    }
    
}

// Add a settings link next to the activate button
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'unsfw_add_settings_link');

function unsfw_add_settings_link($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=ultimate-woo-sender-settings') . '">' . __('Settings', 'unsfw') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

new Unsfw_Base_Init();
