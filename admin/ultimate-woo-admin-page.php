<?php
if (!defined('ABSPATH')) {
    exit;
}

class Unsfw_Admin {
    // Constructor
    public function __construct() {
        // Add menu page
        add_action('admin_menu', array($this, 'unsfw_add_menu_page'));

        // Register plugin settings
        add_action('admin_init', array($this, 'unsfw_register_settings'));
        
    }

    // Add menu page
    public function unsfw_add_menu_page() {
        add_menu_page(
            __('Ultimate Notifications Sender Settings', 'unsfw'),
            __('Woo Notification', 'unsfw'),
            'manage_options',
            'ultimate-woo-sender-settings',
            array($this, 'settings_page'),
            'dashicons-bell', 57
        );
    }

    // Register plugin settings
    public function unsfw_register_settings() {
        register_setting('ultimate_woo_sender_options', 'unsfw_settings', array($this, 'sanitize_settings'));
    }

    // Settings page content
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Ultimate Woo Notifications Settings', 'unsfw'); ?></h1>
            <form method="post" action="options.php">
            <?php settings_fields('ultimate_woo_sender_options'); ?>
                <?php $options = get_option('unsfw_settings', array()); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Enable Notifications', 'unsfw'); ?></th>
                        <td>
                            <input type="checkbox" name="unsfw_settings[enable_notifications]" value="1" <?php checked($options['enable_notifications'] ?? '', 1); ?> />
                            <label for="enable_notifications"><?php esc_html_e('Enable Telegram Notifications', 'unsfw'); ?></label>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Telegram API Token', 'unsfw'); ?></th>
                        <td><input type="text" name="unsfw_settings[telegram_api_token]" value="<?php echo esc_attr($options['telegram_api_token'] ?? ''); ?>" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Telegram Chat ID', 'unsfw'); ?></th>
                        <td><input type="text" name="unsfw_settings[telegram_chat_id]" value="<?php echo esc_attr($options['telegram_chat_id'] ?? ''); ?>" /></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    // Sanitize settings before saving
    public function sanitize_settings($input) {
        $input['enable_notifications'] = !empty($input['enable_notifications']) ? 1 : 0;
        $input['telegram_api_token'] = sanitize_text_field($input['telegram_api_token']);
        $input['telegram_chat_id'] = sanitize_text_field($input['telegram_chat_id']);
        return $input;
    }
}

new Unsfw_Admin();
