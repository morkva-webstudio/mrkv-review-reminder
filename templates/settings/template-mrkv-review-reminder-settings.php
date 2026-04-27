<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
	$current_page = '/wp-admin/admin.php?page=mrkv_review_reminder_settings';
?>
<div class="admin_mrkv_ua_shipping_page">
	<div class="admin_mrkv_ua_shipping_page__header">
		<div class="admin_mrkv_ua_shipping__header mrkv_block_rounded">
			<div class="admin_mrkv_ua_shipping__header__content">
				<a class="admin_mrkv_ua_shipping__header_img" href="<?php echo esc_url($current_page); ?>">
					<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/clock.svg'); ?>" alt="Morkva Review Reminder" title="Morkva Review Reminder">
				</a>
				<a href="<?php echo esc_url($current_page); ?>"><?php echo esc_html__('Global', 'mrkv-review-reminder'); ?></a>
				<a class="admin_mrkv_ua_shipping_morkva-logo" href="https://morkva.co.ua/" target="blanc">
					<img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/morkva-logo.svg'); ?>" alt="Morkva" title="Morkva">
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
								<div class="admin_ua_ship_morkva_settings_line">
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
							</section>
						<?php echo esc_html(submit_button(esc_html__('Save', 'mrkv-review-reminder'))); ?>
					</div>
				</form>
			</div>
		</div>
		<div class="admin_mrkv_ua_shipping__block col-mrkv-3">
			<div class="admin_mrkv_ua_shipping__plugin-info mrkv_block_rounded">
				<div class="admin_mrkv_ua_shipping__plugin__support">
					<h2><img src="<?php echo esc_url(MRKV_REVIEW_REMINDER_IMG_URL . '/global/question-icon.svg'); ?>" alt="Question" title="Question"><?php echo esc_html__('Support', 'mrkv-review-reminder'); ?></h2>
					<p><?php echo esc_html__('Need help or customization?', 'mrkv-review-reminder'); ?></p>
					<a href="mailto:support@morkva.co.ua" class="button button-primary admin_mrkv_ua_shipping__btn" target="_blank"><?php echo esc_html__('E-mail', 'mrkv-review-reminder'); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>