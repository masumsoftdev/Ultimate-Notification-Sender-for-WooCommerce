<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Unsfw_Order_Pending_Notification extends Unsfw_Telegram_Order_Notifications {
    public function __construct() {
        add_action('woocommerce_order_status_pending', array($this, 'send_notification'), 10, 1);
    }

    public function send_notification($order_id) {
        $order = wc_get_order($order_id);
        $message = "New pending order received!\n";
        $message .= "Order ID: " . $order->get_id() . "\n";
        $message .= "Customer: " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . "\n";
        $message .= "Total: " . $order->get_total() . " " . $order->get_currency();
        $this->unsfw_send_telegram_message($message);
    }
}
new Unsfw_Order_Pending_Notification();
