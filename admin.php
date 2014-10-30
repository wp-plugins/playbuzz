<?php
/*
 * PlayBuzz Admin
 * Displays playbuzz menus in WordPress dashboard.
 *
 * @since 0.1.0
 */
class PlayBuzzAdmin {

	protected static $option_name = 'playbuzz';
	protected static $data = array(

		// General
		'key'               => 'default',

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
		'links'             => 'http://www.playbuzz.com',
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

	/*
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'init' ) );

		// Text domain for localization and translation
		try {
			load_plugin_textdomain( 'playbuzz', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
		} catch (Exception $e) {
			// Nothing
		}

		// Admin sub-menu
		if ( is_admin() ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'add_page'   ) );
		}

	}

	/*
	 * Get option name
	 */
	public function get_option_name() {
		return self::$option_name;
	}

	/*
	 * Get data
	 */
	public function get_data() {
		return self::$data;
	}

	/*
	 * Fired when the plugin is activated.
	 */
	public static function activate() {

		// Set default options when the plugin is activated
		if ( ! current_user_can( 'activate_plugins' ) )
		return;
		
		// Set API Key
		if ( 'default' == self::$data['key']  ) {

			// Extract host domain
			$domain = parse_url( home_url(), PHP_URL_HOST );

			// Remove "www." from the domain
			$api = str_replace( 'www.', '', $domain );

			// Set API
			self::$data['key'] = $api;
		}
		
		if( !get_option( self::$option_name ) ) {
			update_option( self::$option_name, self::$data );
		}
		else // We already have a settings object in memory, add the new items
		{
			update_option( self::$option_name, array_merge( (self::$data), get_option( self::$option_name )) );
		}
	}

	/*
	 * Fired when the plugin is deactivated.
	 */
	public static function deactivate() {

		// Delete options from the database when the plugin is deactivated
		//delete_option( self::$option_name );

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
				<script type="text/javascript" src="//cdn.playbuzz.com/widget/widget.js"></script>
				<div class="pb_recommendations" data-key="' . $key . '" data-view="' . $view . '" data-num-items="' . $items . '" data-links="' . $links . '" data-tags="' . $tags . '" data-nostyle="false"></div>
			';

			// Add embed code
			if ( 'true' == $active ) {

				// Add embed code only to posts and pages
				if ( is_singular() ) {

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

		register_setting( 'playbuzz', $this->get_option_name() );

		wp_register_style( 'playbuzz-admin', plugins_url( 'css/admin.css', __FILE__ ), false, '0.3' );
		wp_enqueue_style(  'playbuzz-admin' );

	}

	/*
	 * Add entry in the settings menu.
	 */
	public function add_page() {

		add_options_page( __( 'PlayBuzz', 'playbuzz' ), __( 'PlayBuzz', 'playbuzz' ), 'manage_options', 'playbuzz', array( $this, 'options_do_page' ) );

	}

	/*
	 * Print the menu page itself.
	 */
	public function options_do_page() {

		// Load settings
		$options = get_option( $this->get_option_name() );

		// Set default tab
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'start';

		// Display the page
		?>
		<a name="top"></a>
		<div class="wrap">
			<h2 class="nav-tab-wrapper">
				<a href="?page=<?php echo $this->get_option_name(); ?>&tab=start" class="nav-tab <?php echo $active_tab == 'start' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Getting Started', 'playbuzz' ); ?></a>
				<a href="?page=<?php echo $this->get_option_name(); ?>&tab=embed" class="nav-tab <?php echo $active_tab == 'embed' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Embed Options',   'playbuzz' ); ?></a>
				<a href="?page=<?php echo $this->get_option_name(); ?>&tab=help"  class="nav-tab <?php echo $active_tab == 'help'  ? 'nav-tab-active' : ''; ?>"><?php _e( 'Help',            'playbuzz' ); ?></a>
			</h2>

			<?php if( $active_tab == 'start' ) { ?>

				<h1><?php _e( 'PlayBuzz Plugin', 'playbuzz' ); ?></h1>

				<div class="playbuzz_getting_started">
					<img src="<?php echo plugins_url( 'img/blog-item.png', __FILE__ ); ?>" class="location_img">
					<h3><?php _e( 'Embeding an Item', 'playbuzz' ); ?></h3>
					<ol class="circles-list">
						<li>
							<p><?php printf( __( '<strong>Choose your content</strong>: Go to %s to choose the item you want to embed in your post.', 'playbuzz' ), '<a href="http://playbuzz.com/" target="_blank">PlayBuzz.com</a>' ); ?></p>
						</li>
						<li>
							<p><?php _e( 'Use the following <strong>shortcode</strong> where you want to embed the item:', 'playbuzz' ); ?></p>
							<p><code> [playbuzz-item]</code></p>
							<p><?php _e( 'For example:', 'playbuzz' ); ?></p>
							<p><code> [playbuzz-item url="http://www.playbuzz.com/llamap10/how-weird-are-you"]</code></p>
						</li>
						<li>
							<p><?php printf( __( '<a href="%s">Tweak the item\'s settings</a> or read about more <a href="%s">advanced shortcode attributes</a>.', 'playbuzz' ), ('?page='.$this->get_option_name().'&tab=embed'), ('?page='.$this->get_option_name().'&tab=help') ); ?></p>
						</li>
					</ol>
				</div>

				<div class="playbuzz_getting_started">
					<img src="<?php echo plugins_url( 'img/blog-section.png', __FILE__ ); ?>" class="location_img">
					<h3><?php _e( 'Embeding a Section', 'playbuzz' ); ?></h3>
					<ol class="circles-list">
						<li>
							<p><?php printf( __( '<strong>Choose your list of playful content items</strong>: Go to %s to choose the tag you want to embed in your page.', 'playbuzz' ), '<a href="http://playbuzz.com/" target="_blank">PlayBuzz.com</a>' ); ?></p>
						</li>
						<li>
							<p><?php _e( 'Use the following <strong>shortcode</strong> where you want to embed the Playful Items list:', 'playbuzz' ); ?></p>
							<p><code> [playbuzz-section]</code></p>
							<p><?php _e( 'For example:', 'playbuzz' ); ?></p>
							<p><code> [playbuzz-section tags="funny, cats"]</code></p>
						</li>
						<li>
							<p><?php printf( __( 'Customize by using <a href="%s">advanced shortcode attributes</a>.', 'playbuzz' ), ('?page='.$this->get_option_name().'&tab=help') ); ?></p>
						</li>
					</ol>
				</div>

				<!--
				<table class="form-table">
					<tr>
						<th scope="row"><?php _e( 'API Key', 'playbuzz' ); ?></th>
						<td>
							<strong><?php echo $options['key']; ?></strong>
						</td>
					</tr>
				</table>
				-->

			<?php } elseif( $active_tab == 'embed' ) { ?>

				<h1><?php _e( 'PlayBuzz Plugin', 'playbuzz' ); ?></h1>

				<form method="post" action="options.php">

					<?php settings_fields( 'playbuzz' ); ?>

					<div class="playbuzz_embed">

						<h3><?php _e( 'Item Embed Options', 'playbuzz' ); ?></h3>

						<table class="form-table">
							<tr>
								<th scope="row"><?php _e( 'Item Info', 'playbuzz' ); ?></th>
								<td>
									<input type="checkbox" name="<?php echo $this->get_option_name(); ?>[info]" value="1" <?php if ( isset( $options['info'] ) && ( '1' == $options['info'] ) ) echo 'checked="checked"'; ?>> <?php _e( 'Show item info (thumbnail, title, description, etc.)', 'playbuzz' ); ?>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Sharing', 'playbuzz' ); ?></th>
								<td>
									<input type="checkbox" name="<?php echo $this->get_option_name(); ?>[shares]" value="1" <?php if ( isset( $options['shares'] ) && ( '1' == $options['shares'] ) ) echo 'checked="checked"'; ?>> <?php _e( 'Show sharing buttons (recommended - redirects to your page).', 'playbuzz' ); ?>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Comments', 'playbuzz' ); ?></th>
								<td>
									<input type="checkbox" name="<?php echo $this->get_option_name(); ?>[comments]" value="1" <?php if ( isset( $options['comments'] ) && ( '1' == $options['comments'] ) ) echo 'checked="checked"'; ?>> <?php _e( 'Enable facebook comments.', 'playbuzz' ); ?>
								</td>
							</tr>
							<tr class="separator">
								<td colspan="2">
									<hr>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Recommendations', 'playbuzz' ); ?></th>
								<td>
									<input type="checkbox" name="<?php echo $this->get_option_name(); ?>[recommend]" value="1" <?php if ( isset( $options['comments'] ) && ( '1' == $options['recommend'] ) ) echo 'checked="checked"'; ?>> <?php _e( 'Show recommendations for more items.', 'playbuzz' ); ?>
									<table class="form-table">
										<tr valign="top">
											<th scope="row"><?php _e( 'Tags', 'playbuzz' ); ?></th>
											<td>
												<input type="checkbox" class="checkbox"        name="<?php echo $this->get_option_name(); ?>[tags-mix]"          value="1" <?php if ( isset( $options['tags-mix']          ) && ( '1' == $options['tags-mix']          ) ) echo 'checked="checked"'; ?>> <?php _e( 'All',            'playbuzz' ); ?><br/>
												<input type="checkbox" class="checkbox_indent" name="<?php echo $this->get_option_name(); ?>[tags-fun]"          value="1" <?php if ( isset( $options['tags-fun']          ) && ( '1' == $options['tags-fun']          ) ) echo 'checked="checked"'; ?>> <?php _e( 'Fun',            'playbuzz' ); ?><br/>
												<input type="checkbox" class="checkbox_indent" name="<?php echo $this->get_option_name(); ?>[tags-pop]"          value="1" <?php if ( isset( $options['tags-pop']          ) && ( '1' == $options['tags-pop']          ) ) echo 'checked="checked"'; ?>> <?php _e( 'Pop',            'playbuzz' ); ?><br/>
												<input type="checkbox" class="checkbox_indent" name="<?php echo $this->get_option_name(); ?>[tags-geek]"         value="1" <?php if ( isset( $options['tags-geek']         ) && ( '1' == $options['tags-geek']         ) ) echo 'checked="checked"'; ?>> <?php _e( 'Geek',           'playbuzz' ); ?><br/>
												<input type="checkbox" class="checkbox_indent" name="<?php echo $this->get_option_name(); ?>[tags-sports]"       value="1" <?php if ( isset( $options['tags-sports']       ) && ( '1' == $options['tags-sports']       ) ) echo 'checked="checked"'; ?>> <?php _e( 'Sports',         'playbuzz' ); ?><br/>
												<input type="checkbox" class="checkbox_indent" name="<?php echo $this->get_option_name(); ?>[tags-editors-pick]" value="1" <?php if ( isset( $options['tags-editors-pick'] ) && ( '1' == $options['tags-editors-pick'] ) ) echo 'checked="checked"'; ?>> <?php _e( 'Editor\'s Pick', 'playbuzz' ); ?><br/>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row"><?php _e( 'Custom Tags', 'playbuzz' ); ?></th>
											<td>
												<input type="text" name="<?php echo $this->get_option_name(); ?>[more-tags]" value="<?php echo $options['more-tags']; ?>" class="regular-text" placeholder="<?php _e( 'Comma separated tags', 'playbuzz' ); ?>">
												<p><?php _e( 'Example: food, rap, weather', 'playbuzz' ); ?></p>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr class="separator">
								<td colspan="2">
									<hr>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e( 'Margin Top', 'playbuzz' ); ?></th>
								<td>
									<input type="text" name="<?php echo $this->get_option_name(); ?>[margin-top]" value="<?php echo $options['margin-top']; ?>" class="regular-text" placeholder="<?php _e( 'Default: 0px', 'playbuzz' ); ?>"><br>
									<p><?php _e( 'Use in case of a floating bar.', 'playbuzz' ); ?></p>
								</td>
							</tr>
							<tr class="separator">
								<td colspan="2">
									<hr>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'WordPress Theme Visibility', 'playbuzz' ); ?></th>
								<td>
									<select name="<?php echo $this->get_option_name(); ?>[embeddedon]">
										<option value="content" <?php if ( isset( $options['embeddedon'] ) && ( 'content' == $options['embeddedon'] ) ) echo 'selected'; ?>><?php _e( 'Show embedded content in posts/pages only',                    'playbuzz' ); ?></option>
										<option value="all"     <?php if ( isset( $options['embeddedon'] ) && ( 'all'     == $options['embeddedon'] ) ) echo 'selected'; ?>><?php _e( 'Show embedded content in all pages (singular, archive, ect.)', 'playbuzz' ); ?></option>
									</select>
									<p><?php printf( __( 'Whether to show the embedded content only in <a href="%s" target="_blank">singular pages</a>, or <a href="%s" target="_blank">archive page</a> too.', 'playbuzz' ), 'http://codex.wordpress.org/Function_Reference/is_singular', 'http://codex.wordpress.org/Template_Hierarchy' ); ?></p>
								</td>
							</tr>
						</table>

						<?php submit_button(); ?> 

					</div>

					<div class="playbuzz_embed" style="display:none;"> <?php /* Hidden from the user for the next year for backwards compatibility reasons, and then remove the "Related Content Embed" feature from the plugin. */ ?>

						<h3><?php _e( 'Related Content Embed Options', 'playbuzz' ); ?></h3>

						<table class="form-table">
							<tr>
								<th scope="row"><?php _e( 'Number of Items', 'playbuzz' ); ?></th>
								<td>
									<select name="<?php echo $this->get_option_name(); ?>[items]">
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
								<th scope="row"><?php _e( 'Items layout', 'playbuzz' ); ?></th>
								<td valign="top">
									<input type="radio" name="<?php echo $this->get_option_name(); ?>[view]" value="large_images"      <?php if ( 'large_images'      == $options['view'] ) echo 'checked="checked"'; ?>> <?php _e( 'Large Images',      'playbuzz' ); ?><br><img src="<?php echo plugins_url( 'img/tumbs.jpg',      __FILE__); ?>" title="<?php _e( 'Large Images',      'playbuzz' ); ?>"><br><br>
									<input type="radio" name="<?php echo $this->get_option_name(); ?>[view]" value="horizontal_images" <?php if ( 'horizontal_images' == $options['view'] ) echo 'checked="checked"'; ?>> <?php _e( 'Horizontal Images', 'playbuzz' ); ?><br><img src="<?php echo plugins_url( 'img/image-list.jpg', __FILE__); ?>" title="<?php _e( 'Horizontal Images', 'playbuzz' ); ?>"><br><br>
									<input type="radio" name="<?php echo $this->get_option_name(); ?>[view]" value="no_images"         <?php if ( 'no_images'         == $options['view'] ) echo 'checked="checked"'; ?>> <?php _e( 'No Images',         'playbuzz' ); ?><br><img src="<?php echo plugins_url( 'img/list.jpg',       __FILE__); ?>" title="<?php _e( 'No Images',         'playbuzz' ); ?>">
								</td>
							</tr>
							<?php /*
							<tr valign="top">
								<th scope="row"><?php _e( 'Tags', 'playbuzz' ); ?></th>
								<td>
									<input type="checkbox" class="checkbox"        name="<?php echo $this->get_option_name(); ?>[tags-mix]"          value="1" <?php if ( '1' == $options['tags-mix']          ) echo 'checked="checked"'; ?>> <?php _e( 'All',            'playbuzz' ); ?><br/>
									<input type="checkbox" class="checkbox_indent" name="<?php echo $this->get_option_name(); ?>[tags-fun]"          value="1" <?php if ( '1' == $options['tags-fun']          ) echo 'checked="checked"'; ?>> <?php _e( 'Fun',            'playbuzz' ); ?><br/>
									<input type="checkbox" class="checkbox_indent" name="<?php echo $this->get_option_name(); ?>[tags-pop]"          value="1" <?php if ( '1' == $options['tags-pop']          ) echo 'checked="checked"'; ?>> <?php _e( 'Pop',            'playbuzz' ); ?><br/>
									<input type="checkbox" class="checkbox_indent" name="<?php echo $this->get_option_name(); ?>[tags-geek]"         value="1" <?php if ( '1' == $options['tags-geek']         ) echo 'checked="checked"'; ?>> <?php _e( 'Geek',           'playbuzz' ); ?><br/>
									<input type="checkbox" class="checkbox_indent" name="<?php echo $this->get_option_name(); ?>[tags-sports]"       value="1" <?php if ( '1' == $options['tags-sports']       ) echo 'checked="checked"'; ?>> <?php _e( 'Sports',         'playbuzz' ); ?><br/>
									<input type="checkbox" class="checkbox_indent" name="<?php echo $this->get_option_name(); ?>[tags-editors-pick]" value="1" <?php if ( '1' == $options['tags-editors-pick'] ) echo 'checked="checked"'; ?>> <?php _e( 'Editor\'s Pick', 'playbuzz' ); ?><br/>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e( 'Custom Tags', 'playbuzz' ); ?></th>
								<td>
									<input type="text" name="<?php echo $this->get_option_name(); ?>[more-tags]" value="<?php echo $options['more-tags']; ?>" class="regular-text" placeholder="<?php _e( 'Comma separated tags', 'playbuzz' ); ?>">
								</td>
							</tr>
							*/ ?>
							<tr>
								<th scope="row"><?php _e( 'Open Items at', 'playbuzz' ); ?></th>
								<td>
									<p><?php printf( __( '<a href="%s" target="_blank">Create</a> a new page containing the <code>[playbuzz-section]</code> shortcode. Then select it below as the destination page where items will open:', 'playbuzz' ), 'post-new.php?post_type=page' ); ?></p>
									<?php
									if ( isset( $options['section-page'] ) ) {
										$link_page_id = $options['section-page'];
									} else {
										$link_page_id = 0;
									}
									wp_dropdown_pages( array( 'selected' => $link_page_id, 'post_type' => 'page', 'hierarchical' => 1, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '0' ) );
									?>
									<input type="hidden" name="<?php echo $this->get_option_name(); ?>[links]" value="<?php echo $options['links']; ?>" class="regular-text" placeholder="http://www.playbuzz.com/">
								</td>
							</tr>
							<tr class="separator">
								<td colspan="2">
									<hr>
								</td>
							</tr>
							<tr>
								<th colspan="2"><?php _e( 'Automatically Add Recommendations', 'playbuzz' ); ?></th>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Active', 'playbuzz' ); ?></th>
								<td>
									<select name="<?php echo $this->get_option_name(); ?>[active]">
										<option value="false" <?php if ( 'false' == $options['active'] ) echo 'selected'; ?>><?php _e( 'Disable', 'playbuzz' ); ?></option>
										<option value="true"  <?php if ( 'true'  == $options['active'] ) echo 'selected'; ?>><?php _e( 'Enable',  'playbuzz' ); ?></option>
									</select>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Location', 'playbuzz' ); ?></th>
								<td>
									<select name="<?php echo $this->get_option_name(); ?>[show]">
										<option value="header" <?php if ( 'header' == $options['show'] ) echo 'selected'; ?>><?php _e( 'Above the content',  'playbuzz' ); ?></option>
										<option value="footer" <?php if ( 'footer' == $options['show'] ) echo 'selected'; ?>><?php _e( 'Bellow the content', 'playbuzz' ); ?></option>
									</select>
								</td>
							</tr>
						</table>

						<?php submit_button(); ?> 

					</div>

				</form>

			<?php } elseif( $active_tab == 'help' ) { ?>

				<h1><?php _e( 'PlayBuzz Plugin', 'playbuzz' ); ?></h1>

				<div class="playbuzz_help">

					<h3><?php _e( 'Item Shortcode', 'playbuzz' ); ?></h3>
					<p><?php printf( __( 'Choose any Playful Content item from %s and easily embed it in a post.', 'playbuzz' ), '<a href="http://playbuzz.com/" target="_blank">PlayBuzz.com</a>' ); ?></p>
					<p><?php _e( 'Use the following shortcode where you want to embed the item:', 'playbuzz' ); ?></p>
					<p><code>[playbuzz-item]</code>
					<p><?php _e( 'For example:', 'playbuzz' ); ?></p>
					<p><code>[playbuzz-item url="http://www.playbuzz.com/rachaelg/eye-candy-name-the-chocolate-by-its-cross-section"]</code>
					<p><?php printf( __( 'You can tweak the general settings for all embedded  content in the <a href="%s">Embed Options</a> tab.', 'playbuzz' ), ('?page='.$this->get_option_name().'&tab=embed') ); ?></p>
					<p><?php _e( 'Or you can override and customize each embedded content with the following shortcode attributes:', 'playbuzz' ); ?></p>
					<dl>
						<dt>url</dt>
						<dd>
							<p><?php _e( 'The URL of the item that will be displayed.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: URL', 'playbuzz' ); ?></p>
						</dd>
						<dt>info</dt>
						<dd>
							<p><?php _e( 'Show item info (thumbnail, name, description, editor, etc).', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: Boolean (true/false) ; Default: true', 'playbuzz' ); ?></p>
						</dd>
						<dt>shares</dt>
						<dd>
							<p><?php _e( 'Show sharing buttons.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: Boolean (true/false) ; Default: true', 'playbuzz' ); ?></p>
						</dd>
						<dt>comments</dt>
						<dd>
							<p><?php _e( 'Show comments control from the item page.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: Boolean (true/false) ; Default: true', 'playbuzz' ); ?></p>
						</dd>
						<dt>recommend</dt>
						<dd>
							<p><?php _e( 'Show recommendations for more items.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: Boolean (true/false) ; Default: true', 'playbuzz' ); ?></p>
						</dd>
						<dt>width</dt>
						<dd>
							<p><?php _e( 'Define custom width in pixels.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: String ; Default: auto', 'playbuzz' ); ?></p>
						</dd>
						<dt>height</dt>
						<dd>
							<p><?php _e( 'Define custom height in pixels.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: String ; Default: auto', 'playbuzz' ); ?></p>
						</dd>
						<dt>margin-top</dt>
						<dd>
							<p><?php _e( 'Define custom margin-top in pixels.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: String ; Default: 0px', 'playbuzz' ); ?></p>
						</dd>
					</dl>

				</div>
				<div class="playbuzz_help">

					<h3><?php _e( 'Section Shortcode', 'playbuzz' ); ?></h3>
					<p><?php printf( __( 'Choose any list of Playful Items in a specific vertical from %s and easily embed it in a post. This is best used as a "Playful Section" displaying items in the selected tags (topics).', 'playbuzz' ), '<a href="http://playbuzz.com/" target="_blank">PlayBuzz.com</a>' ); ?></p>
					<p><?php _e( 'Use the following shortcode where you want to embed the item:', 'playbuzz' ); ?></p>
					<p><code>[playbuzz-section]</code>
					<p><?php _e( 'For example:', 'playbuzz' ); ?></p>
					<p><code>[playbuzz-section tags="All"]</code>
					<p><?php _e( 'You can tweak the general settings for the section with the following shortcode attributes:', 'playbuzz' ); ?></p>
					<dl>
						<dt>tags</dt>
						<dd>
							<p><?php _e( 'Filters the content shown by comma separated tags.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: String ; Default: All', 'playbuzz' ); ?></p>
						</dd>
						<dt>shares</dt>
						<dd>
							<p><?php _e( 'Show sharing buttons.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: Boolean (true/false) ; Default: true', 'playbuzz' ); ?></p>
						</dd>
						<dt>comments</dt>
						<dd>
							<p><?php _e( 'Show comments control from the item page.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: Boolean (true/false) ; Default: true', 'playbuzz' ); ?></p>
						</dd>
						<dt>recommend</dt>
						<dd>
							<p><?php _e( 'Show recommendations for more items.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: Boolean (true/false) ; Default: true', 'playbuzz' ); ?></p>
						</dd>
						<dt>width</dt>
						<dd>
							<p><?php _e( 'Define custom width in pixels.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: String ; Default: auto', 'playbuzz' ); ?></p>
						</dd>
						<dt>height</dt>
						<dd>
							<p><?php _e( 'Define custom height in pixels.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: String ; Default: auto', 'playbuzz' ); ?></p>
						</dd>
						<dt>margin-top</dt>
						<dd>
							<p><?php _e( 'Define custom margin-top in pixels.', 'playbuzz' ); ?></p>
							<p><?php _e( 'Type: String ; Default: 0px', 'playbuzz' ); ?></p>
						</dd>
					</dl>

				</div>

			<?php } ?>

		</div>
		<?php

	}

}



/*
 * Extract tags list
 *
 * @since 0.1.0
 */
function pb_tags( $options ) {

	// Tags string
	$tags = '';

	// Default tags
	if ( '1' == $options['tags-mix']          ) $tags .= 'All,';
	if ( '1' == $options['tags-fun']          ) $tags .= 'Fun,';
	if ( '1' == $options['tags-pop']          ) $tags .= 'Pop,';
	if ( '1' == $options['tags-geek']         ) $tags .= 'Geek,';
	if ( '1' == $options['tags-sports']       ) $tags .= 'Sports,';
	if ( '1' == $options['tags-editors-pick'] ) $tags .= 'EditorsPick_Featured,';

	// Custom tags
	$tags .= $options['more-tags'];

	// Remove the comma from the end
	$tags = rtrim( $tags, ',');

	// Return the tag list
	return $tags;

}
