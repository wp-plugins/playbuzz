<?php
/*
 * Security check
 * Exit if file accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/*
 * Fired when the plugin is activated.
 * This class defines all code necessary to run during plugin activation.
 *
 * @since 0.9.0
 */
class PlaybuzzActivator {

	/*
	 * Activate
	 */
	public static function activate() {

		// Check if the user has plugin activation privilege
		if ( ! current_user_can( 'activate_plugins' ) )
			return;

		// Set Default values
		$data = array(

			// General
			'key'               => 'default',
			'pb_user'           => '',

			// items
			'info'              => '1',
			'shares'            => '1',
			'comments'          => '1',
			'recommend'         => '1',
			'margin-top'        => '0',
			'embeddedon'        => 'content',

			// Recommendations
			'active'            => 'false',
			'show'              => 'footer',
			'view'              => 'large_images',
			'items'             => '3',
			'links'             => 'https://www.playbuzz.com',
			'section-page'		=>	'',

			// Tags
			'tags-mix'          => '1',
			'tags-fun'          => '',
			'tags-pop'          => '',
			'tags-geek'         => '',
			'tags-sports'       => '',
			'tags-editors-pick' => '',
			'more-tags'         => '',

		);

		// Set API Key
		if ( 'default' == $data['key']  ) {

			// Extract host domain
			$domain = parse_url( home_url(), PHP_URL_HOST );

			// Remove "www." from the domain
			$api = str_replace( 'www.', '', $domain );

			// Set API
			$data['key'] = $api;

		}

		// Update options on database
		if ( !get_option( 'playbuzz' ) ) {

			update_option( 'playbuzz', $data );

		} else {

			// If we already have a settings object in memory, add the new items
			update_option( 'playbuzz', array_merge( $data, get_option( 'playbuzz' ) ) );

		}

	}

}



/*
 * Register WordPress activation hook
 * Run the activator class only when activation hook called
 *
 * @since 0.9.0
 */
function playbuzz_activation() {

	PlaybuzzActivator::activate();

}
register_activation_hook( __FILE__, 'playbuzz_activation' );
