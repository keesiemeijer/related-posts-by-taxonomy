<?php
/**
 * Class to get all the defaults needed for this plugin.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Related_Posts_By_Taxonomy_Defaults' ) ) {
	class Related_Posts_By_Taxonomy_Defaults {

		/**
		 * Post types.
		 *
		 * @since 0.2.1
		 * @var array
		 */
		public $post_types;

		/**
		 * Taxonomies.
		 *
		 * @since 0.2.1
		 * @var array
		 */
		public $taxonomies;

		/**
		 * Slug for "all" taxonomies.
		 *
		 * @since 0.2.1
		 * @var string
		 */
		public $all_tax;

		/**
		 * Default taxonomy (category).
		 *
		 * @since 0.2.1
		 * @var string
		 */
		public $default_tax;

		/**
		 * Formats.
		 *
		 * @since 0.2.1
		 * @var array
		 */
		public $formats;

		/**
		 * Image sizes.
		 *
		 * @since 0.2.1
		 * @var array
		 */
		public $image_sizes;

		/**
		 * Cache Class instance.
		 *
		 * @since 0.2.1
		 * @see get_instance()
		 * @var object
		 */
		public $cache = null;

		/**
		 * Class instance.
		 *
		 * @access private
		 *
		 * @since 0.2.1
		 * @see get_instance()
		 * @var object
		 */
		private static $instance = null;

		/**
		 * Acces this plugin's working instance.
		 *
		 * @since 0.2.1
		 *
		 * @return object
		 */
		public static function get_instance() {
			// create a new object if it doesn't exist.
			is_null( self::$instance ) && self::$instance = new self;
			return self::$instance;
		}

		/**
		 * Sets up class properties on action hook wp_loaded.
		 * wp_loaded is fired after custom post types and taxonomies are registered by themes and plugins.
		 *
		 * @since 0.2.1
		 */
		public static function init() {
			add_action( 'wp_loaded', array( self::get_instance(), '_setup' ) );
			add_action( 'rest_api_init', array( self::get_instance(), '_setup_wp_rest_api' ) );
		}

		/**
		 * Sets up class properties.
		 *
		 * @since 0.2.1
		 */
		public function _setup() {

			// Default taxonomies.
			$this->all_tax = 'all'; // All taxonomies.
			$this->default_tax = array( 'category' => __( 'Category', 'related-posts-by-taxonomy' ) );

			$this->post_types = $this->get_post_types();
			if ( empty( $this->post_types ) ) {
				$this->post_types = array( 'post' => __( 'Post', 'related-posts-by-taxonomy' ) );
			}

			$this->taxonomies = $this->get_taxonomies();
			if ( empty( $this->taxonomies ) ) {
				$this->taxonomies = $this->default_tax;
			}

			$this->image_sizes = $this->get_image_sizes();
			if ( empty( $this->image_sizes ) ) {
				$this->image_sizes = array( 'thumbnail' => __( 'Thumbnail', 'related-posts-by-taxonomy' ) );
			}

			$this->formats = $this->get_formats();

			if ( $this->plugin_supports( 'cache' ) ) {
				// Only load the cache class when $cache is set to true.
				require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/cache.php';
				$this->cache = new Related_Posts_By_Taxonomy_Cache();
			}

			if ( $this->plugin_supports( 'debug' ) && ! is_admin() ) {
				// Only load the debug file when $debug is set to true.
				require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/debug.php';
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

			if ( $this->plugin_supports( 'wp_rest_api' ) ) {
				require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/wp-rest-api.php';

				$rest_api = new Related_Posts_By_Taxonomy_Rest_API();
				$rest_api->register_routes();
			}
		}

		/**
		 * Adds opt in support with a filter for cache, WP REST API and debug.
		 *
		 * @since  2.3.0
		 *
		 * @param string $type Type of support ('cache', 'wp_rest_api', 'debug').
		 * @return bool True if set to true with a filter. Default false.
		 */
		public function plugin_supports( $type = '' ) {
			if ( ! in_array( $type , array( 'cache', 'wp_rest_api', 'debug' ) ) ) {
				return false;
			}

			/**
			 * Filter whether to support cache, wp_rest_api or debug.
			 *
			 * The dynamic portion of the hook name, `$type`, refers to the
			 * type type of support ('cache', 'wp_rest_api', 'debug').
			 *
			 * @param bool $bool Add support if true. Default false
			 */
			return apply_filters( "related_posts_by_taxonomy_{$type}", false );
		}

		/**
		 * Returns all public post types.
		 *
		 * @since 0.2.1
		 *
		 * @return array Array with post type objects.
		 */
		public function get_post_types() {
			$post_types = array();
			$post_types_obj = get_post_types( array( 'public' => true, '_builtin' => false ), 'objects', 'and' );

			$post_types_obj = array( 'post' => get_post_type_object( 'post' ) ) + $post_types_obj;

			foreach ( (array) $post_types_obj as $key => $value ) {
				$post_types[ $key ] = esc_attr( $value->labels->menu_name );
			}
			return $post_types;
		}

		/**
		 * Returns all public taxonomies
		 * Sets the id for 'All Taxonomies'
		 * Sets the default taxonomy
		 *
		 * @since 0.2.1
		 *
		 * @return array Array with taxonomy names and labels.
		 */
		public function get_taxonomies() {
			$tax = array();
			$taxonomies = get_taxonomies( array( 'public' => true ), 'objects', 'and' );

			$i = 0;
			foreach ( (array) $taxonomies as $key => $value ) {

				$tax[ $key ] = esc_attr( $value->labels->menu_name );

				// Set first taxonomy as the default taxonomy.
				if ( ! $i++ ) {
					$this->default_tax = array( $key => esc_attr( $value->labels->menu_name ) );
				}
			}

			// If 'all' is a registered taxonomy change the all_tax value (slug: all-2).
			if ( ! empty( $tax ) ) {
				if ( in_array( $this->all_tax, array_keys( $tax ) ) ) {
					$num = 2;
					do {
						$alt_slug = $this->all_tax . "-$num";
						$num++;
						$slug_check = in_array( $alt_slug, $tax );
					} while ( $slug_check );
					$this->all_tax = $alt_slug;
				}
			}

			return array_unique( $tax );
		}

		/**
		 * Returns all image sizes.
		 *
		 * @since 0.2.1
		 *
		 * @global array $_wp_additional_image_sizes
		 * @return array Array with all image sized.
		 */
		public function get_image_sizes() {

			global $_wp_additional_image_sizes;
			$sizes = array();
			$image_sizes = get_intermediate_image_sizes();

			foreach ( $image_sizes as $s ) {

				$width = $height = false;
				if ( isset( $_wp_additional_image_sizes[ $s ] ) ) {
					$width  = intval( $_wp_additional_image_sizes[ $s ]['width'] );
					$height = intval( $_wp_additional_image_sizes[ $s ]['height'] );
				} else {
					$width  = get_option( $s . '_size_w' );
					$height = get_option( $s . '_size_h' );
				}

				if ( $width && $height ) {
					$size = sanitize_title( $s );
					$size = ucwords( str_replace( array( '-', '_' ), ' ', $s ) );
					$sizes[ $s ] = $size . ' (' . $width . ' x ' . $height . ')';
				}
			}

			return $sizes;
		}

		/**
		 * Returns all formats.
		 *
		 * @since 0.2.1
		 *
		 * @return array Formats.
		 */
		public function get_formats() {
			$formats = array(
				'links'      => __( 'Links', 'related-posts-by-taxonomy' ),
				'posts'      => __( 'Posts', 'related-posts-by-taxonomy' ),
				'excerpts'   => __( 'Excerpts', 'related-posts-by-taxonomy' ),
				'thumbnails' => __( 'Post thumbnails', 'related-posts-by-taxonomy' ),
			);
			return $formats;
		}

		/**
		 * Returns default settings for the shortcode and widget.
		 *
		 * @since 2.2.2
		 * @param tring $type Type of settings. Choose from 'widget', 'shortcode', 'wp_rest_api' or 'all'.
		 * @return string ype of settings. Values can be 'shortcode' or 'widget'
		 */
		public function get_default_settings( $type = '' ) {

			// Settings for the km_rpbt_related_posts_by_taxonomy() function.
			$defaults = km_rpbt_get_default_args();
			$types    = array(
				'shortcode',
				'widget',
				'wp_rest_api',
			);

			$_type = $type;

			// wp_rest_api settings are the same as a shortcode.
			$type  = ( 'wp_rest_api' === $type ) ? 'shortcode' : $type;

			// Common settings for the widget and shortcode.
			$settings = array(
				'post_id'        => '',
				'taxonomies'     => 'all',
				'title'          => __( 'Related Posts', 'related-posts-by-taxonomy' ),
				'format'         => 'links',
				'image_size'     => 'thumbnail',
				'columns'        => 3,
				'link_caption'   => false,
				'caption'        => 'post_title',
			);

			$settings = array_merge( $defaults, $settings );

			// No default setting for post types.
			$settings['post_types'] = '';

			if ( ! in_array( $type, $types ) ) {
				return $settings;
			}

			// Custom settings for the widget.
			if ( ( 'widget' === $type ) ) {
				$settings['random']            = false;
				$settings['singular_template'] = false;
			}

			// Custom settings for the shortcode.
			if ( ( 'shortcode' === $type ) ) {
				$shortcode_args = array(
					'before_shortcode' => '<div class="rpbt_shortcode">',
					'after_shortcode'  => '</div>',
					'before_title'     => '<h3>',
					'after_title'      => '</h3>',
				);

				$settings = array_merge( $settings, $shortcode_args );
			}

			// Custom settings for the WP rest API.
			if ( 'wp_rest_api' === $_type ) {
				$settings['before_shortcode'] = '<div class="rpbt_wp_rest_api">';
				$settings['after_shortcode']  = '</div>';
			}

			$settings['type'] = $_type;

			return $settings;
		}

	} // end class

} // class exists
