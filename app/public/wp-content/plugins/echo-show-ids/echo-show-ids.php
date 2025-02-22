<?php
/**
 * Plugin Name: Show IDs by Echo
 * Plugin URI: http://www.echoplugins.com
 * Description: Show IDs on admin pages for posts, pages, categories, taxonomies, custom post types and more.
 * Version: 1.3.0
 * Author: Echo Plugins
 * Author URI: http://www.echoplugins.com
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Show IDs by Echo Plugins is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Show IDs by Echo Plugins is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Show IDs by Echo Plugins. If not, see <http://www.gnu.org/licenses/>.
 *
*/

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Main class to load the plugin.
 *
 * Singleton
 */
final class Echo_Show_IDs {

	/* @var Echo_Show_IDs */
	private static $instance;

	public static $version = '1.3.0';
	public static $plugin_dir;
	public static $plugin_url;
	public static $plugin_file = __FILE__;

	/* @var EPSI_Settings_DB */
	public $settings;

	/**
	 * Initialise the plugin
	 */
	private function __construct() {
		self::$plugin_dir = plugin_dir_path(  __FILE__ );
		self::$plugin_url = plugin_dir_url( __FILE__ );
	}

	/**
	 * Retrieve or create a new instance of this main class (avoid global vars)
	 *
	 * @static
	 * @return Echo_Show_IDs
	 */
	public static function instance() {

		if ( ! empty( self::$instance ) && ( self::$instance instanceof Echo_Show_IDs ) ) {
			return self::$instance;
		}

		self::$instance = new Echo_Show_IDs();

		self::$instance->includes();
		self::$instance->setup_system();
		self::$instance->setup_plugin();

		return self::$instance;
	}

	private function includes() {
		if ( is_admin() ) {
			require_once $this::$plugin_dir . 'includes/class-epsi-show-ids.php';
			require_once $this::$plugin_dir . 'includes/class-epsi-input-filter.php';
			require_once $this::$plugin_dir . 'includes/class-epsi-html-elements.php';
			require_once self::$plugin_dir  . 'includes/admin/features-settings/features-settings-page.php';
			require_once $this::$plugin_dir . 'includes/admin/features-settings/class-epsi-settings-controller.php';
			require_once $this::$plugin_dir . 'includes/admin/features-settings/class-epsi-settings-db.php';
			require_once $this::$plugin_dir . 'includes/admin/features-settings/class-epsi-settings-specs.php';
		}
	}

	/**
	 * Setup class autoloading and other support functions
	 */
	private function setup_system() {
		if ( is_admin() ) {
			self::$instance->settings = new EPSI_Settings_DB();
		}
	}

	/**
	 * Setup plugin before it runs i.e. ensure settings have (default) values etc.
	 */
	private function setup_plugin() {

		// plugin setup is based on whether we are serving the front-end or the back-end

		// 1) IF is admin or CLI or AJAX front-end request (from any user) then load related files
		if ( is_admin() ) {

			// only when on admin page - initialize hook for saving config and settings
			// phpcs:disable WordPress.Security.NonceVerification.Recommended
			if ( ! empty( $_REQUEST['action']) && !strncmp( $_REQUEST['action'], 'epsi_', strlen('epsi_') ) ) {
				new EPSI_Settings_Controller();
			}

			new EPSI_Show_IDs();
		}
	}

	// Don't allow this singleton to be cloned.
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, 'Invalid (#1)', '4.0' );
	}

	// Don't allow un-serializing of the class except when testing
	public function __wakeup() {
		if ( strpos($GLOBALS['argv'][0], 'phpunit') === false ) {
			_doing_it_wrong( __FUNCTION__, 'Invalid (#1)', '4.0' );
		}
	}
}

/**
 * Returns the single instance of this class
 *
 * @return object - this class instance
 */
function epsi_get_instance() {
	return Echo_Show_IDs::instance();
}
epsi_get_instance();


