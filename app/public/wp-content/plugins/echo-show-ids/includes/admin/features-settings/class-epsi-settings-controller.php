<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle saving feature settings.
 */
class EPSI_Settings_Controller {

	public function __construct() {
		add_action( 'admin_post_epsi_save_settings', array( $this, 'save_settings' ) );
		add_action( 'admin_post_nopriv_epsi_save_settings', array( $this, 'user_not_logged_in' ) );
	}

	/**
	 * Triggered when user submits Save to plugin settings. Saves the updated settings into the database
	 */
	public function save_settings() {

		// verify that request is authentic
		if ( ! isset( $_REQUEST['_wpnonce_epsi_save_settings'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce_epsi_save_settings'] ) ), '_wpnonce_epsi_save_settings' ) ) {
			$this->non_ajax_user_show_msg_and_die('ep_refresh_page');
		}

		// ensure user has correct permissions
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			$this->non_ajax_user_show_msg_and_die( 'ep_security_failed' );
		}

		// retrieve user input
		$field_specs = EPSI_Settings_Specs::get_fields_specification();
		$new_settings = $this->retrieve_form_fields( $field_specs );

		// sanitize and save settings in the database. see EPSI_Settings_DB class
		$result = epsi_get_instance()->settings->update_settings( $new_settings );
		if ( $result == null || is_wp_error( $result ) ) {
			$this->non_ajax_user_show_msg_and_die( 'ep_refresh_page_error' );
		}

		// we are done here
		$this->non_ajax_user_show_msg_and_die( 'ep_settings_saved' );
	}

	/**
	 * NON-AJAX: user tries to archive page -> redirect user to given page with attached error message to display
	 *
	 * @param $message
	 */
	private function non_ajax_user_show_msg_and_die( $message ) {
		wp_safe_redirect( admin_url( 'options-general.php?page=show-ids-settings&epsi_admin_notice=' . $message ) );
		die();
	}

	/**
	 * Place form fields into an array. If field doesn't exist don't consider it.
	 *
	 * @param $all_fields_specs
	 *
	 * @return array of name - value pairs
	 */
	private function retrieve_form_fields( $all_fields_specs ) {

		$post_data = is_array( $_POST ) ? $_POST : array();

		$name_values = array();
		foreach ( $all_fields_specs as $key => $spec ) {

			// checkboxes in a box have zero or more values
			$is_multiselect =  $spec['type'] == EPSI_Input_Filter::MULTI_SELECT;
			if ( $is_multiselect ||  $spec['type'] == EPSI_Input_Filter::MULTI_SELECT_NOT) {

				$multi_selects = array();
				foreach ( $post_data as $submitted_key => $submitted_value) {
					if ( ! empty( $submitted_key ) && strpos( $submitted_key, 'epsi_' . $key ) === 0) {

						$chunks = $is_multiselect ?  explode('[[-,-]]', $submitted_value) : explode('[[-HIDDEN-]]', $submitted_value);
						if ( empty( $chunks[0] ) || empty( $chunks[1] ) || ! empty( $chunks[2] ) ) {
							continue;
						}

						if ( $is_multiselect ) {
							$multi_selects[$chunks[0]] = $chunks[1];
						} else if ( ! empty( $submitted_value ) && strpos( $submitted_value, '[[-HIDDEN-]]' ) !== false ) {

							$multi_selects[$chunks[0]] = $chunks[1];
						}
					}
				}
				$name_values += array( $key => $multi_selects );
			}

			$input_value = empty( $submitted_fields[ 'epsi_' . $key ] ) ? '' : trim( $submitted_fields[ 'epsi_' . $key ] );
			$name_values += array( $key => $input_value);
		}

		return $name_values;
	}

	public function user_not_logged_in() {
		wp_die( wp_json_encode( array( 'error' => true, 'message' => '<p>' . esc_html__( 'You are not logged in. Refresh your page and log in.', 'echo-show-ids' ) . '</p>', 'error_code' => 'EPSI-11' ) ) );
	}
}