<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_REVIEW_REMINDER_MENU'))
{
	/**
	 * Class for setup plugin admin menu
	 */
	class MRKV_REVIEW_REMINDER_MENU
	{
		/**
	     * Slug for page in Woo Tab Sections
	     * 
	     * */
	    private $slug = 'mrkv_review_reminder_settings';

		/**
		 * Constructor for plugin admin menu
		 * */
		function __construct()
		{
			# Register page settings
			add_action('admin_menu', array($this, 'mrkv_review_reminder_register_plugin_page'), 99);

			# Add support language
			add_action( 'plugins_loaded', array($this, 'mrkv_review_reminder_load_textdomain'), 11 );
		}

		/**
		 * Add settings page to menu
		 * */
		public function mrkv_review_reminder_register_plugin_page()
		{
			# Add menu to WP
	        add_menu_page(__('morkva Review Reminder', 'mrkv-review-reminder'), __('morkva Review Reminder', 'mrkv-review-reminder'), 'manage_options', $this->slug, array($this, 'mrkv_review_reminder_get_plugin_settings_content'), MRKV_REVIEW_REMINDER_IMG_URL . '/global/morkva-icon-20x20.svg');
		}

		/**
		 * Get settings page
		 * */
		public function mrkv_review_reminder_get_plugin_settings_content()
		{
			# Include template
			include MRKV_REVIEW_REMINDER_PLUGIN_PATH_TEMP . '/settings/template-mrkv-review-reminder-settings.php';
		}

		/**
		 * Load translate
		 * */
		public function mrkv_review_reminder_load_textdomain()
		{
			# Load
			load_plugin_textdomain(
		        'mrkv-review-reminder', 
		        false,             
		        MRKV_REVIEW_REMINDER_PLUGIN_PATH . 'i18n/'
		    );
		}
	}
}