<?php
/**
 * Cleanup on uninstall
 *
 * @link URL
 *
 * @package jm-buddy-translate
 * @subpackage uninstall
 * @since 1.0.7
 */

if (
	!defined( 'WP_UNINSTALL_PLUGIN' )
	||
	!WP_UNINSTALL_PLUGIN
	||
	dirname( WP_UNINSTALL_PLUGIN ) != dirname( plugin_basename( __FILE__ ) )
) {
	status_header( 404 );
	exit;
}
// Delete all compact options
delete_option( 'jm_buddy_translate_options'        );
