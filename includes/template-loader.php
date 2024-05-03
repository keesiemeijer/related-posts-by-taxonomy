<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets the template used for display of related posts
 *
 * @since 2.5.0
 *
 * Used by widget and shortcode
 *
 * @param string $format The format to get the template for.
 * @param string $type   Supplied by widget or shortcode.
 * @return mixed False on failure, template file path on success.
 */
function km_rpbt_get_template( $format = false, $type = false ) {

	$template = 'related-posts-links.php'; // Default template.
	$dir      = 'related-post-plugin';  // Default theme directory to search in.
	$format   = ( $format ) ? (string) $format : '';
	$type     = ( $type ) ? (string) $type : '';

	$base_dir = RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR;

	switch ( $format ) {
		case 'posts': $template = 'related-posts-posts.php'; break;
		case 'excerpts': $template = 'related-posts-excerpts.php'; break;
		case 'thumbnails': $template = 'related-posts-thumbnails.php'; break;
	}

	/**
	 * Filter the theme directory used.
	 *
	 * @since 2.7.6
	 *
	 * @param string $dir      theme directory.
	 * @param string $type     Template used for widget or shortcode.
	 */
	$theme_dir = apply_filters( 'related_posts_by_taxonomy_template_directory', $dir, $type, $format );

	$theme_dir = is_string( $theme_dir ) ? $theme_dir : $dir;
	$theme_dir = trim ( trailingslashit( $theme_dir ) );
	$theme_dir = ( '/' === $theme_dir) ? '' : $theme_dir;

	/**
	 * Filter the theme template used.
	 *
	 * @since 0.1
	 *
	 * @param string $template template file name.
	 * @param string $type     Template used for widget or shortcode.
	 */
	$theme_template = apply_filters( 'related_posts_by_taxonomy_template', $template, $type, $format );
	$theme_template = locate_template( array( $theme_dir . $theme_template ) );

	if ( $theme_template && is_file( $theme_template ) ) {
		return $theme_template;
	} else {

		if ( file_exists( $base_dir . 'templates/' . $template ) ) {
			return $base_dir . 'templates/' . $template;
		}
	}

	return false;
}
