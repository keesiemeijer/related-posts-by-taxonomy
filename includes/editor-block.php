<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since  2.4.2
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if there is support for the block editor.
 *
 * It also checks if the Block editor function `register_block_type` exists.
 *
 * @since 2.4.2
 *
 * @return True if there is support for the block editor.
 */
function km_rpbt_has_editor_block_support() {
	$plugin_support = km_rpbt_plugin_supports( 'editor_block' );
	$block_exists   = function_exists( 'register_block_type' );

	if ( $block_exists && $plugin_support ) {
		return true;
	}

	return false;
}

add_action( 'enqueue_block_editor_assets', 'km_rpbt_block_editor_assets' );

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * `wp-blocks`: includes block type registration and related functions.
 * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
 * `wp-i18n`: To internationalize the block's text.
 *
 * @since 2.4.2
 */
function km_rpbt_block_editor_assets() {

	if ( ! km_rpbt_has_editor_block_support() ) {
		return;
	}

	$plugin = km_rpbt_plugin();

	// Use un-minified Javascript when in debug mode.
	$debug = $plugin && $plugin->plugin_supports( 'debug' ) ? '' : '.min';

	// Scripts.
	wp_enqueue_script(
		'rpbt-related-posts-block', // Handle.
		RELATED_POSTS_BY_TAXONOMY_PLUGIN_URL . "includes/assets/js/editor-block.js",
		array( 'wp-blocks', 'wp-i18n', 'wp-url', 'wp-element', 'wp-data', 'wp-api-fetch', 'wp-editor', 'wp-components' )
	);

	// Styles.
	wp_enqueue_style(
		'rpbt-related-posts-block-css', // Handle.
		RELATED_POSTS_BY_TAXONOMY_PLUGIN_URL . 'includes/assets/css/editor-block.css',
		array( 'wp-edit-blocks' )
	);

	$order = array(
		'DESC' => __( 'Most terms in common', 'related-posts-by-taxonomy' ),
		'ASC'  => __( 'Least terms in common', 'related-posts-by-taxonomy' ),
		'RAND' => __( 'Randomly', 'related-posts-by-taxonomy' ),
	);

	wp_localize_script( 'rpbt-related-posts-block', 'km_rpbt_plugin_data',
		array(
			'post_types'       => $plugin->post_types,
			'taxonomies'       => $plugin->taxonomies,
			'default_tax'      => $plugin->default_tax,
			'all_tax'          => $plugin->all_tax,
			'formats'          => $plugin->formats,
			'image_sizes'      => $plugin->image_sizes,
			'order'            => $order,
			'preview'          => (bool) $plugin->plugin_supports( 'editor_block_preview' ),
			'html5_gallery'    => (bool) current_theme_supports( 'html5', 'gallery' ),
			'default_category' => absint( get_option( 'default_category' ) ),
		)
	);
}

/**
 * Registers the render callback for the editor block.
 *
 * @since 2.4.2
 */
function km_rpbt_register_block_type() {
	if ( ! km_rpbt_has_editor_block_support() ) {
		return;
	}

	register_block_type( 'related-posts-by-taxonomy/related-posts-block', array(
			'attributes' => array(
				'taxonomies' => array(
					'type'    => 'string',
					'default' => 'all',
				),
				'post_types' => array(
					'type' => 'string',
				),
				'terms' => array(
					'type' => 'string',
				),
				'title' => array(
					'type'    => 'string',
					'default' => __( 'Related Posts', 'related-posts-by-taxonomy' ),
				),
				'posts_per_page' => array(
					'type'    => 'int',
					'default' => 5,
				),
				'order' => array(
					'type'    => 'string',
					'default' => 'DESC',
				),
				'post_id' => array(
					'type'    => 'int',
					'default' => 0,
				),
				'format' => array(
					'type'    => 'string',
					'default' => 'links',
				),
				'image_size' => array(
					'type'    => 'string',
					'default' => 'thumbnail',
				),
				'columns' => array(
					'type'    => 'int',
					'default' => 3,
				),
				'link_caption' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'show_date' => array(
					'type'    => 'boolean',
					'default' => false,
				),
			),
			'render_callback' => 'km_rpbt_render_block_related_post',
		) );
}

/**
 * Render related posts block on the front end.
 *
 * @since 2.4.2
 *
 * @param array $args Block attributes
 * @return string Rendered related posts
 */
function km_rpbt_render_block_related_post( $args ) {
	if ( ! is_array( $args ) ) {
		return '';
	}

	// Set the type for the argument filters in the shortcode.
	$args['type'] = 'editor_block';

	return km_rpbt_get_feature_html( 'editor_block', $args, 'km_rpbt_validate_args' );
}
