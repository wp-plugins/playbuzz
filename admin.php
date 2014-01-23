<?php
/*
 * Recommendations Widget
 * WordPress widget that displays playbuzz related playful content links and recommendations on sites sidebar.
 *
 * @since 0.1
 */
class PlayBuzzAdmin {

	protected $option_name = 'playbuzz';

	protected $data = array(

		// General
		'key'               => 'default',

		// Games
		'info'              => '1',
		'social'            => '1',
		'recommend'         => '1',

		// Recommendations
		'active'            => 'false',
		'show'              => 'footer',
		'view'              => 'large_images',
		'items'             => '3',
		'links'             => 'http://www.playbuzz.com',

		// Tags
		'tags-mix'          => '1',
		'tags-funz'         => '',
		'tags-popz'         => '',
		'tags-brainz'       => '',
		'tags-sportz'       => '',
		'tags-editors-pick' => '',
		'tags-tv'           => '',
		'tags-celebrities'  => '',
		'more-tags'         => '',

	);

	/*
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'init' ) );

		// Hooks fired when the Plugin is activated and deactivated
		register_activation_hook(   __FILE__ , array( $this, 'activate'   ) );
		register_deactivation_hook( __FILE__ , array( $this, 'deactivate' ) );

		// Text domain for localization and translation
		load_plugin_textdomain( 'playbuzz', false, plugin_dir_path( __FILE__ ) . '/lang' );

		// Admin sub-menu
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'add_page'   ) );

	}

	/*
	 * Fired when the plugin is activated.
	 */
	public function activate() {

		// Set default options when the plugin is activated
		update_option( $this->option_name, $this->data );

	}

	/*
	 * Fired when the plugin is deactivated.
	 */
	public function deactivate() {

		// Delete options from the database when the plugin is deactivated
		delete_option( $this->option_name );

	}

	/*
	 * Init function.
	 */
	public function init() {

		// Add Recommendations to the content
		add_filter( 'the_content', 'pb_content_filter', 20 );

		function pb_content_filter( $content ) {

			global $post;

			$options   = get_option( 'playbuzz' );
			$key       = $options['key'];
			$active    = $options['active'];
			$show      = $options['show'];
			$view      = $options['view'];
			$items     = $options['items'];
			$links     = $options['links'];
			$tags      = pb_tags( $options );

			// Embed Code
			$pb_code  = '
				<script type="text/javascript" src="http://www.playbuzz.com/bundles/widgets"></script>
				<div class="pb_recommendations" data-key="' . $key . '" data-view="' . $view . '" data-num-items="' . $items . '" data-links="' . $links . '" data-tags="' . $tags . '" data-nostyle="false"></div>
			';

			// Add embed code
			if ( 'true' == $active ) {

				// Add embed code only to posts
				if ( is_single() ) {

					// Add to header or footer
					if ( 'header' == $show ) {

						$content = $pb_code . $content;

					} elseif ( 'footer' == $show ) {

						$content = $content . $pb_code;

					}

				}

			}

			// Return the content
			return $content;

		}

	}

	/*
	 * White list our options using the Settings API.
	 */
	public function admin_init() {

		register_setting( 'playbuzz', $this->option_name );

	}

	/*
	 * Add entry in the settings menu.
	 */
	public function add_page() {

		add_options_page( __('PlayBuzz', 'playbuzz' ), __( 'PlayBuzz', 'playbuzz' ), 'manage_options', 'playbuzz', array( $this, 'options_do_page' ) );

	}

