<?php
/**
 * Plugin Name: Morkva Review Reminder
 * Plugin URI: https://morkva.co.ua/product-category/plugins/
 * Description: We send simple emails to remind you of your review
 * Version: 1.0.8
 * Author: MORKVA
 * Text Domain: mrkv-review-reminder
 * Domain Path: /languages
 * Tested up to: 6.8
 * WC requires at least: 3.8
 * WC tested up to: 9.8
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'WPINC' ) ) {
    die;
}

add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

# Global File
define('MRKV_REVIEW_REMINDER_PLUGIN_FILE', __FILE__);

# Include CONSTANTS
require_once 'constants-mrkv-review-reminder.php';

# Check if Woo plugin activated
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) 
{
    # Include plugin settings
    require_once 'classes/mrkv-review-reminder-run.php'; 

    # Setup plugin settings
    new MRKV_REVIEW_REMINDER_RUN();
}

add_action( 'plugins_loaded', function() {
    load_plugin_textdomain(
        'mrkv-review-reminder',
        false,
        dirname( plugin_basename(__FILE__) ) . '/languages/'
    );
}, 11);
