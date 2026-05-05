<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
	$current_page = '/wp-admin/admin.php?page=mrkv_review_reminder_settings';
?>
<div class="admin_mrkv_ua_shipping_page">
	<div class="admin_mrkv_ua_shipping_page__header">
		<div class="admin_mrkv_ua_shipping__header mrkv_block_rounded">
			<div class="admin_mrkv_ua_shipping__header__content">
				<a class="admin_mrkv_ua_shipping__header_img" href="<?php echo esc_url($current_page); ?>">
					<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/clock.svg'); ?>" alt="morkva Review Reminder" title="morkva Review Reminder">
				</a>
				<a href="<?php echo esc_url($current_page); ?>"><?php echo esc_html__('Global', 'mrkv-review-reminder'); ?></a>
				<a class="admin_mrkv_ua_shipping_morkva-logo" href="https://morkva.co.ua/" target="blanc">
					<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/morkva-logo.svg'); ?>" alt="morkva" title="morkva">
				</a>
			</div>
		</div>
	</div>
	<div class="admin_mrkv_ua_shipping_page__inner">
		<div class="admin_mrkv_ua_shipping__block col-mrkv-10">
			<div class="admin_mrkv_ua_shipping__info">
				<?php settings_errors(); ?>
			</div>
		</div>
		<div class="admin_mrkv_ua_shipping__block col-mrkv-10">
			<div class="admin_mrkv_ua_shipping__tabs">
				<div class="admin_mrkv_ua_shipping__tabs_main mrkv_block_rounded">
					<h2>
						<?php echo esc_html__('Settings', 'mrkv-review-reminder'); ?>
					</h2>
					<div class="admin_mrkv_ua_shipping__tabs_main__inner">
						<?php
							$mrkv_review_reminder_tabs = array(
								'basic_settings' => esc_html__('Basic', 'mrkv-review-reminder'),
								'log' => esc_html__('Debug', 'mrkv-review-reminder')
							);
							$counter = 0;
							foreach($mrkv_review_reminder_tabs as $id => $name)
							{
								?>
									<a href="#<?php echo esc_html($id); ?>-mrkv" data-tab="<?php echo esc_html($id); ?>" class="mrkv_up_ship_tab_btn <?php if($counter == 0){echo 'active'; } ?>"><?php echo esc_html($name); ?></a>
								<?php

								++$counter;
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="admin_mrkv_ua_shipping__block col-mrkv-7">
			<div class="admin_mrkv_ua_shipping__settings">
				<form method="post" action="options.php">
					<?php settings_fields('mrkv-review-reminder-settings-group'); ?>
						<div class="mrkv_block_rounded">
							<?php

								$mrkv_review_reminder = get_option('mrkv_review_reminder');

								require_once MRKV_REVIEW_REMINDER_PLUGIN_PATH . 'classes/settings/global/mrkv-review-reminder-option-fields.php';
								$field_generator = new MRKV_REVIEW_REMINDER_OPTION_FILEDS();

								$allowed_tags = array(
								    'strong' => array(),
								    'em' => array(),
								    'label' => array(
								        'for' => array(), // Allow for attribute for label
								        'class' => array(), // Allow class attribute for styling
								    ),
								    'p' => array(
								        'class' => array(), // Allow class attribute for paragraphs
								    ),
								    'input' => array(
								        'type' => array(), // Allow different types of inputs, including checkbox
								        'value' => array(),
								        'name' => array(),
								        'id' => array(),
								        'class' => array(),
								        'placeholder' => array(),
								        'checked' => array(), // Allow checked attribute for checkboxes
								        'disabled' => array()
								    ),
								    'div' => array(
								        'class' => array(), // Allow class attribute for div
								    ),
								    'span' => array(
								        'class' => array(),
								    ),
								    'img' => array(
								        'class' => array(),
								        'src' => array(),
								    ),
								    'select' => array(
								        'id' => array(),
								        'name' => array(),
								        'multiple' => array(), // Allow multiple attribute
								        'data-select2-id' => array(), // Allow custom data attributes
								        'tabindex' => array(),
								        'class' => array(), // Include classes for styling
								        'aria-hidden' => array(), // Accessibility attributes
								    ),
								    'option' => array(
								        'value' => array(),
								        'selected' => array(),
								    ),
								    'textarea' => array( // Allow textarea tag
								        'id' => array(),
								        'name' => array(),
								        'placeholder' => array(),
								        'class' => array(),
								        'rows' => array(), // Allow rows attribute for textarea
								        'cols' => array(), // Allow cols attribute for textarea
								    ),
								);
							?>
							<section id="basic_settings" class="mrkv_up_ship_shipping_tab_block active">
								<h2><img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/settings-icon.svg'); ?>" alt="Settings" title="Settings"><?php echo esc_html__('Basic settings', 'mrkv-review-reminder'); ?></h2>
								<hr class="mrkv-ua-ship__hr">
								<div class="admin_ua_ship_morkva_settings_row">
									<div class="admin_ua_ship_morkva_settings_line col-mrkv-5">
										<div class="admin_ua_ship_morkva_settings_line">
											<?php 
												$data = isset($mrkv_review_reminder['interval']) ? $mrkv_review_reminder['interval'] : '';
												$description = '';

												echo wp_kses($field_generator->get_input_number(esc_html__('Reminder interval, days', 'mrkv-review-reminder'), 'mrkv_review_reminder[interval]', $data, 'mrkv_review_reminder_interval' , '', '', $description), $allowed_tags);
											?>
										</div>
									</div>
								</div>
								<h3>
									<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/mention-square-icon.svg'); ?>" alt="Email" title="Email"><?php echo esc_html__('Email configurations', 'mrkv-review-reminder'); ?>
								</h3>
								<p><?php echo esc_html__('Create a message that will be sent', 'mrkv-review-reminder'); ?></p>
								<hr class="mrkv-ua-ship__hr">
								<div class="admin_ua_ship_morkva_settings_row">
									<div class="col-mrkv-5">
										<div class="admin_ua_ship_morkva_settings_line">
											<?php 
												$data = isset($mrkv_review_reminder['email']['subject']) ? $mrkv_review_reminder['email']['subject'] : '';
												$description = esc_html__('Shortcode firstname: [mrkv-firstname]', 'mrkv-review-reminder');

												echo wp_kses($field_generator->get_input_text(__('Email subject', 'mrkv-review-reminder'), 'mrkv_review_reminder[email][subject]', $data, 'mrkv_review_reminder_email_subject' , '', __('Enter the subject...', 'mrkv-review-reminder'), $description), $allowed_tags);
											?>
										</div>
									</div>
									<div class="col-mrkv-5">
										<div class="admin_ua_ship_morkva_settings_line">
											<?php 
												$data = get_option('woocommerce_email_from_name');
												$description = '';
												$label = __('Sender name', 'mrkv-review-reminder') . '<span class="mrkv-up-ship-tooltip"><img src="' . MRKV_REVIEW_REMINDER_IMG_URL . '/global/info-icon.svg' .'" ><div class="mrkv-up-ship-tooltip__data">' . __('From WooCommerce email settings', 'mrkv-review-reminder') . '</div></span>';

												echo wp_kses($field_generator->get_input_text($label, 'mrkv_review_reminder[email][sender]', $data, 'mrkv_review_reminder_email_sender' , '', __('Enter the sender...', 'mrkv-review-reminder'), $description, 'disabled'), $allowed_tags);
											?>
										</div>
									</div>
								</div>
								<div class="admin_ua_ship_morkva_settings_row">
									<div class="col-mrkv-5">
										<div class="admin_ua_ship_morkva_settings_line">
											<?php 
												$data = get_option('woocommerce_email_from_address');
												$description = '';
												$label = __('Reply-to', 'mrkv-review-reminder') . '<span class="mrkv-up-ship-tooltip"><img src="' . MRKV_REVIEW_REMINDER_IMG_URL . '/global/info-icon.svg' .'" ><div class="mrkv-up-ship-tooltip__data">' . __('From WooCommerce email settings', 'mrkv-review-reminder') . '</div></span>';

												echo wp_kses($field_generator->get_input_text($label, 'mrkv_review_reminder[email][reply]', $data, 'mrkv_review_reminder_email_reply' , '', __('Enter email...', 'mrkv-review-reminder'), $description, 'disabled'), $allowed_tags);
											?>
										</div>
									</div>
									<div class="col-mrkv-5">
										<div class="admin_ua_ship_morkva_settings_line">
											<?php 
												$data = isset($mrkv_review_reminder['email']['btn_text']) ? $mrkv_review_reminder['email']['btn_text'] : '';
												$description = '';

												echo wp_kses($field_generator->get_input_text(__('Button text (Default: Leave review)', 'mrkv-review-reminder'), 'mrkv_review_reminder[email][btn_text]', $data, 'mrkv_review_reminder_email_btn_text' , '', __('Enter text...', 'mrkv-review-reminder'), $description), $allowed_tags);
											?>
										</div>
									</div>
								</div>
								<div class="admin_ua_ship_morkva_settings_line">
									<?php 
										$data = isset($mrkv_review_reminder['email']['header']) ? $mrkv_review_reminder['email']['header'] : '';
										$description = '';

										echo wp_kses($field_generator->get_input_text(__('Email header', 'mrkv-review-reminder'), 'mrkv_review_reminder[email][header]', $data, 'mrkv_review_reminder_email_header' , '', __('Enter header...', 'mrkv-review-reminder'), $description), $allowed_tags);
									?>
								</div>
								<div class="admin_ua_ship_morkva_settings_line">
									<?php
										$data = isset($mrkv_review_reminder['email']['content']) ? $mrkv_review_reminder['email']['content'] : '';
										$description = esc_html__('Plain text, no html', 'mrkv-review-reminder');

										echo wp_kses($field_generator->get_textarea(__('Email text', 'mrkv-review-reminder'), 'mrkv_review_reminder[email][content]', $data, 'mrkv_review_reminder_email_content' , '', __('Enter the email...', 'mrkv-review-reminder'), $description), $allowed_tags);
									?>
									<p><?php echo esc_html__('Shortcode firstname: [mrkv-firstname]', 'mrkv-review-reminder'); ?></p>
								</div>
							</section>
							<section id="log" class="mrkv_up_ship_shipping_tab_block">
								<h2><img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/clipboard-list-icon.svg'); ?>" alt="Log" title="Debug Log"><?php echo esc_html__('Debug Settings', 'mrkv-review-reminder'); ?></h2>
								<hr class="mrkv-ua-ship__hr">
								<div class="admin_ua_ship_morkva_settings_line">
									<?php
										$data = isset($mrkv_review_reminder['log']['order']) ? $mrkv_review_reminder['log']['order'] : '';
										echo wp_kses($field_generator->get_input_checkbox(__('Enable log to order note', 'mrkv-review-reminder'), 'mrkv_review_reminder[log][order]', $data, 'mrkv_review_reminder_email_auto'), $allowed_tags);
									?>
								</div>
								<h3 style="margin-top: 50px;">
									<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/letter-icon.svg'); ?>" alt="Email" title="Email"><?php echo esc_html__('Test letter', 'mrkv-review-reminder'); ?>
								</h3>
								<p><?php echo esc_html__('Send a test email to the specified email. The product will be taken the last one published', 'mrkv-review-reminder'); ?></p>
								<hr class="mrkv-ua-ship__hr">
								<div class="admin_ua_ship_morkva_settings_line admin_ua_ship_morkva_settings_line__row">
									<div class="admin_ua_ship_morkva_settings_line_added">
										<label for="mrkv_review_reminder_email_order_id"><?php echo esc_html__('Test Order ID (Default: last)', 'mrkv-review-reminder'); ?></label>
										<input type="text" id="mrkv_review_reminder_email_order_id" placeholder="Enter order id...">
									</div>
									<div class="admin_ua_ship_morkva_settings_line_added_email">
										<label for="mrkv_review_reminder_email_test"><?php echo esc_html__('Test Email address', 'mrkv-review-reminder'); ?></label>
										<div class="mrkv_review_reminder__send_test_email__line">
											<input id="mrkv_review_reminder_email_test" type="text"  placeholder="Enter email address..." value="">
											<div class="mrkv_review_reminder__send_test_email">
												<?php echo esc_html__('Send', 'mrkv-review-reminder'); ?>
												<div class="mrkv_ua_ship_create_invoice__loader"></div>
												<div class="mrkv_review_reminder_sent_text">
													<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/notice-success.svg'); ?>" alt="Email" title="Email">
													<?php echo esc_html__('Sent', 'mrkv-review-reminder'); ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</section>
						<?php echo esc_html(submit_button(esc_html__('Save', 'mrkv-review-reminder'))); ?>
					</div>
				</form>
			</div>
		</div>
		<div class="admin_mrkv_ua_shipping__block col-mrkv-3">
			<div class="admin_mrkv_ua_shipping__plugin-info mrkv_block_rounded">
				<div class="admin_mrkv_ua_shipping__plugin__support">
					<h2><?php echo esc_html__('Like this plugin?', 'mrkv-review-reminder'); ?></h2>
					<p>
						<?php echo esc_html__( 'Support our efforts with a', 'mrkv-review-reminder' ) . ' '; ?>
						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/star.svg'); ?>" alt="Star" alt="Star">
						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/star.svg'); ?>" alt="Star" alt="Star">
						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/star.svg'); ?>" alt="Star" alt="Star">
						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/star.svg'); ?>" alt="Star" alt="Star">
						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/star.svg'); ?>" alt="Star" alt="Star">
						<?php echo esc_html__( 'review at', 'mrkv-review-reminder' ); ?>
						<a href="https://wordpress.org/plugins/mrkv-review-reminder/" target="blanc">WordPress.org</a>
					</p>
					<a class="button button-primary mrkv-btn-sidebar-main" href="https://wordpress.org/plugins/mrkv-review-reminder/" target="blanc">
						<?php echo esc_html__( 'Leave', 'mrkv-review-reminder' ) . ' '; ?>
						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/star.svg'); ?>" alt="Star" alt="Star">
						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/star.svg'); ?>" alt="Star" alt="Star">
						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/star.svg'); ?>" alt="Star" alt="Star">
						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/star.svg'); ?>" alt="Star" alt="Star">
						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/star.svg'); ?>" alt="Star" alt="Star">
					</a>
					<p>
						<?php echo esc_html__( 'Isn’t good enough for a 5', 'mrkv-review-reminder' ) . ' '; ?>
						<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/star.svg'); ?>" alt="Star" alt="Star">? 
						<?php echo esc_html__( 'Contact us via the widget on our website, or check out', 'mrkv-review-reminder' ) . ' '; ?>
						<a href="https://docs.morkva.co.ua/uk?utm_source=plugin&utm_medium=sidebar&utm_campaign=review_reminder_free" target="blanc"><?php echo esc_html__( 'documantation', 'mrkv-review-reminder' ); ?></a>
					</p>
					<div class="mrkv-btns-line-sidebar" style="display: flex;gap: 4px;">
						<a class="button mrkv-btn-sidebar-black" href="https://morkva.co.ua/?utm_source=plugin&utm_medium=sidebar&utm_campaign=review_reminder_free" target="blanc">
							<?php echo esc_html__( 'Go to the website', 'mrkv-review-reminder' ); ?>
						</a>
						<a class="button mrkv-btn-sidebar-black" href="https://docs.morkva.co.ua/uk?utm_source=plugin&utm_medium=sidebar&utm_campaign=review_reminder_free" target="blanc">
							<?php echo esc_html__( 'Documantation', 'mrkv-review-reminder' ); ?>
						</a>
					</div>
				</div>
			</div>
			<div class="admin_mrkv_ua_shipping__plugin-info mrkv_block_rounded">
				<div class="admin_mrkv_ua_shipping__plugin__support">
					<h2><?php echo esc_html__('Check out pro-version', 'mrkv-review-reminder'); ?></h2>
					<ul>
						<li>
							<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/check.svg'); ?>" alt="Check" alt="Check">
							<?php echo esc_html__( 'Filter by category', 'mrkv-review-reminder' ); ?>
						</li>
						<li>
							<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/check.svg'); ?>" alt="Check" alt="Check">
							<?php echo esc_html__( 'Modify sender’s name', 'mrkv-review-reminder' ); ?>
						</li>
						<li>
							<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/check.svg'); ?>" alt="Check" alt="Check">
							<?php echo esc_html__( 'Modify sender’s email', 'mrkv-review-reminder' ); ?>
						</li>
						<li>
							<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/check.svg'); ?>" alt="Check" alt="Check">
							<?php echo esc_html__( 'Apply custom CSS', 'mrkv-review-reminder' ); ?>
						</li>
						<li><?php echo esc_html__( 'and more', 'mrkv-review-reminder' ); ?></li>
					</ul>
					<a class="button button-primary mrkv-btn-sidebar-main" href="https://morkva.co.ua/shop/review-reminder-pro/?utm_source=plugin&utm_medium=sidebar&utm_campaign=review_reminder_free" target="blanc">
						<?php echo esc_html__( 'Buy Pro-version', 'mrkv-review-reminder' ); ?>
					</a>
				</div>
			</div>
			<div class="admin_mrkv_ua_shipping__plugin-info mrkv_block_rounded">
				<div class="admin_mrkv_ua_shipping__plugin__support">
					<h2><?php echo esc_html__('Other free plugins', 'mrkv-review-reminder'); ?></h2>
					<?php
						$mrkv_review_reminder_response = wp_remote_get( 'https://morkva.co.ua/wp-json/pluginManagement/v2', array(
							'headers' => array(
							),
							'timeout' => 30,
							'redirection' => 5,
							'httpversion' => '1.1',
							'sslverify' => true
						));

						$mrkv_review_reminder_response_data = $mrkv_review_reminder_response['body'] ? json_decode( $mrkv_review_reminder_response['body'], true ) : null;
						$mrkv_review_reminder_plugins = $mrkv_review_reminder_response_data['plugins'] ?? [];

						if(!empty($mrkv_review_reminder_plugins))
						{
							?>
								<ul style="list-style: disc;padding-left: 17px;">
									<?php
										foreach($mrkv_review_reminder_plugins as $mrkv_review_reminder_plugin_slug => $mrkv_review_reminder_plugin_data)
										{
											if($mrkv_review_reminder_plugin_slug == 'mrkv-review-reminder'){ continue; }
											?>
												<li><a style="display:block; margin-bottom:5px;" href="<?php echo esc_attr($mrkv_review_reminder_plugin_data['url'] ?? ''); ?>" target="blanc" class="plugin_line"><?php echo esc_attr($mrkv_review_reminder_plugin_data['label'] ?? ''); ?></a></li>
											<?php
										}
									?>
								</ul>
							<?php
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>