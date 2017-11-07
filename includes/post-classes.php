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

	return implode( ' ', $classes );
}

/**
 * Add classes to (related) post objects.
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
		 * @param array $classes Array with post classes.
		 * @param int   $post_id Current post id.
		 * @param int   $index   Index position of related post. Starts at 0.
		 * @param array $args    Widget or shortcode arguments.
		 */
		$classes = apply_filters( 'related_posts_by_taxonomy_post_class', $classes, $args, $index );
		$classes = km_rpbt_sanitize_classes( implode( ' ', $classes ) );

		$related_posts[ $index ]->rpbt_post_class = $classes;
	}

	return $related_posts;
}
