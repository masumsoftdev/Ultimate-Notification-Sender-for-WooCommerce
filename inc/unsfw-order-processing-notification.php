<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Unsfw_Order_Processing_Notification extends Unsfw_Base_Init {
    public function __construct() {
        add_action('woocommerce_order_status_processing', array($this, 'Send_Order_Processing_Notification'), 10, 1);
    }

    public function Send_Order_Processing_Notification($order_id) {
        $order = wc_get_order($order_id);
        $message = "Order is now processing!\nOrder ID: " . $order->get_id();
        $this->Send_Telegram_Notification($message);
    }
}
new Unsfw_Order_Processing_Notification();
