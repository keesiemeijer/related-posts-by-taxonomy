<?php
/**
 * Gets the template used for display of related posts
 *
 * @since 0.1
 *
 * Used by widget and shortcode
 *
 * @param string $format The format to get the template for
 * @param string $type Supplied by widget or shortcode
 * @return mixed false on failure, template file path on success
 */
function km_rpbt_related_posts_by_taxonomy_template( $format = false, $type = false ) {
	$template = '';

	if ( $format ) {
		switch ( (string) $format ) {
		case 'posts': $template = 'related-posts-posts.php'; break;
		case 'excerpts': $template = 'related-posts-excerpts.php'; break;
		case 'thumbnails': $template = 'related-posts-thumbnails.php'; break;
		default: $template = 'related-posts-links.php'; break;
		}
	}

	$type = ( $type ) ? (string) $type : '';
	$theme_template = apply_filters( 'related_posts_by_taxonomy_template', $template, $type );
	$theme_template = locate_template( array( 'related-post-plugin/' . $theme_template ) );
	if ( $theme_template ) {
		return $theme_template;
	} else {
		if ( $template != '' ) {
			$path = plugin_dir_path( __FILE__ );
			if ( file_exists( $path . 'templates/' . $template ) )
				return $path . 'templates/' . $template;
		}
	}
	return false;
}