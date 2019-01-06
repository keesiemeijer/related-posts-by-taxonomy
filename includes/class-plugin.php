<?php
/**
 * Class to get all the defaults needed for this plugin.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Related_Posts_By_Taxonomy_Plugin' ) ) {
	/**
	 * Class to setup plugin features and enqueue scripts.
	 *
	 * Features:
	 * - shortcode
	 * - widget
	 * - cache
	 * - debug
	 * - WP Rest API
	 * - lazy loading
	 *
	 * @since 2.5.0
	 */
	class Related_Posts_By_Taxonomy_Plugin {

		/**
		 * Sets up plugin features.
		 *
		 * @since 2.5.0
		 */
		public function init() {
			Related_Posts_By_Taxonomy_Defaults::init();

			// Initialize plugin features.
			add_action( 'init', array( $this, 'shortcode_init' ) );
			add_action( 'widgets_init', array( $this, 'widget_init' ) );
			add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
			add_action( 'wp_loaded', array( $this, 'cache_init' ) );
			add_action( 'wp_loaded', array( $this, 'debug_init' ) );
			add_action( 'wp_loaded', array( $this, 'lazy_loading_init' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		public function enqueue_scripts() {
			$file = RELATED_POSTS_BY_TAXONOMY_PLUGIN_URL . 'includes/assets/css/styles.css';
			wp_enqueue_style( 'related-posts-by-taxonomy', $file );
		}

		/**
		 * Set up cache feature.
		 *
		 * @since 2.5.0
		 */
		public function cache_init() {
			if ( km_rpbt_plugin_supports( 'cache' ) ) {
				require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/class-cache.php';
				$defaults        = Related_Posts_By_Taxonomy_Defaults::get_instance();
				$defaults->cache = new Related_Posts_By_Taxonomy_Cache();
			}
		}

		/**
		 * Sets up debug feature.
		 *
		 * @since 2.5.0
		 */
		public function debug_init() {
			if ( ! is_admin() && km_rpbt_plugin_supports( 'debug' ) ) {
				require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/class-debug.php';
				$debug = new Related_Posts_By_Taxonomy_Debug();
			}
		}

		/**
		 * Set up the widget feature.
		 *
		 * @since 2.5.0
		 */
		public function widget_init() {
			if ( km_rpbt_plugin_supports( 'widget' ) ) {
				require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/class-widget.php';
				register_widget( 'Related_Posts_By_Taxonomy' );
			}
		}

		/**
		 * Set up the shortcode feature.
		 *
		 * The shortcode file is loaded in the main plugin file.
		 * Other features rely on shortcode functions as well.
		 *
		 * The shortcode returns an empty string if it's is not supported by this plugin.
		 *
		 * @since 2.5.0
		 */
		public function shortcode_init() {
			add_shortcode( 'related_posts_by_tax', 'km_rpbt_related_posts_by_taxonomy_shortcode' );
		}

		/**
		 * Set up the WordPress REST API feature.
		 *
		 * @since 2.5.0
		 */
		public function rest_api_init() {

			// Class exists for WordPress 4.7 and up.
			if ( ! class_exists( 'WP_REST_Controller' ) ) {
				return;
			}

			if ( is_user_logged_in() || km_rpbt_plugin_supports( 'wp_rest_api' ) ) {
				require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/class-rest-api.php';

				$rest_api = new Related_Posts_By_Taxonomy_Rest_API();
				$rest_api->register_routes();
			}
		}

		/**
		 * Set up the lazy loading feature.
		 *
		 * @since 2.6.0
		 */
		public function lazy_loading_init() {
			if (  km_rpbt_plugin_supports( 'lazy_loading' ) ) {
				require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/class-lazy-loading.php';
				$rest_api = new Related_Posts_By_Taxonomy_Lazy_Loading();
			}
		}

	} // end class

} // class exists
