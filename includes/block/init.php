<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since 	1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'enqueue_block_editor_assets', 'rpbt_block_editor_assets' );

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * `wp-blocks`: includes block type registration and related functions.
 * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
 * `wp-i18n`: To internationalize the block's text.
 *
 * @since 1.0.0
 */
function rpbt_block_editor_assets() {
	// Scripts.
	wp_enqueue_script(
		'rpbt-related-posts-block', // Handle.
		plugins_url( '/block/block.build.js', dirname( __FILE__ ) ),
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-utils' )
	);

	// Styles.
	wp_enqueue_style(
		'rpbt-related-posts-block-css', // Handle.
		plugins_url( 'editor.css', dirname( __FILE__ ) ),
		array( 'wp-edit-blocks' )
	);
}

/**
 * Render related posts block on the front end.
 * 
 * @param  array $attributes Block attributes
 * @return string Rendered related posts
 */
function rpbt_render_block_related_post( $attributes ) {
	return '';
}

register_block_type( 'related-posts-by-taxonomy/related-posts-block', array(
	'attributes'      => array(
		'taxonomies'      => array(
			'type' => 'string',
		),
		'post_id'     => array(
			'type'    => 'number',
			'default' => 0,
		),
	),

	'render_callback' => 'rpbt_render_block_related_posts',
) );

