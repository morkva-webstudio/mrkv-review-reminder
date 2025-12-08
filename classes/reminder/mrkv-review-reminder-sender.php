<?php
# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; 

# Check if class exist
if (!class_exists('MRKV_REVIEW_REMINDER_SENDER'))
{
	/**
	 * Class for setup sending functions
	 */
	class MRKV_REVIEW_REMINDER_SENDER
	{
		/**
		 * @param object Order
		 * */
		private $order;

		/**
		 * @param array Plugin settings
		 * */
		public $mrkv_review_reminder;

		/**
		 * Constructor for plugin sending functions
		 * */
		function __construct($order)
		{
			$this->order = $order;
			$this->mrkv_review_reminder = get_option('mrkv_review_reminder');
		}

		/**
		 * Remove reminder
		 * @return string Message
		 * */
		public function remove_reminder_review()
		{
			# Set empty message
			$message = '<div class="mrkv_review_reminder__order mrkv_review_reminder__send_old">
				<div class="mrkv_review_reminder__order__send_manually" data-order-id="' . $order_id . '">
					<img src="' . MRKV_REVIEW_REMINDER_IMG_URL . '/global/letter-icon.svg' . '" alt="Email" title="Email">
					' . __('Send now', 'mrkv-review-reminder') . '
				</div>
			</div>';

			# Delete meta
			$this->order->delete_meta_data('mrkv_review-reminder_sent');
			$this->order->save();

			# Return success message
			return $message;
		}

		/**
		 * Send test reminder
		 * @var string Email
		 * @return bool Answer
		 * */
		public function send_test_reminder($email)
		{
			# Get all data for mail
			$subject = (isset($this->mrkv_review_reminder['email']['subject'])) ? $this->mrkv_review_reminder['email']['subject'] : '';
			$content = (isset($this->mrkv_review_reminder['email']['content'])) ? $this->mrkv_review_reminder['email']['content'] : '';
			$sender = (isset($this->mrkv_review_reminder['email']['sender']) && $this->mrkv_review_reminder['email']['sender']) ? $this->mrkv_review_reminder['email']['sender'] : get_bloginfo('name');
			$reply_to = (isset($this->mrkv_review_reminder['email']['reply'])) ? $this->mrkv_review_reminder['email']['reply'] : '';
			$content_header = (isset($this->mrkv_review_reminder['email']['header'])) ? $this->mrkv_review_reminder['email']['header'] : __('Review recently purchased products', 'mrkv-review-reminder');

			# Convert data
			$subject = $this->convert_subject($subject);
			$content = $this->convert_subject($content);
			$content = $this->add_products_to_content($content, $sender, $content_header, true);

			# Change mail from name
			add_filter('wp_mail_from_name', array($this, 'mrkv_review_reminder_new_from'));

			# Change content type
			add_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );

			# Create headers
			$headers = array(
				'Content-Type: text/html; charset=UTF-8',
				'Reply-To: ' . $reply_to
			);

			# Send email
			wp_mail( $email, $subject, $content, $headers );

			return true;
		}

		/**
		 * Send review
		 * @return string message
		 * */
		public function send_reminder_review()
		{
			# Set empty message
			$message_result = '<div class="mrkv_review_reminder__order mrkv_review_reminder__order__notice">
				<img src="' .  MRKV_REVIEW_REMINDER_IMG_URL . '/global/notice-success.svg' . '" alt="Email" title="Email">
				<div class="mrkv_review_reminder__orders__data">
				' . __('Already sent', 'mrkv-review-reminder') . '
				</div>
			</div>';

			# Get all data for mail
			$email = $this->order->get_meta('shipping_email') ? $this->order->get_meta('shipping_email') : $this->order->get_billing_email();
			$subject = (isset($this->mrkv_review_reminder['email']['subject'])) ? $this->mrkv_review_reminder['email']['subject'] : '';
			$content = (isset($this->mrkv_review_reminder['email']['content'])) ? $this->mrkv_review_reminder['email']['content'] : '';
			$sender = get_option('woocommerce_email_from_name');
			$reply_to = get_option('woocommerce_email_from_address');
			$content_header = (isset($this->mrkv_review_reminder['email']['header'])) ? $this->mrkv_review_reminder['email']['header'] : __('Review recently purchased products', 'mrkv-review-reminder');

			# Convert data
			$subject = $this->convert_subject($subject);
			$content = $this->convert_subject($content);
			$content = $this->add_products_to_content($content, $sender, $content_header);

			# Change mail from name
			add_filter('wp_mail_from_name', array($this, 'mrkv_review_reminder_new_from'));

			# Change content type
			add_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );

			# Create headers
			$headers = array(
				'Content-Type: text/html; charset=UTF-8',
				'Reply-To: ' . $reply_to
			);

			# Send email
			wp_mail( $email, $subject, $content, $headers );

			$this->order->update_meta_data('mrkv_review-reminder_sent', '1');

			if($this->order->get_meta('mrkv_review-reminder_sent_date'))
			{
				# Add sent date
				$sent_date = json_decode($this->order->get_meta('mrkv_review-reminder_sent_date'), true);
				array_push($sent_date, gmdate('Y-m-d H:i:s'));
				$this->order->update_meta_data('mrkv_review-reminder_sent_date', wp_json_encode($sent_date));
			}
			else
			{
				# Add sent date
				$sent_date = array(gmdate('Y-m-d H:i:s'));
				$this->order->update_meta_data('mrkv_review-reminder_sent_date', wp_json_encode($sent_date));				
			}

			if(isset($this->mrkv_review_reminder['log']['order']) && $this->mrkv_review_reminder['log']['order'] == 'on')
			{
				$this->order->add_order_note('Sent: ' . gmdate('Y-m-d'), $is_customer_note = 0, $added_by_user = false);
			}
			
			$this->order->save();

			# Return success message
			return $message_result;
		}

		/**
		 * Change from name
		 * @var string Name
		 * @return string Name
		 * */
		public function mrkv_review_reminder_new_from($name)
		{
			# Return new name
			return (isset($this->mrkv_review_reminder['email']['sender']) && $this->mrkv_review_reminder['email']['sender']) ? $this->mrkv_review_reminder['email']['sender'] : $name;
		}

		/**
		 * Set html wp_mail type
		 * @return string Type
		 * */
		public function set_html_content_type()
		{
			return 'text/html';
		}

		/**
		 * Convert subject with shortcodes
		 * @var string Subject
		 * @return string Subject
		 * */
		private function convert_subject($subject)
		{
			if($this->order)
			{
				# Get first name
				$first_name = ( $this->order->get_shipping_first_name() ) ? $this->order->get_shipping_first_name() : $this->order->get_billing_first_name();

				# Convert subject
				$subject = str_replace("[mrkv-firstname]", $first_name, $subject);
			}			

			# Return subject
			return $subject;
		}

		/**
		 * Create html content with products
		 * @var string Content
		 * @var sender data
		 * @return string HTML
		 * */
		private function add_products_to_content($content, $sender, $content_header, $is_test = false)
		{
			# Create default empty
			$products_html = '';

			$text_leave_comment = (isset($this->mrkv_review_reminder['email']['btn_text']) && $this->mrkv_review_reminder['email']['btn_text']) ? $this->mrkv_review_reminder['email']['btn_text'] : __( 'Leave review', 'mrkv-review-reminder-pro' );

			# Check test mode sending
			if($is_test)
			{
				# Open products HTML
				$products_html = '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';

				# Create arguments
				$args = array(
			        'limit'      => 1,
			        'orderby'    => 'date',
			        'order'      => 'DESC',
			        'status'     => 'publish',
			    );

				# Get products by arg
			    $products = wc_get_products($args);

			    if(!empty($products))
			    {
			    	foreach($products as $last_product)
			    	{
			    		# Get product image
						$image = wp_get_attachment_image_src($last_product->get_image_id(), 'thumbnail')[0];

						# Add product to table row
						$products_html .= '<tr>
							<td style="padding: 10px; width: 80px;">
								<a href="' . get_permalink($last_product->get_id()) . '">
									<img src="' . esc_url($image) . '" alt="' . esc_attr($last_product->get_name()) . '" style="width: 70px; height: auto;"/>
								</a>
							</td>
							<td style="padding: 10px; vertical-align: middle;">
								<a style="text-decoration: none;" href="' . get_permalink($last_product->get_id()) . '">' . $last_product->get_name() . '</a>
								<br>
				                <a href="' . get_permalink($last_product->get_id()) . '#reviews" style="display: inline-block; margin-top: 6px; font-size: 13px;">
				                   ' . $text_leave_comment . '
				                </a>
							</td>
						</tr>';
			    	}
			    }

				# Close products HTML
				$products_html .= '</table>';
			}
			else
			{
				# Open products HTML
				$products_html = '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';

				# Loop all products from order 
				foreach ($this->order->get_items() as $item_id => $item) 
				{
					# Get product object
					$product = $item->get_product();

					# Check product
					if ($product) 
					{
					    # Check product type
					    if ($product->is_type('variable')) 
					    {
					        # Get variation ID
					        $variation_id = $item->get_variation_id();

					        # Check variation ID
					        if ($variation_id) 
					        {
					            # Get variation object
					            $variation = new WC_Product_Variation($variation_id); 

					            # Get variation image
					            $image = wp_get_attachment_image_src($variation->get_image_id(), 'thumbnail')[0];

					            # Add product to table row
					            $products_html .= '<tr>
					                <td style="padding: 10px; width: 80px;">
					                    <a href="' . get_permalink($variation->get_id()) . '">
					                        <img src="' . esc_url($image) . '" alt="' . esc_attr($variation->get_name()) . '" style="width: 70px; height: auto;"/>
					                    </a>
					                </td>
					                <td style="padding: 10px; vertical-align: middle;">
					                    <a style="text-decoration: none; font-weight: bold;" href="' . get_permalink($variation->get_id()) . '">' . $variation->get_name() . '</a>
					                    <br>
					                    <a href="' . get_permalink($variation->get_id()) . '#reviews" 
					                       style="display: inline-block; margin-top: 6px;font-size: 13px;">
					                       ' . $text_leave_comment . '
					                    </a>
					                </td>
					            </tr>';
					        }
					    } 
					    else 
					    {
					        # Get product image
					        $image = wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0];

					        # Add product to table row
					        $products_html .= '<tr>
					            <td style="padding: 10px; width: 80px;">
					                <a href="' . get_permalink($product->get_id()) . '">
					                    <img src="' . esc_url($image) . '" alt="' . esc_attr($product->get_name()) . '" style="width: 70px; height: auto;"/>
					                </a>
					            </td>
					            <td style="padding: 10px; vertical-align: middle;">
					                <a style="text-decoration: none; font-weight: bold;" href="' . get_permalink($product->get_id()) . '">' . $product->get_name() . '</a>
					                <br>
					                <a href="' . get_permalink($product->get_id()) . '#reviews" 
					                   style="display: inline-block; margin-top: 6px; font-size: 13px;">
					                   ' . $text_leave_comment . '
					                </a>
					            </td>
					        </tr>';
					    }
					}
				}

				# Close products HTML
				$products_html .= '</table>';
			}

			$woocommerce_email_base_color = get_option('woocommerce_email_base_color') ? get_option('woocommerce_email_base_color') : '#96588a';
			$woocommerce_email_background_color = get_option('woocommerce_email_background_color') ? get_option('woocommerce_email_background_color') : '#f4f4f4';
			$woocommerce_email_body_background_color = get_option('woocommerce_email_body_background_color') ? get_option('woocommerce_email_body_background_color') : '#ffffff';
			$woocommerce_email_text_color = get_option('woocommerce_email_text_color') ? get_option('woocommerce_email_text_color') : '#ffffff';
			$woocommerce_email_footer_text_color = get_option('woocommerce_email_footer_text_color') ? get_option('woocommerce_email_footer_text_color') : '#999999';
			$header_image = get_option('woocommerce_email_header_image');
			$header_mail = '';
			
			if($header_image)
			{
				$header_mail = '<div id="template_header_image">
			        <p style="margin-top:0; margin-bottom: 20px;">
			            <img src="' . esc_url( $header_image ) . '" alt="' . bloginfo( 'name' ) . '" />
			        </p>
			    </div>';
			}

			$reminder_css_desktop = '';
			$reminder_css_mobile = '';

			$message = '
			<html>
			<head>
			  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			  <title>' . esc_html($sender) . '</title>
			  <style>
			  	' . $reminder_css_desktop . '
			    body {
			      background-color: #f4f4f4;
			      font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
			      margin: 0;
			      padding: 0;
			      -webkit-text-size-adjust: none;
			      width: 100% !important;
			    }
			    #template_container {
			      background-color: ' . $woocommerce_email_background_color . ';
			      padding: 70px;
			    }
			    #template_header {
			      background-color: ' . $woocommerce_email_base_color . ';
			      color: #ffffff;
			      text-align: center;
			      padding: 36px 48px;
			          max-width: 600px;
				    border-radius: 5px 5px 0 0;
				    margin-left: auto;
				    margin-right: auto;
			    }
			    #template_header h1 {
			      font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
				    font-size: 30px;
				    font-weight: 300;
				    line-height: 150%;
				    margin: 0;
				    text-align: left;
				    color: #fff;
				    background-color: inherit;
			    }
			    #template_body {
			      background-color: ' . $woocommerce_email_body_background_color . ';
			      padding: 20px 48px;
			      border: 1px solid #e4e4e4;
			          max-width: 600px;
				    border-radius: 0 0 5px 5px;
				    margin-left: auto;
				    margin-right: auto;
			    }
			    #template_body a{
			    	color: ' . $woocommerce_email_text_color . ';
				}
			    .email-body {
			      color: ' . $woocommerce_email_text_color . ';
			      font-size: 14px;
			      line-height: 150%;
			      text-align: left;
			    }
			    .email-body p {
			      margin: 0 0 16px;
			    }
			    #template_body ul{
				    padding: 0;
					list-style-type: none;
				}
				#template_body ul a{
					text-decoration: none;
				}
			    #template_footer {
			      padding: 16px 48px;
			      text-align: center;
			      font-size: 12px;
			      color: ' . $woocommerce_email_footer_text_color . ';
			      background-color: #f4f4f4;
			      margin-left: auto;
				  margin-right: auto;
				  max-width: 600px;
			    }
			    #template_footer p {
			      margin: 0;
			      color: ' . $woocommerce_email_footer_text_color . ';
			    }
			    .woocommerce-Price-amount {
			      font-weight: bold;
			    }
			    .button {
			      background-color: #96588a;
			      color: #ffffff;
			      text-decoration: none;
			      padding: 12px 20px;
			      border-radius: 5px;
			      text-align: center;
			      display: inline-block;
			    }
			    .button:hover {
			      background-color: #764c6b;
			    }
			    #template_header_image{
				    max-width: 200px;
				    margin-left: auto;
				    margin-right: auto;
				}
				#template_header_image img{
				max-width: 200px;
				}
			    @media (max-width: 600px) {
			    	' . $reminder_css_mobile . '
		           	#template_container{
			    		padding: 70px 5px;
			    	}
			    	#template_header h1 {
			    		font-size: 20px;
			    	}
			    	#template_body{
			    		padding: 10px 5px;
			    	}
		        }
			  </style>
			</head>
			<body>
			  <div id="template_container">
			  	' . $header_mail . '
			    <div id="template_header">
			      <h1>' . $content_header . '</h1>
			    </div>

			    <div id="template_body">
			      <div class="email-body">
			        ' . nl2br( wp_kses_post( $content ) ) . $products_html . '
			      </div>
			    </div>

			    <div id="template_footer">
			      <p>' . esc_html($sender) . '</p>
			    </div>
			  </div>
			</body>
			</html>
			';

			# Return html
			return $message;
		}
	}
}