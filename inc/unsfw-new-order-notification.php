<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Unsfw_New_Order_Notification extends Unsfw_Base_Init {
    public function __construct() {
        add_action('woocommerce_new_order', array($this, 'Send_New_Order_Received_Notification'), 10, 1);
    }

    public function Send_New_Order_Received_Notification($order_id) {
        $order = wc_get_order($order_id);
        $message = "New order received!\nOrder ID: " . $order->get_id();
        $this->Send_Telegram_Notification($message);
    }
}
new Unsfw_New_Order_Notification();
