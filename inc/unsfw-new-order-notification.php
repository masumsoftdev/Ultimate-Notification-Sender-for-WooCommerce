<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Unsfw_New_Order_Notification extends Unsfw_Telegram_Order_Notifications {
    public function __construct() {
        add_action('woocommerce_new_order', array($this, 'send_notification'), 10, 1);
    }

    public function send_notification($order_id) {
        $order = wc_get_order($order_id);
        $message = "New order received!\nOrder ID: " . $order->get_id();
        $this->unsfw_send_telegram_message($message);
    }
}
new Unsfw_New_Order_Notification();
