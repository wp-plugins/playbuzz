<?php
/*
Plugin Name: Playbuzz
Plugin URI: https://www.playbuzz.com/
Description: Plugin for embedding customized playful content from Playbuzz in your WordPress site.
Version: 0.6.0
Author: playbuzz
Author URI: https://www.playbuzz.com/
Text Domain: playbuzz
Domain Path: /lang
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/



/*
 * Abort if this file is called directly.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}



/*
 * WordPress Admin Options Page
 */
include_once ( plugin_dir_path( __FILE__ ) . 'admin.php' );



/*
 * Add oEmbed support
 */
include_once ( plugin_dir_path( __FILE__ ) . 'oembed.php' );



/*
 * Add WordPress Shortcodes
 */
include_once ( plugin_dir_path( __FILE__ ) . 'shortcodes.php' );



/*
 * Add WordPress Sidebar Widgets
 */
include_once ( plugin_dir_path( __FILE__ ) . 'widgets.php' );



/*
 * Add TinyMCE plugin
 */
include_once ( plugin_dir_path( __FILE__ ) . 'tinymce.php' );



/*
 * Add settings link on plugin page
 */
function playbuzz_settings_link( $links ) {
	$links[] = '<a href="options-general.php?page=playbuzz">' . __( 'Settings' ) . '</a>';
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'playbuzz_settings_link' );



/*
 * Hooks fired when the Plugin is activated and deactivated
 */
register_activation_hook(   __FILE__ , array( 'PlaybuzzAdmin', 'activate'   ) );
register_deactivation_hook( __FILE__ , array( 'PlaybuzzAdmin', 'deactivate' ) );
