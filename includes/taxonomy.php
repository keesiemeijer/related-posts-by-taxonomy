<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Returns array with validated taxonomy names.
 *
 * @since 2.2
 * @param string|array $taxonomies Taxonomies.
 * @return array        Array with taxonomy names.
 */
function km_rpbt_get_taxonomies( $taxonomies ) {
	$plugin  = km_rpbt_plugin();

	if ( $plugin && ( $taxonomies === $plugin->all_tax ) ) {
		$taxonomies = array_keys( $plugin->taxonomies );
	}

	$taxonomies = km_rpbt_get_comma_separated_values( $taxonomies );

	return array_values( array_filter( $taxonomies, 'taxonomy_exists' ) );
}

/**
 * Get all public taxonomies.
 *
 * @since 2.5.0
 *
 * @return array Array with all public taxonomies.
 */
function km_rpbt_get_public_taxonomies() {
	$plugin = km_rpbt_plugin();
	return isset( $plugin->all_tax ) ? km_rpbt_get_taxonomies( $plugin->all_tax ) : array();
}

/**
 * Get the terms from a post or arguments.
 *
 * @since  2.5.0
 *
 * @param int          $post_id    The post id to get terms for.
 * @param array|string $taxonomies The taxonomies to retrieve terms from.
 * @param string|array $args       {
 *     Optional. Arguments to get terms.
 *
 *     @type array|string   $terms            Terms to use for the related posts query. Array or comma separated
 *                                            list of term ids. The terms need to be in the taxonomies set by the
 *                                            `$taxonomies` argument. Default empty.
 *     @type array|string   $include_terms    Terms to include for the related posts query. Array or comma separated
 *                                            list of term ids. Only includes terms also assigned to the post set by the
 *                                            `$post_id` argument. Default empty.
 *     @type array|string   $exclude_terms    Terms to exlude for the related posts query. Array or comma separated
 *                                            list of term ids. Default empty
 *     @type boolean        $related          If false the `terms` and `$include_terms` terms are returned without
 *                                            checking taxonomies or post terms. Default true.
 * }
 * @return array Array with term ids.
 */
function km_rpbt_get_terms( $post_id, $taxonomies, $args = array() ) {
	$terms      = array();
	$post_id    = absint( $post_id );
	$taxonomies = km_rpbt_get_taxonomies( $taxonomies );
	$args       = km_rpbt_sanitize_args( $args );

	// Unrelated terms
	if ( ! $args['related'] && ( $args['terms'] || $args['include_terms'] ) ) {
		return $args['terms'] ? $args['terms'] : $args['include_terms'];
	}

	if ( $args['related'] && empty( $taxonomies ) ) {
		// Taxonomies are needed for related terms.
		return array();
	}

	if ( $args['terms'] ) {
		$term_args = array(
			'include'  => $args['terms'],
			'taxonomy' => $taxonomies,
			'fields'   => 'ids',
		);

		// Get terms from taxonomies.
		$terms = get_terms( $term_args );
		$terms = ! is_wp_error( $terms ) ? $terms : array();
	} else {

		// Get post terms.
		$terms = wp_get_object_terms( $post_id, $taxonomies, array( 'fields' => 'ids', ) );
		$terms = ! is_wp_error( $terms ) ? $terms : array();

		// Only use included terms from the post terms.
		if ( $args['include_terms'] ) {
			$terms = array_intersect( $args['include_terms'], $terms );
		}
	}

	// Exclude terms.
	if ( ! empty( $terms ) && ! empty( $args['exclude_terms'] ) ) {
		$terms = array_diff( $terms , $args['exclude_terms'] );
	}

	return array_values( $terms );
}

function km_rpbt_get_parent_terms($terms, $taxonomies) {
	$taxonomies = km_rpbt_get_taxonomies( $taxonomies );
	if(! ($terms && $taxonomies) ) {
		return $terms;
	}

	$args = array(
		'include'  => $terms,
		'taxonomy' => $taxonomies,
	);

	$terms = get_terms( $args );
	$terms = ! is_wp_error( $terms ) ? $terms : array();

	$parents = array();
	foreach ( $terms as $term ) {
		if ( ! is_taxonomy_hierarchical( $term->taxonomy ) ) {
			continue;
		}

		$ancestors = get_ancestors( $term->term_id, $term->taxonomy );
		if ( $ancestors ) {
			$parents = array_merge( $parents, $ancestors );
		}
	}

	return km_rpbt_validate_ids( $parents );
}