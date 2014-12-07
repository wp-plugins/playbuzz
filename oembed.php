<?php
/*
 * Playbuzz oEmbed
 * Add playbuzz oEmbed support to WordPress
 *
 * Check for:
 * 		http://playbuzz.com/*
 * 		https://playbuzz.com/*
 * 		http://www.playbuzz.com/*
 * 		https://www.playbuzz.com/*
 *
 * @since 0.5.0
 */
 
 
 /*
 * Add oEmbed support
 *
 * @since 0.5.0
 */
function register_oembed_provider() {
	wp_oembed_add_provider( '#https?://(www\.)?playbuzz.com/.*#i', 'https://www.playbuzz.com/api/oembed/', true );
}
add_action( 'init', 'register_oembed_provider' );