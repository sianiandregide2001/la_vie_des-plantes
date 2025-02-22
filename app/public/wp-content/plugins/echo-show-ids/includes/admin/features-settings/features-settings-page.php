<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display each active feature settings tab
 *
 * @copyright   Copyright (C) 2016, Zraly Studio
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
function epsi_display_features_settings_page() {

	$feature_settings = epsi_get_instance()->settings->get_settings();

	ob_start();  ?>

	<div class="wrap" xmlns="http://www.w3.org/1999/html">
		<div class="epsi_admin_wrap">

			<div id="epsi-top-notice-message"><a hidden id="top"></a></div>
			<h1><?php esc_html_e( 'Welcome to Show IDs Plugin', 'echo-show-ids' ); ?></h1>     <?php
			epsi_display_page_details( $feature_settings );    ?>

		</div>
	</div>  <?php

	echo ob_get_clean();
}

/**
 * Display all configuration fields
 *
 * @param $feature_settings
 */
function epsi_display_page_details( $feature_settings ) {

	$feature_specs = EPSI_Settings_Specs::get_fields_specification();
	$form = new EPSI_html_elements();   ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

		<div id="epsi-tabs" class="form-option-tabs">
			<section class="main-nav">
				<ul class="nav-tabs">
					<li class="active">
						<h2><?php _e( 'Settings' ); ?></h2>
					</li>
					<li>
						<h2><?php _e( 'Our Other Free Plugins', 'echo-show-ids' ); ?></h2>
					</li>
					<li>
						<h2><?php _e( 'Support' ); ?></h2>
					</li>
				</ul>
			</section>
			<div class="panel-container">
				<div class="tab-panel active">
					<section class="form-options">
						<ul>
							<li class="epsi-config-item">
								<?php echo $form->checkboxes_multi_select( $feature_specs['where_to_display_ids'] + array( 'value' => $feature_settings['where_to_display_ids'],
								                                                                                           'options' => array('test1' => 'test1', 'test2' => 'test2'),
								                                                                                           'main_class' => 'epsi-checkbox-columns')); ?>
							</li>
						</ul>
					</section>
				</div>
				<div class='tab-panel'>
					<section>
						<div class='epsi_row'>
							<div class="epsi_col epsi_col_20">
								<h3><?php esc_html_e( 'Our Other Free Plugins', 'echo-show-ids' ); ?></h3>
								<p><?php echo esc_html__( 'Knowledge Base with articles and categories', 'echo-show-ids' ) . ': ' . '<a href="https://wordpress.org/plugins/echo-knowledge-base/" target="_blank">Echo Knowledge Base</a></p>'; ?>
								<p><?php echo esc_html__( 'AI Chat, FAQs, Contact Form on any page', 'echo-show-ids' ) . ': ' . '<a href="https://wordpress.org/plugins/help-dialog/" target="_blank">Help Dialog</a></p>'; ?>
								<p><?php echo esc_html__( 'Custom widgets for Elementor', 'echo-show-ids' ) . ': ' . '<a href="https://wordpress.org/plugins/creative-addons-for-elementor/" target="_blank">Creative Add-on for Elementor</a></p>'; ?>
								<p><?php echo esc_html__( 'Scroll down on page with an arrow', 'echo-show-ids' ) . ': ' . '<a href="https://wordpress.org/plugins/scroll-down-arrow/" target="_blank">Scroll Down Arrow</a></p>'; ?>
							</div>
						</div>
					</section>
				</div>
				<div class="tab-panel">
					<section>
						<div class="epsi_row">
							<div class="epsi_col epsi_col_20">
								<h3><?php _e( 'Support' ); ?></h3>
								<p><?php esc_html_e( 'If you encounter an issue or have a question, please submit your request below.', 'echo-show-ids' ); ?></p>
								<a class="button primary-btn" href="http://www.echoplugins.com/contact-us/?inquiry-type=technical" target="_blank"><?php esc_html_e( 'Contact us', 'echo-show-ids' ); ?></a>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>

		<section class="epsi-save-settings">	<?php
			$form->save_settings_button();  ?>
		</section>

		<div id="epsi-dialog-info-icon" title="">
			<p id="epsi-dialog-info-icon-msg"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span></p>
		</div>

	</form>   <?php
}