	/*
	 * Print the menu page itself.
	 */
	public function options_do_page() {

		// Set API Key
		if ( 'default' == $this->data['key']  ) {

			// Extract host domain
			$domain = parse_url( home_url(), PHP_URL_HOST );

			// Remove "www." from the domain
			$api = str_replace( 'www.', '', $domain );

			// Set API
			$this->data['key'] = $api;

		}

		// Load settings
		$options = wp_parse_args( get_option( $this->option_name ), $this->data );

		// Set default tab
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'details';

		// Display the page
		?>
		<a name="top"></a>
		<div class="wrap">
			<h2 class="nav-tab-wrapper">
				<?php _e( 'PlayBuzz', 'playbuzz' ); ?> 
				<a href="?page=<?php echo $this->option_name; ?>&tab=details"         class="nav-tab <?php echo $active_tab == 'details'         ? 'nav-tab-active' : ''; ?>"><?php _e( 'General Details',  'playbuzz' ); ?></a>
				<a href="?page=<?php echo $this->option_name; ?>&tab=games"           class="nav-tab <?php echo $active_tab == 'games'           ? 'nav-tab-active' : ''; ?>"><?php _e( 'Games',            'playbuzz' ); ?></a>
				<a href="?page=<?php echo $this->option_name; ?>&tab=recommendations" class="nav-tab <?php echo $active_tab == 'recommendations' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Recommendations',  'playbuzz' ); ?></a>
			</h2>

			<?php if( $active_tab == 'details' ) { ?>

				<h3><?php _e( 'General', 'playbuzz' ); ?></h3>

				<table class="form-table">
					<tr>
						<th scope="row"><?php _e( 'API Key', 'playbuzz' ); ?></th>
						<td>
							<strong><?php echo $options['key']; ?></strong>
						</td>
					</tr>
				</table>

				<h3><?php _e( 'Shortcodes', 'playbuzz' ); ?></h3>

				<p><?php _e( 'Embedding content is easy, just use a simple shortcode:', 'playbuzz' ); ?></p>
				<br>

				<h4><?php _e( 'Game / Post Shortcode', 'playbuzz' ); ?></h4>
				<p>
					<?php _e( 'Usage:', 'playbuzz' ); ?>
					<code>[playbuzz-game game="jonathang/players-and-playmates-playoffs"]</code>
				</p>
				<br>

				<h4><?php _e( 'Hub / Archive Shortcode', 'playbuzz' ); ?></h4>
				<p>
					<?php _e( 'Usage:', 'playbuzz' ); ?>
					<code>[playbuzz-hub tags="Celebrities"]</code>
				</p>
				<br>

				<h4><?php _e( 'Recommendations / Related-Content Shortcode', 'playbuzz' ); ?></h4>
				<p>
					<?php _e( 'Usage:', 'playbuzz' ); ?>
					<code>[playbuzz-recommendations tags="Celebrities" links="http://www.mysite.com/url_in_your_site_where_you_display_playbuzz_games"]</code>
				</p>
				<br>

			<?php } elseif( $active_tab == 'games' ) { ?>

				<h3><?php _e( 'Games Settings', 'playbuzz' ); ?></h3>

				<form method="post" action="options.php">

					<?php settings_fields( 'playbuzz' ); ?>

					<input type="hidden" name="<?php echo $this->option_name; ?>[key]"    value="<?php echo $options['key'];    ?>">

					<input type="hidden" name="<?php echo $this->option_name; ?>[active]" value="<?php echo $options['active']; ?>">
					<input type="hidden" name="<?php echo $this->option_name; ?>[show]"   value="<?php echo $options['show'];   ?>">
					<input type="hidden" name="<?php echo $this->option_name; ?>[view]"   value="<?php echo $options['view'];   ?>">
					<input type="hidden" name="<?php echo $this->option_name; ?>[items]"  value="<?php echo $options['items'];  ?>">
					<input type="hidden" name="<?php echo $this->option_name; ?>[links]"  value="<?php echo $options['links'];  ?>">

					<table class="form-table">
						<tr>
							<th scope="row"><?php _e( 'Info', 'playbuzz' ); ?></th>
							<td>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[info]" value="1" <?php if ( '1' == $options['info'] ) echo 'checked="checked"'; ?>> <?php _e( 'Show game info (thumbnail, name, description, editor, etc).', 'playbuzz' ); ?>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Social', 'playbuzz' ); ?></th>
							<td>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[social]" value="1" <?php if ( '1' == $options['social'] ) echo 'checked="checked"'; ?>> <?php _e( 'Show sharing buttons and comments control from the game page.', 'playbuzz' ); ?>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Recommendations', 'playbuzz' ); ?></th>
							<td>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[recommend]" value="1" <?php if ( '1' == $options['recommend'] ) echo 'checked="checked"'; ?>> <?php _e( 'Show recommendations for more games.', 'playbuzz' ); ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e( 'Simple Tags', 'playbuzz' ); ?></th>
							<td>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-mix]"          value="1" <?php if ( '1' == $options['tags-mix']          ) echo 'checked="checked"'; ?>> <?php _e( 'Mix',            'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-funz]"         value="1" <?php if ( '1' == $options['tags-funz']         ) echo 'checked="checked"'; ?>> <?php _e( 'Funz',           'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-popz]"         value="1" <?php if ( '1' == $options['tags-popz']         ) echo 'checked="checked"'; ?>> <?php _e( 'Popz',           'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-brainz]"       value="1" <?php if ( '1' == $options['tags-brainz']       ) echo 'checked="checked"'; ?>> <?php _e( 'Brainz',         'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-sportz]"       value="1" <?php if ( '1' == $options['tags-sportz']       ) echo 'checked="checked"'; ?>> <?php _e( 'Sportz',         'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-editors-pick]" value="1" <?php if ( '1' == $options['tags-editors-pick'] ) echo 'checked="checked"'; ?>> <?php _e( 'Editor\'s Pick', 'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-tv]"           value="1" <?php if ( '1' == $options['tags-tv']           ) echo 'checked="checked"'; ?>> <?php _e( 'TV',             'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-celebrities]"  value="1" <?php if ( '1' == $options['tags-celebrities']  ) echo 'checked="checked"'; ?>> <?php _e( 'Celebrities',    'playbuzz' ); ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e( 'Advanced Tags', 'playbuzz' ); ?></th>
							<td>
								<input type="text" name="<?php echo $this->option_name; ?>[more-tags]" value="<?php echo $options['more-tags']; ?>" class="regular-text" placeholder="<?php _e( 'Comma separated tags', 'playbuzz' ); ?>">
							</td>
						</tr>
					</table>

					<?php submit_button(); ?> 

				</form>

			<?php } elseif( $active_tab == 'recommendations' ) { ?>

				<h3><?php _e( 'Related Content', 'playbuzz' ); ?></h3>

				<form method="post" action="options.php">

					<?php settings_fields( 'playbuzz' ); ?>

					<input type="hidden" name="<?php echo $this->option_name; ?>[key]"       value="<?php echo $options['key'];       ?>">

					<input type="hidden" name="<?php echo $this->option_name; ?>[info]"      value="<?php echo $options['info'];      ?>">
					<input type="hidden" name="<?php echo $this->option_name; ?>[social]"    value="<?php echo $options['social'];    ?>">
					<input type="hidden" name="<?php echo $this->option_name; ?>[recommend]" value="<?php echo $options['recommend']; ?>">

					<table class="form-table">
						<tr>
							<th scope="row"><?php _e( 'Activation', 'playbuzz' ); ?></th>
							<td>
								<select name="<?php echo $this->option_name; ?>[active]">
									<option value="false" <?php if ( 'false' == $options['active'] ) echo 'selected'; ?>><?php _e( 'Deactivate', 'playbuzz' ); ?></option>
									<option value="true"  <?php if ( 'true'  == $options['active'] ) echo 'selected'; ?>><?php _e( 'Activate',   'playbuzz' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Location', 'playbuzz' ); ?></th>
							<td>
								<select name="<?php echo $this->option_name; ?>[show]">
									<option value="header" <?php if ( 'header' == $options['show'] ) echo 'selected'; ?>><?php _e( 'Above the content', 'playbuzz' ); ?></option>
									<option value="footer" <?php if ( 'footer' == $options['show'] ) echo 'selected'; ?>><?php _e( 'Bellow the content', 'playbuzz' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Number of Items', 'playbuzz' ); ?></th>
							<td>
								<select name="<?php echo $this->option_name; ?>[items]">
									<option value="2"  <?php if ( '2'  == $options['items'] ) echo 'selected'; ?>>2</option>
									<option value="3"  <?php if ( '3'  == $options['items'] ) echo 'selected'; ?>>3</option>
									<option value="4"  <?php if ( '4'  == $options['items'] ) echo 'selected'; ?>>4</option>
									<option value="5"  <?php if ( '5'  == $options['items'] ) echo 'selected'; ?>>5</option>
									<option value="6"  <?php if ( '6'  == $options['items'] ) echo 'selected'; ?>>6</option>
									<option value="7"  <?php if ( '7'  == $options['items'] ) echo 'selected'; ?>>7</option>
									<option value="8"  <?php if ( '8'  == $options['items'] ) echo 'selected'; ?>>8</option>
									<option value="9"  <?php if ( '9'  == $options['items'] ) echo 'selected'; ?>>9</option>
									<option value="10" <?php if ( '10' == $options['items'] ) echo 'selected'; ?>>10</option>
									<option value="11" <?php if ( '11' == $options['items'] ) echo 'selected'; ?>>11</option>
									<option value="12" <?php if ( '12' == $options['items'] ) echo 'selected'; ?>>12</option>
									<option value="13" <?php if ( '13' == $options['items'] ) echo 'selected'; ?>>13</option>
									<option value="14" <?php if ( '14' == $options['items'] ) echo 'selected'; ?>>14</option>
									<option value="15" <?php if ( '15' == $options['items'] ) echo 'selected'; ?>>15</option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Style', 'playbuzz' ); ?></th>
							<td valign="top">
								<input type="radio" name="<?php echo $this->option_name; ?>[view]" value="large_images"      <?php if ( 'large_images'      == $options['view'] ) echo 'checked="checked"'; ?>> <?php _e( 'Large Images',      'playbuzz' ); ?><br><img src="<?php echo plugins_url( 'img/tumbs.jpg',      __FILE__); ?>" title="<?php _e( 'Large Images', 'playbuzz' );      ?>"><br><br>
								<input type="radio" name="<?php echo $this->option_name; ?>[view]" value="horizontal_images" <?php if ( 'horizontal_images' == $options['view'] ) echo 'checked="checked"'; ?>> <?php _e( 'Horizontal Images', 'playbuzz' ); ?><br><img src="<?php echo plugins_url( 'img/image-list.jpg', __FILE__); ?>" title="<?php _e( 'Horizontal Images', 'playbuzz' ); ?>"><br><br>
								<input type="radio" name="<?php echo $this->option_name; ?>[view]" value="no_images"         <?php if ( 'no_images'         == $options['view'] ) echo 'checked="checked"'; ?>> <?php _e( 'No Images',         'playbuzz' ); ?><br><img src="<?php echo plugins_url( 'img/list.jpg',       __FILE__); ?>" title="<?php _e( 'No Images', 'playbuzz' );         ?>">
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e( 'Simple Tags', 'playbuzz' ); ?></th>
							<td>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-mix]"          value="1" <?php if ( '1' == $options['tags-mix']          ) echo 'checked="checked"'; ?>> <?php _e( 'Mix',            'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-funz]"         value="1" <?php if ( '1' == $options['tags-funz']         ) echo 'checked="checked"'; ?>> <?php _e( 'Funz',           'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-popz]"         value="1" <?php if ( '1' == $options['tags-popz']         ) echo 'checked="checked"'; ?>> <?php _e( 'Popz',           'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-brainz]"       value="1" <?php if ( '1' == $options['tags-brainz']       ) echo 'checked="checked"'; ?>> <?php _e( 'Brainz',         'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-sportz]"       value="1" <?php if ( '1' == $options['tags-sportz']       ) echo 'checked="checked"'; ?>> <?php _e( 'Sportz',         'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-editors-pick]" value="1" <?php if ( '1' == $options['tags-editors-pick'] ) echo 'checked="checked"'; ?>> <?php _e( 'Editor\'s Pick', 'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-tv]"           value="1" <?php if ( '1' == $options['tags-tv']           ) echo 'checked="checked"'; ?>> <?php _e( 'TV',             'playbuzz' ); ?>
								<input type="checkbox" name="<?php echo $this->option_name; ?>[tags-celebrities]"  value="1" <?php if ( '1' == $options['tags-celebrities']  ) echo 'checked="checked"'; ?>> <?php _e( 'Celebrities',    'playbuzz' ); ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e( 'Advanced Tags', 'playbuzz' ); ?></th>
							<td>
								<input type="text" name="<?php echo $this->option_name; ?>[more-tags]" value="<?php echo $options['more-tags']; ?>" class="regular-text" placeholder="<?php _e( 'Comma separated tags', 'playbuzz' ); ?>">
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e( 'Open links on', 'playbuzz' ); ?></th>
							<td>
								<input type="text" name="<?php echo $this->option_name; ?>[links]" value="<?php echo $options['links']; ?>" class="regular-text">
								<p class="description"><?php _e( 'URL where you displayed playbuzz posts/games. Default: http://www.playbuzz.com', 'playbuzz' ); ?></p>
							</td>
						</tr>
					</table>

					<?php submit_button(); ?> 

				</form>

			<?php } ?>

		</div>
		<?php

	}

}


/*
 * Extract tags list
 *
 * @since 0.1
 */
function pb_tags( $options ) {

	// Tags string
	$tags = '';

	// Default tags
	if ( '1' == $options['tags-mix']          ) $tags .= 'All,';
	if ( '1' == $options['tags-funz']         ) $tags .= 'Funz,';
	if ( '1' == $options['tags-popz']         ) $tags .= 'Popz,';
	if ( '1' == $options['tags-brainz']       ) $tags .= 'Brainz,';
	if ( '1' == $options['tags-sportz']       ) $tags .= 'sportz,';
	if ( '1' == $options['tags-editors-pick'] ) $tags .= 'Editor\'s Pick,';
	if ( '1' == $options['tags-tv']           ) $tags .= 'TV,';
	if ( '1' == $options['tags-celebrities']  ) $tags .= 'Celebrities,';

	// Custom tags
	$tags .= $options['more-tags'];

	// Remove the comma from the end
	$tags = rtrim( $tags, ',');

	// Return the tag list
	return $tags;

}
