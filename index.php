<?php
/*
Plugin Name: playbuzz
Plugin URI: https://www.playbuzz.com/
Description: Plugin for embedding playbuzz Playful Content in WordPress sites.
Version: 0.5.0
Author: PlayBuzz
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
require ( plugin_dir_path( __FILE__ ) . 'admin.php' );
new PlaybuzzAdmin();


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
new PlaybuzzTinyMCE();


/*
 * Add settings link on plugin page
 */
function playbuzz_settings_link( $links ) {
	$settings_link = '<a href="options-general.php?page=playbuzz">' . __( 'Settings' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'playbuzz_settings_link' );


/*
 * Hooks fired when the Plugin is activated and deactivated
 */
register_activation_hook(   __FILE__ , array( 'PlaybuzzAdmin', 'activate'   ) );
register_deactivation_hook( __FILE__ , array( 'PlaybuzzAdmin', 'deactivate' ) );
