<?php
/*
 * Security check
 * Exit if file accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/*
 * Fired when the plugin is deactivated.
 * This class defines all code necessary to run during plugin deactivation.
 *
 * @since 0.9.0
 */
class PlaybuzzDeactivator {

	/*
	 * Deactivate
	 */
	public static function deactivate() {

		// Delete option from the database
		// delete_option( 'playbuzz' );

		// Delete multisite option from the database
		// delete_site_option( 'playbuzz' );

	}

}



/*
 * Register WordPress deactivation hook
 * Run the activator class only when deactivation hook called
 *
 * @since 0.9.0
 */
function playbuzz_deactivation() {

	PlaybuzzDeactivator::deactivate();

}
register_deactivation_hook( __FILE__, 'playbuzz_deactivation' );
