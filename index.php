<?php
/*
Plugin Name: PlayBuzz
Plugin URI: http://www.PlayBuzz.com/
Description: Plugin for embedding PlayBuzz playful content in WordPress sites.
Version: 0.1
Author: PlayBuzz
Author URI: http://www.PlayBuzz.com/
Text Domain: playbuzz
Domain Path: /lang
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/



/*
 * WordPress Admin Options Page
 */
require ( plugin_dir_path( __FILE__ ) . 'admin.php' );
new PlayBuzzAdmin();



/*
 * Register WordPress Shortcodes
 */
include_once ( plugin_dir_path( __FILE__ ) . 'shortcodes.php' );



/*
 * Register WordPress Sidebar Widgets
 */
include_once ( plugin_dir_path( __FILE__ ) . 'widgets.php' );
