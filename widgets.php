<?php
/*
 * Recommendations Widget
 * WordPress widget that displays playbuzz related playful content links and recommendations on sites sidebar.
 *
 * @since 0.1.0
 */
class Playbuzz_Recommendations_Widget extends WP_Widget {

	/*
	 * Constructor
	 */
	public function __construct() {

		// load plugin text domain
		add_action( 'init', array( $this, 'playbuzz' ) );

		// Hooks fired when the Widget is activated and deactivated
		register_activation_hook(   __FILE__ , array( $this, 'activate'   ) );
		register_deactivation_hook( __FILE__ , array( $this, 'deactivate' ) );

		parent::__construct(
			'playbuzz-recommendations-id',
			__( 'Playbuzz Recommendations', 'playbuzz' ),
			array(
				'classname'   => 'playbuzz-recommendations',
				'description' => __( 'Related Playful Content links and recommendations by playbuzz.', 'playbuzz' )
			)
		);

	}


	/*
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$options = get_option( 'playbuzz' );

		// set values
		$title = empty( $instance['title'] ) ? '' : apply_filters( 'title', $instance['title'] );
		$key   = $options['key'];
		$tags  = pb_tags( $instance );
		$view  = empty( $instance['view']  ) ? '' : apply_filters( 'view',  $instance['view']  );
		$items = empty( $instance['items'] ) ? '' : apply_filters( 'items', $instance['items'] );
		$links = empty( $instance['links'] ) ? '' : apply_filters( 'links', $instance['links'] );

		// Output
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		echo '
			<script type="text/javascript" src="//cdn.playbuzz.com/widget/widget.js"></script>
			<div class="pb_recommendations" data-key="' . $key . '" data-tags="' . $tags . '" data-view="' . $view . '" data-num-items="' . $items . '" data-links="' . $links . '" data-nostyle="false"></div>
		';
		echo $after_widget;

	}


	/*
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']             = strip_tags( $new_instance['title']                             );
		$instance['view']              = strip_tags( stripslashes( $new_instance['view']              ) );
		$instance['items']             = strip_tags( stripslashes( $new_instance['items']             ) );
		$instance['tags-mix']          = strip_tags( stripslashes( $new_instance['tags-mix']          ) );
		$instance['tags-fun']          = strip_tags( stripslashes( $new_instance['tags-fun']          ) );
		$instance['tags-pop']          = strip_tags( stripslashes( $new_instance['tags-pop']          ) );
		$instance['tags-geek']         = strip_tags( stripslashes( $new_instance['tags-geek']         ) );
		$instance['tags-sports']       = strip_tags( stripslashes( $new_instance['tags-sports']       ) );
		$instance['tags-editors-pick'] = strip_tags( stripslashes( $new_instance['tags-editors-pick'] ) );
		$instance['more-tags']         = strip_tags( stripslashes( $new_instance['more-tags']         ) );
		$instance['section-page']      = $new_instance['section-page'];

		// for backwards compatibility
		if ( empty( $instance['section-page'] ) OR ( 0 == $instance['section-page'] ) ) {
			$instance['links']         = strip_tags( $new_instance['links'] );
		} else {
			$instance['links']         = get_permalink( $new_instance['section-page'] );
		}

		return $instance;

	}


	/*
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		// Load options
		$options = get_option( 'playbuzz' );

		// Set default values
		$defaults = array(
				'title'				=> __( 'Play It', 'playbuzz' ),
				'view'              => ( isset( $options['view']              ) ? $options['view']              : 'large_images' ),
				'items'             => ( isset( $options['items']             ) ? $options['items']             : '3' ),
				'tags-mix'          => ( isset( $options['tags-mix']          ) ? $options['tags-mix']          : '1' ),
				'tags-fun'          => ( isset( $options['tags-fun']          ) ? $options['tags-fun']          : ''  ),
				'tags-pop'          => ( isset( $options['tags-pop']          ) ? $options['tags-pop']          : ''  ),
				'tags-geek'         => ( isset( $options['tags-geek']         ) ? $options['tags-geek']         : ''  ),
				'tags-sports'       => ( isset( $options['tags-sports']       ) ? $options['tags-sports']       : ''  ),
				'tags-editors-pick' => ( isset( $options['tags-editors-pick'] ) ? $options['tags-editors-pick'] : ''  ),
				'more-tags'         => ( isset( $options['more-tags']         ) ? $options['more-tags']         : ''  ),
				'links'             => ( isset( $options['links']             ) ? $options['links']             : ''  ),
				'section-page'      => ( isset( $options['section-page']      ) ? $options['section-page']      : ''  ),
			);

		// New instance (use defaults if empty)
		$new_instance = wp_parse_args( (array)$instance, $defaults );

		// Display the admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title', 'playbuzz' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $new_instance['title']; ?>" placeholder="<?php _e( 'Widget title', 'playbuzz' ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('view'); ?>"><?php _e( 'Items layout', 'playbuzz' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('view'); ?>" name="<?php echo $this->get_field_name('view'); ?>">
				<option value="large_images"      <?php if ( 'large_images'      == $new_instance['view'] ) echo 'selected'; ?>><?php _e( 'Large Images',      'playbuzz' ); ?></option>
				<option value="horizontal_images" <?php if ( 'horizontal_images' == $new_instance['view'] ) echo 'selected'; ?>><?php _e( 'Horizontal Images', 'playbuzz' ); ?></option>
				<option value="no_images"         <?php if ( 'no_images'         == $new_instance['view'] ) echo 'selected'; ?>><?php _e( 'No Images',         'playbuzz' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('items'); ?>"><?php _e( 'Number of Items', 'playbuzz' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>">
				<option value="2"  <?php if ( '2'  == $new_instance['items'] ) echo 'selected'; ?>>2</option>
				<option value="3"  <?php if ( '3'  == $new_instance['items'] ) echo 'selected'; ?>>3</option>
				<option value="4"  <?php if ( '4'  == $new_instance['items'] ) echo 'selected'; ?>>4</option>
				<option value="5"  <?php if ( '5'  == $new_instance['items'] ) echo 'selected'; ?>>5</option>
				<option value="6"  <?php if ( '6'  == $new_instance['items'] ) echo 'selected'; ?>>6</option>
				<option value="7"  <?php if ( '7'  == $new_instance['items'] ) echo 'selected'; ?>>7</option>
				<option value="8"  <?php if ( '8'  == $new_instance['items'] ) echo 'selected'; ?>>8</option>
				<option value="9"  <?php if ( '9'  == $new_instance['items'] ) echo 'selected'; ?>>9</option>
				<option value="10" <?php if ( '10' == $new_instance['items'] ) echo 'selected'; ?>>10</option>
				<option value="11" <?php if ( '11' == $new_instance['items'] ) echo 'selected'; ?>>11</option>
				<option value="12" <?php if ( '12' == $new_instance['items'] ) echo 'selected'; ?>>12</option>
				<option value="13" <?php if ( '13' == $new_instance['items'] ) echo 'selected'; ?>>13</option>
				<option value="14" <?php if ( '14' == $new_instance['items'] ) echo 'selected'; ?>>14</option>
				<option value="15" <?php if ( '15' == $new_instance['items'] ) echo 'selected'; ?>>15</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e( 'Tags', 'playbuzz' ); ?></label><br>
			<input type="checkbox" name="<?php echo $this->get_field_name('tags-mix');          ?>" value="1" <?php if ( '1' == $new_instance['tags-mix']          ) echo 'checked="checked"'; ?>> <?php _e( 'All',            'playbuzz' ); ?> 
			<input type="checkbox" name="<?php echo $this->get_field_name('tags-fun');          ?>" value="1" <?php if ( '1' == $new_instance['tags-fun']          ) echo 'checked="checked"'; ?>> <?php _e( 'Fun',            'playbuzz' ); ?> 
			<input type="checkbox" name="<?php echo $this->get_field_name('tags-pop');          ?>" value="1" <?php if ( '1' == $new_instance['tags-pop']          ) echo 'checked="checked"'; ?>> <?php _e( 'Pop',            'playbuzz' ); ?> 
			<input type="checkbox" name="<?php echo $this->get_field_name('tags-geek');         ?>" value="1" <?php if ( '1' == $new_instance['tags-geek']         ) echo 'checked="checked"'; ?>> <?php _e( 'Geek',           'playbuzz' ); ?> 
			<input type="checkbox" name="<?php echo $this->get_field_name('tags-sports');       ?>" value="1" <?php if ( '1' == $new_instance['tags-sports']       ) echo 'checked="checked"'; ?>> <?php _e( 'Sports',         'playbuzz' ); ?> 
			<input type="checkbox" name="<?php echo $this->get_field_name('tags-editors-pick'); ?>" value="1" <?php if ( '1' == $new_instance['tags-editors-pick'] ) echo 'checked="checked"'; ?>> <?php _e( 'Editor\'s Pick', 'playbuzz' ); ?> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('more-tags'); ?>"><?php _e( 'Custom Tags', 'playbuzz' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('more-tags'); ?>" name="<?php echo $this->get_field_name('more-tags'); ?>" value="<?php echo $new_instance['more-tags']; ?>" placeholder="<?php _e( 'Comma separated tags', 'playbuzz' ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('links'); ?>"><?php _e( 'Open Items at (location of section)', 'playbuzz' ); ?></label><br>
			<p><?php printf( __( '<a href="%s" target="_blank">Create</a> a new page containing the <code>[playbuzz-section]</code> shortcode. Then select it below as the destination page where items will open:', 'playbuzz' ), 'post-new.php?post_type=page' ); ?></p>
			<?php
			if ( isset( $new_instance['section-page'] ) ) {
				$link_page_id = $new_instance['section-page'];
			} else {
				$link_page_id = 0;
			}
			?>
			<?php wp_dropdown_pages( array( 'selected' => $link_page_id, 'post_type' => 'page', 'hierarchical' => 1, 'class' => 'widefat', 'id' => $this->get_field_id('section-page'), 'name' => $this->get_field_name('section-page'), 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '0' ) ); ?>
			<input type="hidden" class="widefat" id="<?php echo $this->get_field_id('links'); ?>" name="<?php echo $this->get_field_name('links'); ?>" value="<?php echo $new_instance['links']; ?>" placeholder="https://www.playbuzz.com/">
		</p>
		<?php
	}


	/*
	 * Loads the Widget's text domain for localization and translation.
	 */
	public function playbuzz() {
		load_plugin_textdomain( 'playbuzz', false, plugin_dir_path( __FILE__ ) . '/lang' );
	}


	/*
	 * Fired when the plugin is activated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public function activate( $network_wide ) {
		// Define activation functionality here
	}


	/*
	 * Fired when the plugin is deactivated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// Define deactivation functionality here
	}

}

add_action( 'widgets_init', create_function( '', 'register_widget("Playbuzz_Recommendations_Widget");' ) );
