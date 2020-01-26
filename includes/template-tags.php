<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prints the post classes.
 *
 * This function is used in the related posts display templates.
 *
 * @since 2.4.0
 *
 * @param object       $post Post object.
 * @param array|string $args Widget or shortcode arguments or string with post classes.
 */
function km_rpbt_post_class( $post = null, $args = '' ) {
	$classes = km_rpbt_get_post_classes( $post, $args );

	if ( $classes ) {
		echo ' class="' . $classes . '"';
	}
}

/**
 * Get the post classes from a post object.
 *
 * Gets the classes from the 'rpbt_post_class' property of a post object (if it exists).
 *
 * @since 2.4.0
 *
 * @param object       $post Post object.
 * @param array|string $args Widget or shortcode arguments or string with post classes.
 * @return string Post classes string.
 */
function km_rpbt_get_post_classes( $post = null, $args = '' ) {
	$classes = '';

	if ( isset( $post->rpbt_post_class ) && is_string( $post->rpbt_post_class ) ) {
		$classes = $post->rpbt_post_class;
	}

	// Backward compatibility PHP < 5.4 needs check is_array() for isset().
	$is_args    = is_array( $args ) && isset( $args['post_class'] );
	$post_class = $is_args ? $args['post_class'] : $args;

	if ( is_string( $post_class ) && $post_class ) {
		$classes .= ' ' . $post_class;
	}

	$classes = km_rpbt_sanitize_classes( $classes );
	$classes = explode( ' ', $classes );

	// Backwards compatibility for filter
	$index = 0;

	/**
	 * Filter CSS classes used in related posts display templates.
	 *
	 * @since 2.4.0
	 *
	 * @param array        $classes Array with post classes.
	 * @param object       $post    Current related post object.
	 * @param array|string $args    Widget or shortcode arguments or string with post classes.
	 * @param int          $index   Deprecated. Default 0
	 */
	$classes = apply_filters( 'related_posts_by_taxonomy_post_class', $classes, $post, $args, $index );

	return km_rpbt_sanitize_classes( implode( ' ', $classes ) );
}

/**
 * Sanitize classnames.
 *
 * @since 2.4.0
 *
 * @param string $classes String with classnames separated by spaces.
 * @return string Sanitized string with classnames.
 */
function km_rpbt_sanitize_classes( $classes ) {
	if ( ! is_string( $classes ) ) {
		return '';
	}

	$classes = esc_attr( trim( $classes ) );
	$classes = preg_replace( '/\s+/', ' ', $classes );
	$classes = array_map( 'sanitize_html_class', explode( ' ', $classes ) );
	$classes = array_unique( $classes );

	return implode( ' ', $classes );
}

/**
 * Display of the related post link.
 *
 * Used in the templates for displaying related posts.
 *
 * @since 2.4.0
 *
 * @param int|WP_Post|null $post Optional. Post ID or post object. Default is global $post.
 * @param array            $args Widget or shortcode arguments.
 *
 */
function km_rpbt_post_link( $post = null, $args = array() ) {
	echo km_rpbt_get_post_link( $post, $args ) . "\n";
}

/**
 * Get the related post link HTML.
 *
 * The post date is appended depending on the `$show_date` value in the arguments.
 *
 * @since 2.4.0
 *
 * @param int|WP_Post|null $post Optional. Post ID or post object. Default is global $post.
 * @param array            $args Optional. Widget or shortcode arguments.
 *                               See km_rpbt_related_posts_by_taxonomy_shortcode() for more
 *                               information on accepted arguments.
 * @return string Related post link HTML.
 */
