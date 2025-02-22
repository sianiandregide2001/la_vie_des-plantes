<?php
/**
 * Uninstall this plugin
 *
 */

// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/** Delete plugin options */
delete_option( 'epsi_settings' );
