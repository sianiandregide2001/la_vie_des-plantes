<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *
 * For input data:
 * 1. Sanitize data
 * 2. Based on field type, also validate data
 * Internal fields have spec with 'internal' => true
 */
class EPSI_Input_Filter {

	const TEXT = 'text';                // use Text or Textarea input
	const MULTI_SELECT = 'multi_select';
	const MULTI_SELECT_NOT = 'multi_select_not';

	/**
	 * Validate and sanitize input. If input not in spec then exclude it.
	 *
	 * @param array $input to sanitize
	 *
	 * @return array|WP_Error returns key - value pairs
	 */
	public function validate_and_sanitize( array $input ) {

		if ( empty( $input ) ) {
			return new WP_Error( 'invalid_input', esc_html__( 'Error Occurred', 'echo-knowledge-base' ) . ' (5532)' );
		}

		$sanitized_input = array();
		$errors = array();
		$specification = EPSI_Settings_Specs::get_fields_specification();

		// filter each field
		foreach ( $input as $key => $input_value ) {

			if ( ! isset( $specification[$key] ) || $input_value === null ) {
				continue;
			}

			$field_spec = $specification[$key];

			$defaults = array(
				'label'       => "Label",
				'type'        => EPSI_Input_Filter::TEXT,
				'optional'    => 'false',
				'max'         => '20',
				'min'         => '3',
				'options'     => array(),
				'default'     => ''
			);
			$field_spec = wp_parse_args( $field_spec, $defaults );

			// SANITIZE FIELD
			$type = empty( $field_spec['type'] ) ? '' : $field_spec['type'];
			switch ( $type ) {

				case self::MULTI_SELECT:
				case self::MULTI_SELECT_NOT:

					$input_value = is_array( $input_value ) ? $input_value : array();
					$input_adj = array();
					foreach ( $input_value as $arr_key => $arr_value ) {

						// one choice can have multiple true [key,value] pairs separated by comma
						$arr_value = empty($arr_value) ? '' : $arr_value;
						$tmp = explode(',', $arr_value);
						if ( ! empty($tmp[0]) && ! empty($tmp[1]) ) {
							$arr_key = $tmp[0];
							$arr_value = $tmp[1];
						}
						$input_adj[$arr_key] = sanitize_text_field($arr_value);
					}
					$input_value = $input_adj;
					break;

				default:
					$input_value = trim( sanitize_text_field( $input_value ) );
			}

			// validate/sanitize input
			$result = $this->filter_input_field( $input_value, $field_spec );
			if ( is_wp_error( $result ) ) {
				$errors[] = '<strong>' . $field_spec['label'] . '</strong> is ' . $result->get_error_message();
			} else {
				$sanitized_input[$key] = $result;
			}

		} // foreach

		if ( empty( $errors ) ) {
			return $sanitized_input;
		}

		return new WP_Error('invalid_input', 'validation failed', $errors );
	}

	private function filter_input_field( $value, $field_spec ) {

		// further sanitize the field
		switch ( $field_spec['type'] ) {

			case self::TEXT:
				return $this->filter_text( $value, $field_spec );
				break;

			case self::MULTI_SELECT:
			case self::MULTI_SELECT_NOT:
				// no filtering needed;
				return $value;
				break;

			default:
				return new WP_Error('epsi-invalid-input-type', esc_html__( 'Error Occurred', 'echo-knowledge-base' ) . ' - ' . $field_spec['type']);
		}
	}

	/**
	 * Sanitize and validate text. Output WP Error if text too big/small
	 *
	 * @param $text
	 * @param $field_spec
	 *
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_text( $text, $field_spec ) {

		if ( is_array( $text ) ) {
			$text = '';
		}

		if ( strlen( $text ) > $field_spec['max'] ) {
			$nof_chars_to_remove = strlen( $text ) - $field_spec['max'];

			/* translators: %d: number of characters to remove by user when entering too long string. */
			$msg = sprintf( _n( 'The value is too long. Remove %d character.', 'The value is too long. Remove %d characters.', $nof_chars_to_remove, 'creative-addons-for-elementor' ), $nof_chars_to_remove );
			return new WP_Error('filter_text_big', $msg );
		}

		if ( ( empty( $text ) && ! empty( $field_spec['mandatory'] ) ) || ( strlen( $text ) > 0 && strlen( $text ) < $field_spec['min'] ) ) {
			$nof_chars_to_remove = $field_spec['min'] - strlen($text);

			/* translators: %d: number of characters to add by user when entering too short string. */
			$msg = sprintf( _n( 'The value is too short. Add at least %d character.', 'The value is too short. Add at least %d characters.', $nof_chars_to_remove, 'creative-addons-for-elementor' ), $nof_chars_to_remove );
			return new WP_Error('filter_text_small', $msg );
		}

		return $text;
	}
}
