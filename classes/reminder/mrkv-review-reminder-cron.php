<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_REVIEW_REMINDER_CRON'))
{
	/**
	 * Class for setup cron
	 */
	class MRKV_REVIEW_REMINDER_CRON
	{
		/**
		 * Main cron hook
		 * */
		const CRON_HOOK = 'mrkv_next_reminder';

		/**
		 * Follow-up hook used to drain a backlog without one huge request
		 * */
		const CRON_HOOK_BATCH = 'mrkv_next_reminder_batch';

		/**
		 * Lock transient, prevents two overlapping runs of the same event
		 * */
		const LOCK_KEY = 'mrkv_review_reminder_running';

		/**
		 * Meta key holding either a scheduled date or '1' when already sent
		 * */
		const META_KEY = 'mrkv_review-reminder_sent';

		/**
		 * True while our own order query is running (scopes the query filter)
		 * @param bool
		 * */
		private $is_collecting = false;

		/**
		 * Constructor for cron
		 * */
		function __construct()
		{
			# Register the deactivation hook on the main plugin file, not on this one
        	register_deactivation_hook(MRKV_REVIEW_REMINDER_PLUGIN_FILE, array($this, 'deactivate'));

        	# Check cron exist
        	if (!wp_next_scheduled(self::CRON_HOOK)) 
        	{
	            # Schedule the event if it's not already scheduled
	            wp_schedule_event(time(), 'twicedaily', self::CRON_HOOK);
	        }

	        # Hook the review_reminder function to the cron event
	        add_action(self::CRON_HOOK, array($this, 'review_reminder_cron_function'));

	        # Same handler drains the rest of a backlog batch by batch
	        add_action(self::CRON_HOOK_BATCH, array($this, 'review_reminder_cron_function'));
		}

		/**
		 * Function to deactivate the cron job
		 * */ 
	    public function deactivate() 
	    {
	        # Clear the scheduled events on deactivation
	        wp_clear_scheduled_hook(self::CRON_HOOK);
	        wp_clear_scheduled_hook(self::CRON_HOOK_BATCH);

	        # Drop the run lock so a re-activation is not blocked by a stale one
	        delete_transient(self::LOCK_KEY);
	    }

	    /**
	     * How many orders one cron run is allowed to process
	     * @return int Batch size
	     * */
	    private function get_batch_size()
	    {
	    	# Allow shops to tune the batch size
	    	$batch_size = (int) apply_filters('mrkv_review_reminder_batch_size', 50);

	    	return ($batch_size > 0) ? $batch_size : 50;
	    }

	    /**
	     * Meta query selecting orders whose reminder is due and not sent yet
	     * @var string Current date
	     * @return array Meta query
	     * */
	    private function get_due_meta_query($now)
	    {
	    	# Dates are stored as 'Y-m-d H:i:s', so a plain string compare is enough.
	    	# The '!=' clause matters: '1' sorts before any date and would otherwise match.
	    	return array(
	    		'relation' => 'AND',
	    		array(
	    			'key'     => self::META_KEY,
	    			'value'   => '1',
	    			'compare' => '!=',
	    		),
	    		array(
	    			'key'     => self::META_KEY,
	    			'value'   => $now,
	    			'compare' => '<=',
	    			'type'    => 'CHAR',
	    		),
	    	);
	    }

	    /**
	     * Check if orders are stored in the HPOS tables
	     * @return bool Answer
	     * */
	    private function is_hpos_enabled()
	    {
	    	if (!class_exists(\Automattic\WooCommerce\Utilities\OrderUtil::class))
	    	{
	    		return false;
	    	}

	    	return \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
	    }

	    /**
	     * Add our meta query to the legacy (post table) order query.
	     * wc_get_orders() accepts 'meta_query' on HPOS only - on the CPT store it is
	     * dropped and triggers _doing_it_wrong(), so it has to be injected here.
	     * @var array Query args
	     * @var array Query vars
	     * @return array Query args
	     * */
	    public function inject_due_meta_query($wp_query_args, $query_vars)
	    {
	    	# Only touch the query we started ourselves
	    	if (!$this->is_collecting)
	    	{
	    		return $wp_query_args;
	    	}

	    	if (!isset($wp_query_args['meta_query']) || !is_array($wp_query_args['meta_query']))
	    	{
	    		$wp_query_args['meta_query'] = array();
	    	}

	    	$wp_query_args['meta_query'][] = $this->get_due_meta_query(gmdate('Y-m-d H:i:s'));

	    	return $wp_query_args;
	    }

	    /**
	     * Get IDs of orders whose reminder is due
	     * @var int Batch size
	     * @return array Order IDs
	     * */
	    private function get_due_order_ids($batch_size)
	    {
	    	$args = array(
	    		'limit'   => $batch_size,
	    		'orderby' => 'ID',
	    		'order'   => 'ASC',
	    		'return'  => 'ids',
	    	);

	    	$settings = get_option('mrkv_review_reminder');

	    	# The date was written when the order matched the filter. Its status may have
	    	# changed since, so only orders that still match are sent. Filtering in the query
	    	# (not in the loop) keeps such orders from filling every batch.
	    	if (!empty($settings['status_filter']) && is_array($settings['status_filter']))
	    	{
	    		$args['status'] = $settings['status_filter'];
	    	}

	    	if ($this->is_hpos_enabled())
	    	{
	    		# HPOS supports meta_query natively and indexes meta_key + meta_value
	    		$args['meta_query'] = $this->get_due_meta_query(gmdate('Y-m-d H:i:s'));

	    		$order_ids = wc_get_orders($args);
	    	}
	    	else
	    	{
	    		$this->is_collecting = true;

	    		add_filter('woocommerce_order_data_store_cpt_get_orders_query', array($this, 'inject_due_meta_query'), 10, 2);

	    		try
	    		{
	    			$order_ids = wc_get_orders($args);
	    		}
	    		finally
	    		{
	    			# Never leave the filter hanging on a global hook if the query blows up
	    			remove_filter('woocommerce_order_data_store_cpt_get_orders_query', array($this, 'inject_due_meta_query'), 10);

	    			$this->is_collecting = false;
	    		}
	    	}

	    	return is_array($order_ids) ? $order_ids : array();
	    }

	    /**
	     * The function that will be called on the cron event
	     * */ 
	    public function review_reminder_cron_function() 
	    {
	    	# WP-Cron only locks a spawn for 60 seconds. Sending dozens of mails takes
	    	# longer than that, so the same event can be started again while we are still
	    	# running. Without this lock the second run would send everything twice.
	    	if (get_transient(self::LOCK_KEY))
	    	{
	    		return;
	    	}

	    	set_transient(self::LOCK_KEY, 1, 10 * MINUTE_IN_SECONDS);

	    	$batch_size = $this->get_batch_size();
	    	$order_ids = $this->get_due_order_ids($batch_size);

	    	# Check orders
    		if (!empty($order_ids))
    		{
    			# Load file sender
    			require_once MRKV_REVIEW_REMINDER_PLUGIN_PATH . 'classes/reminder/mrkv-review-reminder-sender.php';

    			# Loop all orders
    			foreach ($order_ids as $order_id)
	    		{
	    			# Always read the order fresh, never from a cached list
	    			$order = wc_get_order($order_id);

	    			# Check order
	    			if (!$order)
	    			{
	    				# Go to next step
	    				continue;
	    			}

	    			$mrkv_review_reminder_sent = $order->get_meta(self::META_KEY);

	    			# Second guard: the query already excludes sent orders
	    			if (!$this->is_valid_date($mrkv_review_reminder_sent))
	    			{
	    				# Go to next step
	    				continue;
	    			}

    				# Get date
    				$dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $mrkv_review_reminder_sent);

    				# Check date beetween
    				if ($dateTime && $dateTime <= new DateTime())
				    {
				    	# Get reminder object
						$reminder = new MRKV_REVIEW_REMINDER_SENDER($order);

						# Send reminder
						$message = $reminder->send_reminder_review();
				    }
	    		}
    		}

    		delete_transient(self::LOCK_KEY);

    		# A full batch means there is probably more waiting - drain it shortly after,
    		# instead of loading the whole backlog into one request.
    		if (count($order_ids) >= $batch_size && !wp_next_scheduled(self::CRON_HOOK_BATCH))
    		{
    			wp_schedule_single_event(time() + MINUTE_IN_SECONDS, self::CRON_HOOK_BATCH);
    		}
	    }

	    /**
	     * Check if is valid date
	     * @var string Date
	     * @return boolean Answer
	     * */
	    public function is_valid_date($date) 
	    {
	    	# Create date format
	    	$date_new = DateTime::createFromFormat('Y-m-d H:i:s', $date);

	    	# Adjust format based on stored date
    		return $date_new && $date_new->format('Y-m-d H:i:s') === $date;
	    }
	}
}
