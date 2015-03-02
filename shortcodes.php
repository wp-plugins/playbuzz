<?php
/*
 * Item Shortcode
 * Display a specific item in a desired location on your content.
 *
 * usage: [playbuzz-item url="https://www.playbuzz.com/jonathang/players-and-playmates-playoffs"]
 *
 * @since 0.1.0
 */
add_shortcode( 'playbuzz-item', 'playbuzz_item_shortcode' );
add_shortcode( 'playbuzz-game', 'playbuzz_item_shortcode' );
add_shortcode( 'playbuzz-post', 'playbuzz_item_shortcode' );



/*
 * Section Shortcode
 * Display a list of items according specific tags in a desired location on your content.
 *
 * usage: [playbuzz-section tags="Celebrities"]
 *
 * @since 0.1.0
 */
add_shortcode( 'playbuzz-section', 'playbuzz_section_shortcode' );
add_shortcode( 'playbuzz-hub',     'playbuzz_section_shortcode' );
add_shortcode( 'playbuzz-archive', 'playbuzz_section_shortcode' );



/*
 * Recommendations / Related-Content Shortcode
 * Display playbuzz related playful content links and recommendations according specific tags in a desired location on your content.
 *
 * usage: [playbuzz-recommendations tags="Celebrities" links="https://www.mysite.com/url_in_your_site_where_you_displayed_playbuzz_items"]
 *
 * @since 0.1.0
 */
add_shortcode( 'playbuzz-related',         'playbuzz_recommendations_shortcode' );
add_shortcode( 'playbuzz-recommendations', 'playbuzz_recommendations_shortcode' );



/*
 * Shortcode functions
 *
 * @since 0.1.1
 */
function playbuzz_item_shortcode( $atts ) {

	// Load globals
	global $wp_version;

	// Load publisher options from DB defined 
	$options = get_option( 'playbuzz' );

	// Tags
	$tags = '';
	if ( '1' == $options['tags-mix']          ) $tags .= 'All,';
	if ( '1' == $options['tags-fun']          ) $tags .= 'Fun,';
	if ( '1' == $options['tags-pop']          ) $tags .= 'Pop,';
	if ( '1' == $options['tags-geek']         ) $tags .= 'Geek,';
	if ( '1' == $options['tags-sports']       ) $tags .= 'Sports,';
	if ( '1' == $options['tags-editors-pick'] ) $tags .= 'EditorsPick_Featured,';
	$tags .= $options['more-tags'];
	$tags = rtrim( $tags, ',');

	// Attributes with default values
	extract( shortcode_atts(
		array(
			'key'       => $options['key'],                                     // api key allowing configuration and analytics
			'tags'      => $tags,                                               // filter by tags
			'game'      => '',                                                  // defines the item that will be loaded by the IFrame (deprecated in 0.3 ; use "url" attribute)
			'url'       => '',                                                  // defines the item that will be loaded by the IFrame (added in 0.3)
			'info'      => ( '1' == $options['info']      ? 'true' : 'false' ), // show item info (thumbnail, name, description, editor, etc)
			'shares'    => ( '1' == $options['shares']    ? 'true' : 'false' ), // show sharing buttons 
			'comments'  => ( '1' == $options['comments']  ? 'true' : 'false' ), // show comments control from the item page
			'recommend' => ( '1' == $options['recommend'] ? 'true' : 'false' ), // show recommendations for more items
			'links'     => '',                                                  // destination url in your site where new items will be displayed
			'width'     => 'auto',                                              // define custom width (added in 0.3)
			'height'    => 'auto',                                              // define custom height (added in 0.3)
			'margintop' => $options['margin-top'],                              // margin top for score bar in case there is a floating bar
		), $atts )
	);

	// Playbuzz Embed Code
	$code = '
		<script type="text/javascript" src="//cdn.playbuzz.com/widget/feed.js"></script>
		<div class="pb_feed" data-provider="WordPress ' . $wp_version . '" data-key="' . $key . '" data-tags="' . $tags . '" data-game="' . $url . $game . '" data-game-info="' . $info . '" data-comments="' . $comments . '" data-shares="' . $shares . '" data-recommend="' . $recommend . '" data-links="' . $links . '" data-width="' . $width . '" data-height="' . $height . '" data-margin-top="' . $margintop . '"></div>
	';

	// Theme Visibility
	if ( 'content' == $options['embeddedon'] ) {
		// Show only in singular pages
		if ( is_singular() ) {
			return $code;
		}
	} elseif ( 'all' == $options['embeddedon'] ) {
		// Show in all pages
		return $code;
	} else {
		// BUGFIX: after update to v0.3, no "embeddedon' defined and all content gone.
		return $code;
	}

}

/*
 * Shortcode functions
 *
 * @since 0.1.4
 */
