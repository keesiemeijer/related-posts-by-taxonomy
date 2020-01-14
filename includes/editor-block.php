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

	$asset_file = include RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/assets/js/editor-block/index.asset.php';

	// Scripts.
	wp_enqueue_script(
		'rpbt-related-posts-block', // Handle.
		RELATED_POSTS_BY_TAXONOMY_PLUGIN_URL . "includes/assets/js/editor-block/index.js",
		$asset_file['dependencies']
	);

	wp_enqueue_style(
		'rpbt-related-posts-block-css', // Handle.
		RELATED_POSTS_BY_TAXONOMY_PLUGIN_URL . 'includes/assets/css/styles.css'
	);

	$order = array(
		'DESC' => __( 'Most terms in common', 'related-posts-by-taxonomy' ),
		'ASC'  => __( 'Least terms in common', 'related-posts-by-taxonomy' ),
		'RAND' => __( 'Randomly', 'related-posts-by-taxonomy' ),
	);

	wp_localize_script( 'rpbt-related-posts-block', 'km_rpbt_plugin_data',
		array(
			'post_types'          => $plugin->post_types,
			'taxonomies'          => $plugin->taxonomies,
			'formats'             => $plugin->formats,
			'image_sizes'         => $plugin->image_sizes,
			'order'               => $order,
			'hide_empty'          => (bool) km_rpbt_plugin_supports( 'editor_block_hide_empty' ),
			'hide_empty_notice'   => km_rpbt_get_no_posts_found_notice(array()),
			'default_category_id' => absint( get_option( 'default_category' ) ),
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
				'fields' => array(
					'type'    => 'string',
					'default' => km_rpbt_plugin_supports( 'id_query' ) ? 'ids' : '',
				),
				'post_types' => array(
					'type' => 'string',
				),
				'taxonomies' => array(
					'type'    => 'string',
					'default' => 'km_rpbt_all_tax',
				),
				'format' => array(
					'type'    => 'string',
					'default' => 'links',
				),
				'title' => array(
					'type'    => 'string',
					'default' => __( 'Related Posts', 'related-posts-by-taxonomy' ),
				),
				'order' => array(
					'type'    => 'string',
					'default' => 'DESC',
				),
				'image_size' => array(
					'type'    => 'string',
					'default' => 'thumbnail',
				),
				'posts_per_page' => array(
					'type'    => 'int',
					'default' => 5,
				),
				'columns' => array(
					'type'    => 'int',
					'default' => 3,
				),
				'image_crop' => array(
					'type'    => 'boolean',
					'default' => false,
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

	$settings = km_rpbt_get_default_settings( 'editor_block' );
	$settings['gallery_format'] = 'editor_block';

	/**
	 * Filter default editor block attributes.
	 *
	 * @since 0.2.1
	 *
	 * @param array $defaults Default editor block arguments. See km_rpbt_related_posts_by_taxonomy_shortcode() for
	 *                        for more information about default editor block arguments.
	 */
	$defaults = apply_filters( "related_posts_by_taxonomy_editor_block_defaults", $settings );
	$defaults = array_merge( $settings, (array) $defaults );
	$args     = array_merge( $defaults, $args );

	$args['type'] = 'editor_block';
	$args         = km_rpbt_validate_args( $args );

	/**
	 * Filter validated editor block arguments.
	 *
	 * @since  0.1
	 *
	 * @param array $args editor block arguments. See km_rpbt_related_posts_by_taxonomy_shortcode() for
	 *                    for more information about editor block arguments.
	 */
	$args = apply_filters( "related_posts_by_taxonomy_editor_block_args", $args );
	$args = array_merge( $defaults, (array) $args );

	$args['type'] = 'editor_block';

	return km_rpbt_get_feature_html( 'editor_block', $args );
}
