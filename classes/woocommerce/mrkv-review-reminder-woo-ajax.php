<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_REVIEW_REMINDER_WOO_AJAX'))
{
	/**
	 * Class for setup ajax
	 */
	class MRKV_REVIEW_REMINDER_WOO_AJAX
	{
		/**
		 * Constructor for plugin ajax
		 * */
		function __construct()
		{
			# Sending ajax reminder
			add_action( 'wp_ajax_mrkv_review_reminder_sent', array($this, 'mrkv_review_reminder_sending_func') );
			add_action( 'wp_ajax_nopriv_mrkv_review_reminder_sent', array($this, 'mrkv_review_reminder_sending_func') );

			# Remove ajax reminder
			add_action( 'wp_ajax_mrkv_review_reminder_remove', array($this, 'mrkv_review_reminder_remove_func') );
			add_action( 'wp_ajax_nopriv_mrkv_review_reminder_remove', array($this, 'mrkv_review_reminder_remove_func') );

			# Test ajax reminder
			add_action( 'wp_ajax_mrkv_review_reminder_test_send', array($this, 'mrkv_review_reminder_test_send_func') );
			add_action( 'wp_ajax_nopriv_mrkv_review_reminder_test_send', array($this, 'mrkv_review_reminder_test_send_func') );
		}

		/**
		 * Sending ajax reminder
		 * */
		public function mrkv_review_reminder_sending_func()
		{
			# Check nonce
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['nonce'])), 'mrkv_review_reminder_sent_nonce' ) ) 
			{
				# Stop job
		        wp_die();
		    }

		    # Check order ID
			if(isset($_POST['order_id']))
			{
				# Get order ID
				$order_id = sanitize_text_field( wp_unslash($_POST['order_id']) );

				# Get order
				$order = wc_get_order($order_id);

				# Check order
				if($order)
				{
					# Get reminder object
					require_once MRKV_REVIEW_REMINDER_PLUGIN_PATH . 'classes/reminder/mrkv-review-reminder-sender.php';
					$reminder = new MRKV_REVIEW_REMINDER_SENDER($order);

					# Send reminder
					$message = $reminder->send_reminder_review();

					$allowed_tags = array(
					    'div' => array(
					        'class' => array(), // Allow class attributes for div
					    ),
					    'img' => array(
					        'src' => array(), // Allow the src attribute for images
					        'alt' => array(), // Allow the alt attribute for accessibility
					        'title' => array(), // Allow the title attribute
					    ),
					    'strong' => array(), // If you want to support strong text
					    'em' => array(), // If you want to support emphasized text
					    'p' => array(
					        'class' => array(), // Allow class attribute for paragraphs
					    ),
					    // Add other tags as needed
					);

					echo wp_kses($message, $allowed_tags);
				}
			}

			# Stop job
			wp_die();
		}

		public function mrkv_review_reminder_remove_func()
		{
			# Check nonce
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['nonce'])), 'mrkv_review_reminder_sent_nonce' ) ) 
			{
				# Stop job
		        wp_die();
		    }

		    # Check order ID
			if(isset($_POST['order_id']))
			{
				# Get order ID
				$order_id = sanitize_text_field( wp_unslash($_POST['order_id']) );

				# Get order
				$order = wc_get_order($order_id);

				# Check order
				if($order)
				{
					# Get reminder object
					require_once MRKV_REVIEW_REMINDER_PLUGIN_PATH . 'classes/reminder/mrkv-review-reminder-sender.php';
					$reminder = new MRKV_REVIEW_REMINDER_SENDER($order);

					# Send reminder
					$message = $reminder->remove_reminder_review();

					$allowed_tags = array(
					    'div' => array(
					        'class' => array(), // Allow class attributes for div
					        'data-order-id' => array(), // Allow custom data attributes
					    ),
					    'img' => array(
					        'src' => array(), // Allow the src attribute for images
					        'alt' => array(), // Allow the alt attribute for accessibility
					        'title' => array(), // Allow the title attribute
					    ),
					    'strong' => array(), // If you want to support strong text
					    'em' => array(), // If you want to support emphasized text
					    'span' => array(), // If you want to support span text
					    'p' => array(
					        'class' => array(), // Allow class attribute for paragraphs
					    ),
					    // Add other tags as needed
					);

					echo wp_kses($message, $allowed_tags);
				}
			}

			# Stop job
			wp_die();
		}

		/**
		 * Sending test ajax reminder
		 * */
		public function mrkv_review_reminder_test_send_func()
		{
			# Check nonce
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['nonce'])), 'mrkv_review_reminder_sent_nonce' ) ) 
			{
				wp_die();
			}

			# Check email
			if (isset($_POST['email']) && is_email(sanitize_text_field(wp_unslash($_POST['email'])))) {
				$email = sanitize_email(wp_unslash($_POST['email']));
				$raw_order_id = isset($_POST['order_id']) ? sanitize_text_field(wp_unslash($_POST['order_id'])) : 'last';
				$order = false;

				if ($raw_order_id !== 'last' && absint($raw_order_id) > 0) {
					$order = wc_get_order(absint($raw_order_id));
				}

				if (!$order) {
					$last_orders = wc_get_orders(array(
						'limit'  => 1,
						'orderby' => 'date',
						'order'   => 'DESC',
						'return'  => 'objects',
					));

					if (!empty($last_orders)) {
						$order = $last_orders[0];
					}
				}

				if (!$order) {
					wp_send_json_error(__('No orders found on the website.', 'woocommerce'));
				}

				# Get reminder object
				require_once MRKV_REVIEW_REMINDER_PLUGIN_PATH . 'classes/reminder/mrkv-review-reminder-sender.php';
				$reminder = new MRKV_REVIEW_REMINDER_SENDER($order);
				$message = $reminder->send_test_reminder($email);

				if ( ob_get_length() ) {
					ob_clean();
				}
				
				wp_send_json_success($message);
			}

			# Stop job
			wp_die();
		}
	}
}