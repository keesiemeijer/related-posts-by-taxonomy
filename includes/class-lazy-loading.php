<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lazy loading feature
 *
 * Query related posts after the page has loaded (with Ajax).
 *
 * @since 2.6.0
 */
class Related_Posts_By_Taxonomy_Lazy_Loading {

	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization
	 *
	 * @access public
	 * @since 2.6.0
	 */
	public function init() {
		// ajax actions
		add_action( 'wp_ajax_rpbt_lazy_loading',  array( $this, 'lazy_loading_query' ) );
		add_action( "wp_ajax_nopriv_rpbt_lazy_loading",  array( $this, 'lazy_loading_query' ) );

		// Enqueue scripts and styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts_and_styles' ), 11 );
	}

	/**
	 * Enqueues scripts and styles.
	 *
	 * @access public
	 * @since 2.6.0
	 */
	public function scripts_and_styles() {
		$debug = defined( 'WP_DEBUG') && WP_DEBUG ? '' : '.min';
		wp_register_script( 'rpbt-lazy-loading', RELATED_POSTS_BY_TAXONOMY_PLUGIN_URL . "includes/assets/js/lazy-loading{$debug}.js", array( 'jquery' ), false, true );
		wp_localize_script( 'rpbt-lazy-loading', 'rpbt_lazy_loading', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'rpbt_lazy_loading_nonce' ),
				'loading' => __('Loading...', 'related-posts-by-taxonomy' ),
			)
		);

		wp_enqueue_script( 'rpbt-lazy-loading' );
	}

	/**
	 * Handles AJAX updates for the lazy loading feature.
	 *
	 * @access public
	 * @since 2.6.0
	 *
	 * @return string JSON data
	 */
	public function lazy_loading_query() {
		check_ajax_referer( 'rpbt_lazy_loading_nonce', 'nonce' );

		if ( ! ( isset( $_POST['args'] ) && $_POST['args'] ) ) {
			wp_send_json_error();
		}

		$args     = stripslashes_deep( $_POST['args'] );
		$type     = km_rpbt_get_settings_type( $args );
		$defaults = km_rpbt_get_default_settings( $type );
		$args     = array_merge( $defaults, $args );

		// Get allowed fields for use in templates
		$args['fields'] = km_rpbt_get_template_fields( $args );

		$related_posts = km_rpbt_get_related_posts( $args['post_id'], $args );
		$hide_empty    = (bool) km_rpbt_plugin_supports( "{$type}_hide_empty", $args );

		$html = '';
		if ( ! $hide_empty || ! empty( $related_posts ) ) {
			$html = km_rpbt_get_related_posts_html( $related_posts, $args );
		}

		/** This action is documented in includes/functions.php */
		do_action( 'related_posts_by_taxonomy_after_display', $type );

		wp_send_json_success ( $html );
	}
}
