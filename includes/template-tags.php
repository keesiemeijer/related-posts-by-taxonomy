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
 * Get the post classes from a post object.
 *
 * Gets the classes from the 'rpbt_post_class' property of a post object (if it exists).
 *
 * @since  2.4.0
 *
 * @param object $post  Post object.
 * @param string $class Space separated string of classes to add to the post classes.
 *                      Default empty string.
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
 * Add CSS classes to (related) post objects.
 *
 * This function is used after retrieving the related posts from the database or cache.
 *
 * Use the {@see 'related_posts_by_taxonomy_post_class'} filter to add post classes on a
 * post per post basis
 *
 * @since  2.4.0
 *
 * @param array        $related_posts Array with (related) post objects.
 * @param array|string $args          Widget or shortcode arguments.
 *                                    See km_rpbt_get_related_posts() for for more
 *                                    information on accepted arguments.
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
		 * Filter CSS classes used in related posts display templates.
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
 * Gets a related post link.
 *
 * The post date is appended depending on the `$show_date` value in the arguments.
 *
 * @since 2.4.0
 *
 * @param int|WP_Post|null $post Optional. Post ID or post object. Default is global $post.
 * @param array            $args Optional. Widget or shortcode arguments.
 *                               See km_rpbt_related_posts_by_taxonomy_shortcode() for for more
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

	return apply_filters( 'related_posts_by_taxonomy_post_link', $link, $post, compact( 'title', 'permalink, $args' ) );
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
 *                               See km_rpbt_related_posts_by_taxonomy_shortcode() for for more
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
	 * @param string Permalink.
	 */
	return apply_filters( 'related_posts_by_taxonomy_the_permalink', $permalink, $post, $args );
}


/**
 * Get the related post date.
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
