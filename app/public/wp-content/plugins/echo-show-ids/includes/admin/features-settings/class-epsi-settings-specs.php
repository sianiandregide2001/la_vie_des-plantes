<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Collects feature specifications from each feature.
 */
class EPSI_Settings_Specs {

	/**
	 * Defines data needed for display, initialization and validation/sanitation of settings
	 *
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'optional' => 'true' )
	 *
	 * @return array with settings specification
	 */
	public static function get_fields_specification() {
		/** @noinspection PhpUndefinedClassInspection */
		$feature_settings = array (
				'where_to_display_ids' => array(
						'label'       => 'Where to Display IDs',
						'name'        => 'where_to_display_ids',
						'info'        => "Show IDs in a new 'ID' column and/or below item title. @ " . Echo_Show_IDs::$plugin_url . 'img/settings/where-to-display-IDs.jpg',
						'type'        => EPSI_Input_Filter::MULTI_SELECT,
						'options'     => array(
											'epsi-show-inline' => 'Below Item Title',
											'epsi-show-column' => "In an 'ID' Column" ),
						'default'     => array(
											'epsi-show-inline' => 'Below Item Title',
											'epsi-show-column' => "In an 'ID' Column" ),
						'optional'    => true
				)
		);
		return $feature_settings;
	}

	/**
	 * Get default settings
	 *
	 * @return array contains default setting values
	 */
	public static function get_default_settings() {
		$setting_specs = self::get_fields_specification();
		if ( ! is_array($setting_specs) ) {
			return array();
		}

		$configuration = array();
		foreach( $setting_specs as $key => $spec ) {
			$default = isset($spec['default']) ? $spec['default'] : '';
			$configuration += array( $key => $default );
		}

		return $configuration;
	}

	/**
	 * Get names of all configuration items for settings
	 * @return array
	 */
	public static function get_specs_item_names() {
		return array_keys( self::get_fields_specification() );
	}
}