function km_rpbt_get_post_link( $post = null, $args = array() ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	$defaults = array(
		'show_date'  => false,
		'title_attr' => false,
		'type'       => '',
	);

	// Backwards compatibility
	$args = is_bool( $args ) ? array( 'title_attr' => $args ) : $args;

	$args = wp_parse_args( $args, $defaults );
	$args = km_rpbt_validate_booleans( $args, $defaults );

	$title      = get_the_title( $post );
	$link       = '';
	$title_attr = '';

	if ( ! $title ) {
		$title = get_the_ID();
	}

	if ( $args['title_attr'] && $title ) {
		$title_attr = ' title="' . esc_attr( $title ) . '"';
	}

	$permalink  = km_rpbt_get_permalink( $post, $args );
	if ( $permalink && $title ) {
		$link = '<a href="' . $permalink . '"' . $title_attr . '>' . $title . '</a>';
		$link .= $args['show_date'] ? ' ' . km_rpbt_get_post_date( $post ) : '';
	}

	$link_attr = compact( 'title', 'permalink' );

	/**
	 * Filter related post link HTML.
	 *
	 * @since 2.4.0
	 * @param string $link Related post link HTML.
	 * @param Object $post Post object.
	 * @param array  $attr Link attributes.
	 */
	return apply_filters( 'related_posts_by_taxonomy_post_link', $link, $post, $link_attr, $args );
}

/**
 * Message when no related posts are found.
 *
 * @since 2.7.3
 *
 * @param array $args Optional. Widget or shortcode arguments.
 *                    See km_rpbt_related_posts_by_taxonomy_shortcode() for more
 *                    information on accepted arguments.
 */
function km_rpbt_no_posts_found_notice( $args = array() ) {
	echo km_rpbt_get_no_posts_found_notice( $args );
}

/**
 * Message when no related posts are found.
 *
 * @since 2.7.3
 *
 * @param array $args Optional. Widget or shortcode arguments.
 *                    See km_rpbt_related_posts_by_taxonomy_shortcode() for more
 *                    information on accepted arguments.
 */
function km_rpbt_get_no_posts_found_notice( $args = array() ) {
	$notice = __( 'No related posts found', 'related-posts-by-taxonomy' );

	/**
	 * Filter the no related posts found message
	 *
	 * @since 2.7.3
	 *
	 * @param string $notice No posts found notice. Default "No related posts found".
	 * @param array  $args   Optional. Widget or shortcode arguments.
	 *                       See km_rpbt_related_posts_by_taxonomy_shortcode() for more
	 *                       information on accepted arguments.
	 */
	return apply_filters( 'related_posts_by_taxonomy_no_posts_found_notice', $notice, $args );
}

/**
 * Wrapper function for get_permalink() to allow filtering.
 *
 * Filter related posts permalinks with the {@see 'related_posts_by_taxonomy_the_permalink'} filter.
 *
 * @since 2.5.1
 *
 * @param int|WP_Post|null $post Optional. Post ID or post object. Default is global $post.
 * @param array            $args Widget or shortcode arguments.
 *                               See km_rpbt_related_posts_by_taxonomy_shortcode() for more
 *                               information on accepted arguments.
 * @return string permalink.
 */
function km_rpbt_get_permalink( $post = null, $args = '' ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	$permalink = esc_url( apply_filters( 'the_permalink', get_permalink( $post ), $post ) );

	/**
	 * Filter the permalink used for related posts.
	 *
	 * @since 2.5.1
	 *
	 * @param string Permalink.
	 */
	return apply_filters( 'related_posts_by_taxonomy_the_permalink', $permalink, $post, $args );
}


/**
 * Get the related post date HTML.
 *
 * @since 2.5.1
 *
 * @param int|WP_Post|null $post Optional. Post ID or post object. Default is global $post.
 * @return string Post date wrapped in a HTML `<time>` tag.
 */
function km_rpbt_get_post_date( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	$time_string = '<time class="rpbt-post-date" datetime="%1$s">%2$s</time>';

	$time_string = sprintf(
		$time_string,
		get_the_date( DATE_W3C, $post ),
		get_the_date( '', $post )
	);

	return $time_string;
}
