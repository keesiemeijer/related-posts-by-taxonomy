<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to query related posts with Javascript.
 *
 * This feature allows you to query related posts
 * after the page has loaded.
 *
 * @since 2.6.0
 */
class Related_Posts_By_Taxonomy_Ajax_Query {

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

		add_action( 'wp_ajax_rpbt_ajax_query',  array( $this, 'query_ajax' ) );
		add_action( "wp_ajax_nopriv_rpbt_ajax_query",  array( $this, 'query_ajax' ) );

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
		wp_register_script( 'rpbt-ajax-query', RELATED_POSTS_BY_TAXONOMY_PLUGIN_URL . 'includes/assets/js/ajax-query.js', array( 'jquery' ), false, true );
		wp_localize_script( 'rpbt-ajax-query', 'rpbt_ajax_query', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'rpbt_ajax_query_nonce' ),
			)
		);

		wp_enqueue_script( 'rpbt-ajax-query' );
	}

	/**
	 * Handles AJAX updates.
	 *
	 * @access public
	 * @since 2.6.0
	 *
	 * @return string JSON data
	 */
	public function query_ajax() {
		check_ajax_referer( 'rpbt_ajax_query_nonce', 'nonce' );

		if ( ! ( isset( $_POST['args'] ) && $_POST['args'] ) ) {
			wp_send_json_error();
		}

		$args     = stripslashes_deep( $_POST['args'] );
		$type     = km_rpbt_get_settings_type( $args );
		$defaults = km_rpbt_get_default_settings( $type );
		$args     = array_merge( $defaults, $args );

		$related_posts = km_rpbt_get_related_posts( $args['post_id'], $args );

		/*
		 * Whether to hide the feature if no related posts are found.
		 * Default true.
		 */
		$hide_empty = (bool) km_rpbt_plugin_supports( "{$type}_hide_empty" );

		$html = '';
		if ( ! $hide_empty || ! empty( $related_posts ) ) {
			$html = km_rpbt_get_related_posts_html( $related_posts, $args );
		}

		/** This action is documented in includes/functions.php */
		do_action( 'related_posts_by_taxonomy_after_display', $type );

		wp_send_json_success ( $html );
	}
}
