<?php
/*
 * PlayBuzz TinyMCE Plugin
 * TinyMCE plugin for PlayBuzz on WordPress editor.
 *
 * @since 0.4.0
 */
class PlayBuzzTinyMCE {

	public $name = 'playbuzz';

	/*
	 * Constructor
	 */
	function __construct() {

		add_action( 'admin_init', array( $this, 'register_stuff' ) );

	}

	/*
	 * Create TinyMCE 
	 */
	function register_stuff() {

		global $wp_version;

		// Check WordPress Version (We need WordPress 3.9 to use TinyMCE 4.0)
		if ( $wp_version < 3.9 )
			return;

		// Check if the user has editing privilege
		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') )
			return;

		// Check if the user uses rich editing
		if ( 'false' == get_user_option('rich_editing') )
			return;

		// Add PlayBuzz button above the editor
		//add_action( 'media_buttons', array( $this, 'register_button_above_the_editor' ) );

		// Add PlayBuzz button to the TinyMCE editor
		add_filter( 'mce_buttons', array( $this, 'register_tinymce_button' ) );

		// Register TinyMCE editor style CSS
		add_filter( 'mce_css', array( $this, 'register_tinymce_css' ) );

		// Register TinyMCE JavaScript
		add_filter( 'mce_external_plugins', array( $this, 'register_tinymce_js' ) );

	}

	/*
	 * Add PlayBuzz button above the editor
	 *
	function register_button_above_the_editor() {

		echo '<a href="#" id="insert-playbuzz-button" class="button insert-playbuzz playbuzz" data-editor="content">' . __( 'Add PlayBuzz', 'playbuzz' ) . '</a>';

	}
	*/

	/*
	 * Add PlayBuzz button to the TinyMCE editor
	 */
	function register_tinymce_button( $buttons ) {

		array_push( $buttons, $this->name );
		return $buttons;

	}

	/*
	 * Register TinyMCE editor style CSS
	 */
	function register_tinymce_css( $mce_css ){

		// If the site has other css, add a comma
		if ( ! empty( $mce_css ) )
			$mce_css .= ',';

		// Add PlayBuzz TinyMCE editor css
		$mce_css .= plugins_url( 'css/editor.css', __FILE__ );

		// Return the css list
		return $mce_css;

	}

	/*
	 * Register TinyMCE JavaScript
	 */
	function register_tinymce_js( $plugin_array ) {

		$plugin_array[$this->name] = plugins_url( 'js/tinymce.js' , __FILE__ );
		return $plugin_array;

	}

}