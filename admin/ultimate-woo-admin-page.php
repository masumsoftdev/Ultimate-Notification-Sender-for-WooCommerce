<?php
if (!defined('ABSPATH')) {
    exit;
}

class Uwns_Admin {
    // Constructor
    public function __construct() {
        // Add menu page
        add_action('admin_menu', array($this, 'uwns_add_menu_page'));

        // Register plugin settings
        add_action('admin_init', array($this, 'uwns_register_settings'));
    }

    // Add menu page
    public function uwns_add_menu_page() {
        add_menu_page(
            __('Ultimate Woo Notifications Settings', 'ultimate-woo-notification-sender'),
            __('Woo Sender Settings', 'ultimate-woo-notification-sender'),
            'manage_options',
            'ultimate-woo-sender-settings',
            array($this, 'settings_page'),
            'dashicons-megaphone'
        );
    }

    // Register plugin settings
    public function uwns_register_settings() {
        register_setting('ultimate_woo_sender_options', 'uwns_settings', array($this, 'sanitize_settings'));
    }

    // Settings page content
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Ultimate Woo Notifications Settings', 'ultimate-woo-notification-sender'); ?></h1>
            <form method="post" action="options.php">
            <?php settings_fields('ultimate_woo_sender_options'); ?>
                <?php $options = get_option('uwns_settings', array()); ?>
                <table class="form-table">
                    <tr valign="top">
                            <th scope="row"><?php _e('Telegram API Token', 'ultimate-woo-notification-sender'); ?></th>
                            <td><input type="text" name="uwns_settings[telegram_api_token]" value="<?php echo esc_attr($options['telegram_api_token'] ?? ''); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e('Telegram Chat ID', 'ultimate-woo-notification-sender'); ?></th>
                        <td><input type="text" name="uwns_settings[telegram_chat_id]" value="<?php echo esc_attr($options['telegram_chat_id'] ?? ''); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

new Uwns_Admin();