function playbuzz_section_shortcode( $atts ) {

	// Load globals
	global $wp_version;

	// Load publisher options from DB defined 
	$options = get_option( 'playbuzz' );

	// Tags
	$tags = '';
	if ( '1' == $options['tags-mix']          ) $tags .= 'All,';
	if ( '1' == $options['tags-fun']          ) $tags .= 'Fun,';
	if ( '1' == $options['tags-pop']          ) $tags .= 'Pop,';
	if ( '1' == $options['tags-geek']         ) $tags .= 'Geek,';
	if ( '1' == $options['tags-sports']       ) $tags .= 'Sports,';
	if ( '1' == $options['tags-editors-pick'] ) $tags .= 'EditorsPick_Featured,';
	$tags .= $options['more-tags'];
	$tags = rtrim( $tags, ',');

	// Attributes with default values
	extract( shortcode_atts(
		array(
			'key'       => $options['key'],                                     // api key allowing configuration and analytics
			'tags'      => $tags,                                               // filter by tags
			'game'      => '',                                                  // defines the item that will be loaded by the IFrame (deprecated in 0.3 ; use "url" attribute)
			'url'       => '',                                                  // defines the item that will be loaded by the IFrame (added in 0.3)
			'info'      => ( '1' == $options['info']      ? 'true' : 'false' ), // show item info (thumbnail, name, description, editor, etc)
			'shares'    => ( '1' == $options['shares']    ? 'true' : 'false' ), // show sharing buttons 
			'comments'  => ( '1' == $options['comments']  ? 'true' : 'false' ), // show comments control from the item page
			'recommend' => ( '1' == $options['recommend'] ? 'true' : 'false' ), // show recommendations for more items
			'links'     => '',                                                  // destination url in your site where new items will be displayed
			'width'     => 'auto',                                              // defines the width (added in 0.3)
			'height'    => 'auto',                                              // defines the height (added in 0.3)
			'margintop' => $options['margin-top'],                              // margin top for score bar in case there is a floating bar
		), $atts )
	);

	// Playbuzz Embed Code
	$code = '
		<script type="text/javascript" src="//cdn.playbuzz.com/widget/feed.js"></script>
		<div class="pb_feed" data-provider="WordPress ' . $wp_version . '" data-key="' . $key . '" data-tags="' . $tags . '" data-game="' . $url . $game . '" data-game-info="' . $info . '" data-comments="' . $comments . '" data-shares="true" data-recommend="' . $recommend . '" data-links="' . $links . '" data-width="' . $width . '" data-height="' . $height . '" data-margin-top="' . $margintop . '"></div>
	';

	// Theme Visibility
	if ( 'content' == $options['embeddedon'] ) {
		// Show only in singular pages
		if ( is_singular() ) {
			return $code;
		}
	} elseif ( 'all' == $options['embeddedon'] ) {
		// Show in all pages
		return $code;
	} else {
		// BUGFIX: after update to v0.3, no "embeddedon' defined and all content gone.
		return $code;
	}

}

/*
 * Shortcode functions
 *
 * @since 0.1.4
 */
function playbuzz_recommendations_shortcode( $atts ) {

	// Load globals
	global $wp_version;

	// Load publisher options from DB defined 
	$options = get_option( 'playbuzz' );

	// Tags
	$tags = '';
	if ( '1' == $options['tags-mix']          ) $tags .= 'All,';
	if ( '1' == $options['tags-fun']          ) $tags .= 'Fun,';
	if ( '1' == $options['tags-pop']          ) $tags .= 'Pop,';
	if ( '1' == $options['tags-geek']         ) $tags .= 'Geek,';
	if ( '1' == $options['tags-sports']       ) $tags .= 'Sports,';
	if ( '1' == $options['tags-editors-pick'] ) $tags .= 'EditorsPick_Featured,';
	$tags .= $options['more-tags'];
	$tags = rtrim( $tags, ',');

	// Attributes with default values
	extract( shortcode_atts(
		array(
			'key'     => $options['key'],     // api key allowing configuration and analytics
			'view'    => $options['view'],    // set view type
			'items'   => $options['items'],   // number of items to display
			'links'   => $options['links'],   // destination url in your site where new items will be displayed
			'tags'    => $tags,               // filter by tags
			'nostyle' => 'false',             // set style
		), $atts )
	);

	// Playbuzz Embed Code
	$code = '
		<script type="text/javascript" src="//cdn.playbuzz.com/widget/widget.js"></script>
		<div class="pb_recommendations" data-provider="WordPress ' . $wp_version . '" data-key="' . $key . '" data-tags="' . $tags . '" data-view="' . $view . '" data-num-items="' . $items . '" data-links="' . $links . '" data-nostyle="' . $nostyle . '"></div>
	';

	// Theme Visibility
	if ( 'content' == $options['embeddedon'] ) {
		// Show only in singular pages
		if ( is_singular() ) {
			return $code;
		}
	} elseif ( 'all' == $options['embeddedon'] ) {
		// Show in all pages
		return $code;
	} else {
		// BUGFIX: after update to v0.3, no "embeddedon' defined and all content gone.
		return $code;
	}

}