/**
 * Adds various links for plugin on the Plugins page displayed on the left
 *
 * @param   array $links contains current links for this plugin
 * @return  array returns an array of links
 */
function epsi_add_plugin_action_links ( $links ) {

	$my_links = array(
			'Settings'  => '<a href="' . admin_url('options-general.php?page=show-ids-settings') . '">'. __( 'Settings' ) . '</a>',
			'Support'   => '<a href="http://www.echoplugins.com/contact-us/?inquiry-type=technical" target="_blank">' . __( 'Support' ) . '</a>'
	);

	return array_merge( $my_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'epsi_add_plugin_action_links', 10, 2 );

/**
 * Register plugin options as a sub-menu for WordPress Settings menu
 */
function epsi_add_plugin_menus() {

	$feature_settings_page = add_options_page( esc_html__( 'Show IDs', 'echo-show-ids' ), esc_html( 'Show IDs', 'echo-show-ids' ), 'manage_options', 'show-ids-settings', 'epsi_display_features_settings_page' );
	if ( $feature_settings_page === false ) {
		return;
	}

	// Register (i.e. whitelist) the option for the configuration pages.
	register_setting('epsi_settings', 'epsi_settings');

	// load scripts needed for Features Settings page only on that page
	add_action( 'load-' . $feature_settings_page, 'epsi_load_admin_page_resources' );
}
add_action('admin_menu', 'epsi_add_plugin_menus' );

function epsi_load_admin_page_resources() {
	add_action( 'admin_enqueue_scripts', 'epsi_load_admin_pages_resources' );
}

function epsi_load_admin_pages_resources(  ) {

	// if SCRIPT_DEBUG is off then use minified scripts
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script('epsi-admin-bar-scripts', Echo_Show_IDs::$plugin_url . 'js/admin-pages' . $suffix . '.js', array( 'jquery', 'jquery-ui-core','jquery-ui-dialog','jquery-effects-core' ), Echo_Show_IDs::$version );
	wp_enqueue_style('epsi-admin-pages-styles', Echo_Show_IDs::$plugin_url . 'css/admin-pages' . $suffix . '.css', array(), Echo_Show_IDs::$version );
	wp_enqueue_style( 'wp-jquery-ui-dialog' );
}
function epsi_load_admin_feature_resources(  ) {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_style('epsi-admin-feature-styles', Echo_Show_IDs::$plugin_url . 'css/admin-features' . $suffix . '.css', array(), Echo_Show_IDs::$version );

}
add_action( 'admin_enqueue_scripts', 'epsi_load_admin_feature_resources' );

/**
 * Show noticers for admin at the top of the page
 */
function epsi_show_admin_notices() {

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$admin_notice =  empty( $_GET['epsi_admin_notice'] ) ? '' : $_GET['epsi_admin_notice'];
	if ( empty( $admin_notice ) ) {
		return;
	}

	$type = 'error';
	switch ( $admin_notice ) {

		case 'ep_settings_saved' :
			$message = esc_html__( 'Settings saved', 'echo-show-ids' );
			$type   = 'success';
			break;
		case 'ep_refresh_page' :
			$message = esc_html__( 'Refresh your page', 'echo-show-ids' );
			break;
		case 'ep_refresh_page_error' :
			$message = esc_html__( 'Error occurred. Please refresh your browser and try again', 'echo-show-ids' );
			break;
		case 'ep_security_failed' :
			$message = esc_html__( 'You do not have permission', 'echo-show-ids' );
			break;
		default:
			$message = esc_html__( 'unknown error', 'echo-show-ids' ) . ' (1223)';
			break;
	}

	echo
		"<div id='epsi-top-notice-message'>
			<div class='epsi_notification'>
				<span class='$type'>
					<!--<h4>title</h4> -->
					<p>$message</p>
				</span>
			</div>
		</div>";
}
add_action( 'admin_notices', 'epsi_show_admin_notices' );
