<?php
/**
 * Class to get all the defaults needed for this plugin.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Related_Posts_By_Taxonomy_Plugin' ) ) {
	class Related_Posts_By_Taxonomy_Plugin {

		/**
		 * Sets up class properties on action hook wp_loaded.
		 * wp_loaded is fired after custom post types and taxonomies are registered by themes and plugins.
		 *
		 * @since 0.2.1
		 */
		public function init() {
			Related_Posts_By_Taxonomy_Defaults::init();

			add_action( 'wp_loaded', array( $this, '_setup' ) );
			add_action( 'rest_api_init', array( $this, '_setup_wp_rest_api' ) );
		}

		/**
		 * Sets up class properties.
		 *
		 * @since 0.2.1
		 */
		public function _setup() {

			if ( km_rpbt_plugin_supports( 'cache' ) ) {
				$defaults = Related_Posts_By_Taxonomy_Defaults::get_instance();

				// Only load the cache class when $cache is set to true.
				require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/class-cache.php';
				$defaults->cache = new Related_Posts_By_Taxonomy_Cache();
			}

			if ( km_rpbt_plugin_supports( 'debug' ) && ! is_admin() ) {
				// Only load the debug file when $debug is set to true.
				require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/class-debug.php';
				$debug = new Related_Posts_By_Taxonomy_Debug();
			}
		}

		/**
		 * Sets up the WordPress REST API
		 *
		 * @since 2.3.0
		 *
		 * @return array Array with post type objects.
		 */
		public function _setup_wp_rest_api() {

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
	} // end class

} // class exists
