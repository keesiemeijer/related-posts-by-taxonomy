<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prints the post class
 *
 * @since  2.4.0
 *
 * @param object $post  Post object.
 * @param string $class String of classes to add to the post classes. Default empty string.
 */
function km_rpbt_post_class( $post = null, $class = '' ) {
	$classes = km_rpbt_get_post_classes( $post, $class );

	if ( $classes ) {
		echo ' class="' . $classes . '"';
	}
}

/**
 * Returns post classes if set.
 *
 * @since  2.4.0
 *
 * @param object $post  Post object.
 * @param string $class String of classes to add to the post classes. Default empty string.
 * @return string Post classes string.
 */
function km_rpbt_get_post_classes( $post = null, $class = '' ) {
	$classes = '';
	if ( isset( $post->rpbt_post_class ) && is_string( $post->rpbt_post_class ) ) {
		$classes = $post->rpbt_post_class;
	}

	if ( is_string( $class ) ) {
		$classes .= ' ' . $class;
	}

	return km_rpbt_sanitize_classes( $classes );
}

/**
 * Sanitize classnames.
 *
 * @since  2.4.0
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
 * Add classes to array of (related) post objects.
 *
 * @since  2.4.0
 *
 * @param array $related_posts Array with related post objects.
 * @param int   $post_id       Current post id.
 * @param array $args          Array with widget or shortcode arguments.
 * @return array Array with related post objects with classes added.
 */
function km_rpbt_add_post_classes( $related_posts, $args = '' ) {
	if ( ! is_array( $related_posts ) ) {
		return $related_posts;
	}

	foreach ( array_values( $related_posts ) as $index => $post ) {
		if ( ! is_object( $post ) ) {
			continue;
		}

		$add_classes = '';
		if ( isset( $args['post_class'] ) ) {
			$add_classes = $args['post_class'];
		}

		$classes = km_rpbt_get_post_classes( $post, $add_classes );
		$classes = explode( ' ', $classes );

		/**
		 * Filter post classes.
		 *
		 * @since 2.4.0
		 *
		 * @param array  $classes Array with post classes.
		 * @param object $post    Current related post object.
		 * @param array  $args    Widget or shortcode arguments.
		 * @param int    $index   Index position of related post. Starts at 0.
		 */
		$classes = apply_filters( 'related_posts_by_taxonomy_post_class', $classes, $post, $args, $index );
		$classes = km_rpbt_sanitize_classes( implode( ' ', $classes ) );

		$related_posts[ $index ]->rpbt_post_class = $classes;
	}

	return $related_posts;
}

/**
 * Display of the related post link.
 *
 * @since 2.4.0
 *
 * @param object $post       Post object.
 * @param bool   $title_attr Whether to use a title attribute in the link. Default false.
 */
function km_rpbt_post_title_link( $post, $title_attr = false ) {
	echo km_rpbt_get_related_post_title_link( $post, $title_attr );
}

/**
 * Get the related $post link.
 *
 * @since 2.4.0
 *
 * @param object $post       Post object.
 * @param bool   $title_attr Whether to use a title attribute in the link. Default false.
 * @return string Related post link HTML.
 */
function km_rpbt_get_related_post_title_link( $post, $title_attr = false ) {
	$link = '';
	$title = get_the_title( $post );

	if ( ! $title ) {
		$title = get_the_ID();
	}

	if ( $title_attr && $title ) {
		$title_attr = ' title="' . esc_attr( $title ) . '"';
	}

	$title_attr = is_string( $title_attr ) ? $title_attr : '';
	$permalink  = esc_url( apply_filters( 'the_permalink', get_permalink( $post ), $post ) );

	if ( $permalink && $title ) {
		$link = '<a href="' . $permalink . '"' . $title_attr . '>' . $title . '</a>';
	}

	return apply_filters( 'related_posts_by_taxonomy_post_link', $link, $post, compact( 'title', 'permalink' ) );
}
