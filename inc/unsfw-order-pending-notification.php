<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Unsfw_Order_Pending_Notification extends Unsfw_Base_Init {
    public function __construct() {
        add_action('woocommerce_order_status_pending', array($this, 'Send_Order_Pending_Notification'), 10, 1);
    }

    public function Send_Order_Pending_Notification($order_id) {
        $order = wc_get_order($order_id);
        $message = "New pending order received!\n";
        $message .= "Order ID: " . $order->get_id() . "\n";
        $message .= "Customer: " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . "\n";
        $message .= "Total: " . $order->get_total() . " " . $order->get_currency();
        $this->Send_Telegram_Notification($message);
    }
}
new Unsfw_Order_Pending_Notification();
