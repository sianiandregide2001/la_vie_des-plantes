<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * All methods are static with no side effects i.e. don't change some global states and don't exhibit other non-testable behaviors
 *
 * @copyright   Copyright (C) 2016, Zraly Studio
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPSI_html_elements {

	/**
	 * Renders an HTML Text field
	 *
	 * @param array $args Arguments for the text field
	 * @return string Text field
	 */
	public function text( $args = array() ) {

		$defaults = array(
				'id'           => '',
				'name'         => 'text',
				'value'        => '',
				'label'        => '',
				'desc'         => '',
				'info'         => '',
				'placeholder'  => '',
				'class'        => 'regular-text',
				'readonly'     => false,  // will not be submitted
				'autocomplete' => false,
				'data'         => false,
				'size'          => 3,
				'max'          => 50
		);
		$args = wp_parse_args( $args, $defaults );

		$id =  esc_attr('epsi_' . $args['name']);
		$autocomplete = ( $args['autocomplete'] ? 'on' : 'off' );
		$readonly = $args['readonly'] ? ' readonly' : '';

		$data_escaped = self::get_data_escaped( $args['data'] );

		$output = "<span id='{$id}-wrap'>";

		$output .= "<label class='epsi-label' for='{$id}'>" . esc_html( $args['label'] ) . "</label>";

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="epsi-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$output .= "<input type='text' name='" . esc_attr( $id ) . "' id='" . esc_attr( $id ) . "' class='" . esc_attr( $args['class'] ) . "' autocomplete='" . esc_attr( $autocomplete ) . "' value='" . esc_attr( $args['value'] ) .
		           "' placeholder='" . esc_attr( $args['placeholder'] ) . "'" . $data_escaped . ' ' . esc_attr( $readonly ) . " maxlength='" . esc_attr( $args['max'] ) . "' />";

		if ( ! empty( $args['info'] ) ) {
			$output .= "<span class='epsi-info-icon'><p class='hidden'>" . esc_html( $args['info'] ) . "</p></span>";
		}

		$output .= '</span>';

		return $output;
	}

	/**
	 * Renders several HTML checkboxes in several columns
	 *
	 * @param array $args
	 * @param string $empty_list
	 *
	 * @return string
	 */
	public function checkboxes_multi_select( $args = array(), $empty_list = 'Not Available' ) {
		$output = '';
		$defaults = array(
			'id'           => 'checkbox',
			'name'         => 'checkbox',
			'value'        => array(),
			'label'        => '',
			'title'        => '',
			'desc'         => '',
			'info'         => '',
			'class'        => 'epsi-checkbox',
			'main_class'   => '',
			'current'      => null,
			'options'      => array()
		);
		$args = wp_parse_args( $args, $defaults );

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="epsi-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$ix = 0;
		$output .= '<div class="epsi-checkboxes-vertical '.esc_attr( $args['main_class'] ).'">';
		$output .= '<h4 class="epsi-label">'.esc_html( $args['label'] ).'</h4>';
		if ( ! empty( $args['info'] ) ) {
			$info = is_array( $args['info'] ) ? $args['info'][$ix] : $args['info'] ;
			$output .= '<span class="epsi-info-icon"><p class="hidden">' . esc_html( $info ) . '</p></span>';
		}
		$output .= '<ul>';

		if ( empty( $args['options'] ) ) {
			$args['options'] = array();
			$output .= $empty_list;
		}

		foreach( $args['options'] as $key => $label ) {

			$tmp_value = is_array($args['value']) ? $args['value'] : array();

			if ( $args['type'] == EPSI_Input_Filter::MULTI_SELECT_NOT ) {
				$checked = in_array( $key, array_keys( $tmp_value ) ) ? '' : 'checked';
			} else {
				$checked = in_array( $key, array_keys( $tmp_value ) ) ? 'checked' : '';
			}

			$input_attr_escaped = array(
				'id'    => empty( $args['name'] ) ? '' :  ' id="' . esc_attr( 'epsi_' . $args['name'] . $ix ) . '"',
				'value' => ' value="'. esc_attr( $key . '[[-,-]]' . $label ) . '" ',
				'name'  => ' name="epsi_' . esc_attr( $args['name'] ) . '_' . esc_attr( $ix ) . '" ',
				'class' => ' class="' . esc_attr( $args['class'] ).'" ',
				'checked' => esc_attr( $checked )
			);

			$label = str_replace( ',', '', $label );

			$output .= '<li>';
			if ( $args['type'] == EPSI_Input_Filter::MULTI_SELECT_NOT ) {
				$output .= '<input type="hidden" value="'. esc_attr( $key . '[[-HIDDEN-]]' . $label ) . '" name="epsi_' . esc_attr( $args['name'] ) . '_' . esc_attr( $ix ) . '">';
			}
			$output .= '<input type="checkbox"';
			$output .= $input_attr_escaped[ 'id' ];
			$output .= $input_attr_escaped[ 'class' ];
			$output .= $input_attr_escaped[ 'name' ];
			$output .= $input_attr_escaped[ 'value' ];
			$output .= $input_attr_escaped[ 'checked' ];
			$output .= ' />';

			$output .= '<label';
			$output .= ' for="epsi_'.  esc_attr( $args['name'] . $ix ) . '" >';
			$output .= esc_html( $label );
			$output .= '</label>';
			$output .= '</li>';
			$ix++;
		} //foreach

		$output .= '</ul>';

		$output .= '</div>';

		return $output;
	}

	/**
	 * Output Save Settings button
	 */
	public function save_settings_button() {   ?>
		<div class="submit">
			<input type="hidden" id="_wpnonce_epsi_save_settings" name="_wpnonce_epsi_save_settings" value="<?php echo wp_create_nonce( '_wpnonce_epsi_save_settings' ); ?>"/>
			<input type="hidden" name="action" value="epsi_save_settings"/>
			<input type="submit" class="primary-btn" value="<?php esc_html_e( 'Save', 'echo-show-ids' ); ?>" />
		</div>  <?php
	}

	/**
	 * Return data attributes with escaped keys and values
	 *
	 * @param $data
	 * @return string
	 */
	public static function get_data_escaped( $data ) {
		$data_escaped = '';

		if ( empty( $data ) ) {
			return $data_escaped;
		}

		foreach ( $data as $key => $value ) {
			$data_escaped .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
		}

		return $data_escaped;
	}
}
