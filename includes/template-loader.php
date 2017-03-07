<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets the template used for display of related posts
 *
 * @since 0.1
 *
 * Used by widget and shortcode
 *
 * @param string $format The format to get the template for.
 * @param string $type   Supplied by widget or shortcode.
 * @return mixed False on failure, template file path on success.
 */
function km_rpbt_related_posts_by_taxonomy_template( $format = false, $type = false ) {

	$template = 'related-posts-links.php'; // Default template.
	$format   = ( $format ) ? (string) $format : '';
	$type     = ( $type ) ? (string) $type : '';

	switch ( $format ) {
		case 'posts': $template = 'related-posts-posts.php'; break;
		case 'excerpts': $template = 'related-posts-excerpts.php'; break;
		case 'thumbnails': $template = 'related-posts-thumbnails.php'; break;
	}

	/**
	 * Filter the template used.
	 *
	 * @since 0.1
	 *
	 * @param string $template template file name.
	 * @param string $type     Template used for widget or shortcode.
	 */
	$theme_template = apply_filters( 'related_posts_by_taxonomy_template', $template, $type, $format );

	$theme_template = locate_template( array( 'related-post-plugin/' . $theme_template ) );
	if ( $theme_template ) {
		return $theme_template;
	} else {

		if ( file_exists( RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'templates/' . $template ) ) {
			return RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'templates/' . $template;
		}
	}

	return false;
}
