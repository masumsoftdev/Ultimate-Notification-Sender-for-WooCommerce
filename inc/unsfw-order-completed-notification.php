<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Unsfw_Order_Completed_Notification extends Unsfw_Base_Init {
    public function __construct() {
        add_action('woocommerce_order_status_completed', array($this, 'Send_Order_Complete_Notification'), 10, 1);
    }

    public function Send_Order_Complete_Notification($order_id) {
        $order = wc_get_order($order_id);
        $message = "Order has been completed!\nOrder ID: " . $order->get_id();
        $this->Send_Telegram_Notification($message);
    }
}
new Unsfw_Order_Completed_Notification();
