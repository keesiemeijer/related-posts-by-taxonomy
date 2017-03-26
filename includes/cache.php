<?php
/**
 * Persistent Cache class.
 *
 * Notice: This file is only loaded if you activate the cache with the filter related_posts_by_taxonomy_cache.
 * Notice: Cache log messages can't be translated.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Related_Posts_By_Taxonomy_Cache' ) ) {
	class Related_Posts_By_Taxonomy_Cache {

		/**
		 * Cache arguments
		 *
		 * @var array
		 */
		public $cache;

		/**
		 * Cache log messages
		 *
		 * @var array
		 */
		public $cache_log = array();

		/**
		 * Current post arguments
		 *
		 * @var array
		 */
		private $current_args;

		/**
		 * Default args to get related posts by
		 *
		 * @var array
		 */
		private $default_args;

		/**
		 * Flush the cache or not (in shutdown hook)
		 *
		 * @var bool
		 */
		public $flush_cache;


		public function __construct() {
			$this->setup();
		}


		/**
		 * Setup actions and filters for the cache
		 *
		 * @since 2.0.1
		 * @return void
		 */
		private function setup() {

			$this->default_args = km_rpbt_get_default_args();
			$this->cache        = $this->get_cache_options();

			// Enable cache for the shortcode and widget.
			add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'add_cache' ) );
			add_filter( 'related_posts_by_taxonomy_widget_args',    array( $this, 'add_cache' ) );

			if ( $this->cache['display_cache_log'] ) {
				add_action( 'admin_bar_menu', array( $this, 'display_cache_log' ), 999 );
			}

			if ( ! $this->cache['flush_manually'] ) {

				// Flush the cache when a post is deleted.
				add_action( 'after_delete_post', array( $this, '_flush_cache' ) );
				add_action( 'trashed_post',      array( $this, '_flush_cache' ) );
				add_action( 'untrashed_post',    array( $this, '_flush_cache' ) );

				// Flush the cache when terms are updated.
				add_action( 'set_object_terms',  array( $this, 'set_object_terms' ), 10, 6 );

				// Flush the cache if a post thumbnail is added, updated or deleted.
				add_action( 'updated_post_meta', array( $this, 'updated_postmeta' ), 10, 4 );
				add_action( 'deleted_post_meta', array( $this, 'updated_postmeta' ), 10, 4 );
				add_action( 'added_post_meta',   array( $this, 'updated_postmeta' ), 10, 4 );

				// Flush cache when PHP is almost shutting down and page is loaded.
				add_action( 'shutdown', array( $this, 'shutdown_flush_cache' ) );
			}

			// Flush cache by expiration date if set.
			if ( $this->cache['expiration'] ) {
				$cache = get_transient( 'rpbt_related_posts_flush_cache' );
				if ( ! $cache ) {
					$this->flush_cache();
				}

				// Flush cache if transient expires.
				add_action( 'delete_transient_rpbt_related_posts_flush_cache', array( $this, 'delete_cache_transient' ) );
			}
		}


		/**
		 * Get the cache arguments.
		 *
		 * @since 2.0.1
		 * @return array Array with cache arguments.
		 */
		private function get_cache_options() {

			$cache_log = (bool) apply_filters( 'related_posts_by_taxonomy_display_cache_log', false );
			return apply_filters( 'related_posts_by_taxonomy_cache_args', array(
					'expiration'        => DAY_IN_SECONDS * 5, // Five days.
					'flush_manually'    => false,
					'display_cache_log' => $cache_log,
				)
			);
		}


		/**
		 * Add 'cache' to the widget and shortcode arguments.
		 *
		 * @since 2.0.1
		 * @param array $args Array with widget or shortcode args.
		 * @return array Array with widget or shortcode args.
		 */
		public function add_cache( $args ) {
			$args['cache'] = true;
			return $args;
		}


		/**
		 * Get related posts from cache.
		 *
		 * @since 2.0.1
		 * @param array $args Array with widget or shortcode args.
		 * @return array  Array with related post objects .
		 */
		public function get_related_posts( $args ) {

			$args = array_merge( $this->default_args, (array) $args );

			// Check if post_id and taxonomies are set.
			if ( ! $this->is_valid_cache_args( $args ) ) {
				$this->cache_log[] = 'Cache failed - invalid cache args';
				return array();
			}

			// Get cached post ids from meta.
			$cache = $this->get_post_meta( $args );

			if ( isset( $cache['ids'] ) ) {

				if ( empty( $cache['ids'] ) ) {
					// Cached, but the current post has no related posts.
					$posts = array();
					$this->cache_log[] = sprintf( 'Post ID %d - cache exists empty', $args['post_id'] );
				} else {
					// Cached related post ids are found!
					$posts = $this->get_cache( $args, $cache );
				}
			} else {
				// Related posts are not cached yet.
				$posts = $this->set_cache( $args );
			}

			return $posts;
		}


		/**
		 * Public function to update the cache
		 *
		 * @since 2.1
		 * @param array $args Array with arguments to get the related posts.
		 * @return array|bool Array with cached related post objects or false if arguments were invalid.
		 */
		public function update_cache( $args ) {
			$args = array_merge( $this->default_args, (array) $args );

			if ( $this->is_valid_cache_args( $args ) ) {
				return $this->set_cache( $args );
			}

			return false;
		}


		/**
		 * Cache related posts
		 *
		 * @since 2.0.1
		 * @param array $args Array with Widget or shortcode arguments.
		 * @return array Array with related post objects that are cached.
		 */
		private function set_cache( $args ) {

			$function_args = $args;
			$key           = $this->get_post_meta_key( $args );
			$cache         = array( 'ids' => array(), 'args' => array() );

			// Restricted function arguments.
			unset( $function_args['taxonomies'], $function_args['post_id'], $function_args['fields'] );

			// Add a filter to get the current arguments with related terms found.
			add_filter( 'related_posts_by_taxonomy', array( $this, 'current_post' ), 99, 4 );

			// Get related posts.
			$posts = km_rpbt_related_posts_by_taxonomy( $args['post_id'], $args['taxonomies'], $function_args );

			// Remove the filter.
			remove_filter( 'related_posts_by_taxonomy', array( $this, 'current_post' ), 99, 4 );

			// Create the array with cached post ids
			// and add the related term count.
			foreach ( $posts as $post ) {
				$cache['ids'][ $post->ID ] = isset( $post->termcount ) ? $post->termcount : 0;
			}

			// Add the properties used in the related_posts_by_taxonomy filter.
			$cache['args']['post_id']    = $args['post_id'];
			$cache['args']['taxonomies'] = $args['taxonomies'];
			if ( isset( $this->current_args['related_terms'] ) ) {
				$cache['args']['related_terms'] = $this->current_args['related_terms'];
			}

			// Reset current arguments.
			$this->current_args = array();

			// Cache the post ids.
			$updated = update_post_meta( $args['post_id'], $key, $cache );

			if ( ! empty( $updated ) ) {
				$this->cache_log[] = sprintf( 'Post ID %d - caching posts...', $args['post_id'] );
			} else {
				$this->cache_log[] = sprintf( 'Post ID %d - failed caching posts', $args['post_id'] );
			}

			return $posts;
		}


		/**
		 * Get related posts from cache
		 *
		 * @since 2.0.1
		 * @param array $args  Array with sanitized widget or shortcode arguments.
		 * @param array $cache Array with cached post ids.
		 * @return array Array with related post objects.
		 */
		private function get_cache( $args, $cache ) {

			if ( ! isset( $cache['ids'] ) ) {
				return array();
			}

			if ( empty( $cache['ids'] ) ) {
				$this->cache_log[] = sprintf( 'Post ID %d - cache exists empty', $args['post_id'] );
				return array();
			}

			$defaults = array(
				'post_id'       => 0,
				'taxonomies'    => array(),
				'related_terms' => array(),
			);

			// Get the related_posts_by_taxonomy filter properties.
			$cache_args = isset( $cache['args'] ) ? $cache['args'] : array();
			$cache_args = array_merge( $defaults, (array) $cache_args );

			// set the function arguments for the related_posts_by_taxonomy filter.
			$function_args                  = $args;
			$function_args['related_terms'] = $cache_args['related_terms'];

			// Restricted arguments.
			unset( $function_args['taxonomies'], $function_args['post_id'] );

			$_args = array(
				'posts_per_page'         => $args['posts_per_page'],
				'post_type'              => $args['post_types'],
				'post__in'               => array_keys( $cache['ids'] ),
				'orderby'                => 'post__in',
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			);

			// Get related posts with get_posts().
			$posts = get_posts( $_args );

			if ( ! empty( $posts ) ) {

				// Add the termcount back to the found posts.
				foreach ( $posts as $key => $post ) {
					if ( isset( $cache['ids'][ $post->ID ] ) ) {
						$posts[ $key ]->termcount = $cache['ids'][ $post->ID ];
					}
				}

				$this->cache_log[] = sprintf( 'Post ID %d - cache exists', $args['post_id'] );
			} else {
				$posts = array();
				$this->cache_log[] = sprintf( 'Post ID %d - cache exists empty', $args['post_id'] );
			}

			$post_id    = $cache_args['post_id'];
			$taxonomies = km_rpbt_get_taxonomies( $cache_args['taxonomies'] );

			// See km_rpbt_related_posts_by_taxonomy filter in includes/functions.php.
			return apply_filters( 'related_posts_by_taxonomy', $posts, $post_id, $taxonomies, $function_args );
		}


		/**
		 * Checks if post id and taxonomies are in arguments.
		 *
		 * @since 2.1.2
		 * @param array $args Widget or Shortcode arguments.
		 * @return boolean True if post_id and taxonomies are set.
		 */
		public function is_valid_cache_args( $args ) {
			if ( isset( $args['post_id'] ) && isset( $args['taxonomies'] ) ) {
				return true;
			}
			return false;
		}


		/**
		 * Sanitizes widget or shortcode arguments.
		 * Removes arguments not needed for the km_rpbt_related_posts_by_taxonomy() function.
		 * Arguments are stored as the cache meta key.
		 *
		 * @since 2.1
		 * @param array $args Array with widget or shortcode argument.
		 * @return array      Array with sanitized widget or shortcode arguments.
		 */
		public function sanitize_cache_args( $args ) {

			$defaults   = $this->default_args;
			$cache_args = wp_parse_args( $args, $defaults );

			// Remove unnecessary args.
			$cache_args = array_intersect_key( $cache_args, $defaults );

			// Add post id and taxonomies back to the cache args.
			$cache_args['post_id']    = isset( $args['post_id'] ) ? $args['post_id'] : 0;
			$cache_args['taxonomies'] = isset( $args['taxonomies'] ) ? $args['taxonomies'] : '';

			return $this->order_cache_args( km_rpbt_sanitize_args( $cache_args ) );
		}


		/**
		 * Returns ordered uniform arguments to store as meta key.
		 *
		 * @since 2.2
		 * @param array $args Arguments.
		 * @return array       Sorted arguments.
		 */
		public function order_cache_args( $args ) {

			foreach ( $args as $key => $value ) {
				if ( is_array( $args[ $key ] ) ) {
					sort( $args[ $key ] );
					$args[ $key ] = implode( ',', $args[ $key ] );
				}
			}

			ksort( $args );

			return $args;
		}


		/**
		 * Returns the related posts post meta.
		 *
		 * @since 2.0.1
		 * @param array $args Array with widget or shortcode arguments.
		 * @return array Array with Related posts ids, empty string, or empty array.
		 */
		public function get_post_meta( $args ) {
			$key = $this->get_post_meta_key( $args );
			return get_post_meta( $args['post_id'], $key, true );
		}


		/**
		 * Create a meta key from args.
		 *
		 * @since 2.0.1
		 * @param array $args Array with widget or shortcode arguments.
		 * @return string Meta key created from sanitized args.
		 */
		public function get_post_meta_key( $args ) {
			$key = md5( maybe_serialize( $this->sanitize_cache_args( $args ) ) );
			return "_rpbt_related_posts:$key";
		}


		/**
		 * Get related post arguments for the current post.
		 *
		 * @since 2.0.1
		 * @param array $results    Related posts. Array with Post objects or post IDs or post titles or post slugs.
		 * @param int   $post_id    Post id used to get the related posts.
		 * @param array $taxonomies Taxonomies used to get the related posts.
		 * @param array $args       Function arguments used to get the related posts.
		 * @return array Array with widget or shortcode args.
		 */
		public function current_post( $results, $post_id, $taxonomies, $args ) {
			$this->current_args = ! empty( $args ) ? $args : array();
			return $results;
		}


		/**
		 * Flush the related posts cache.
		 *
		 * @since 2.0.1
		 * @return int|false. Returns number of deleted rows (0,1,2 etc.) or false on failure.
		 */
		public function flush_cache() {
			global $wpdb;

			if ( $this->cache['expiration'] ) {
				$this->set_transient();
			}

			$flush = $wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_rpbt_related_posts%'" );

			if ( false === $flush ) {
				$this->cache_log[] = 'Failed Flushing cache!';
			} else {
				$this->cache_log[] = 'Flushed cache!';
			}

			return $flush;
		}


		/**
		 * Flush the related posts cache.
		 *
		 * @since 2.1
		 * @return void.
		 */
		public function _flush_cache() {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$this->flush_cache();
			} else {
				$this->flush_cache = true;
			}
		}


		/**
		 * Flush the related posts cache in the shutdown action.
		 *
		 * @since 2.1
		 * @return void.
		 */
		public function shutdown_flush_cache() {
			if ( ! $this->cache['flush_manually'] && ( true === $this->flush_cache ) ) {
				$this->flush_cache();
			}
		}

		/**
		 * Flush the cache if a post thumbnail is added, updated or deleted.
		 * Callback for the post meta actions.
		 *
		 * @since 2.0.1
		 *
		 * @param int    $meta_id    ID of updated metadata entry.
		 * @param int    $object_id  Object ID.
		 * @param string $meta_key   Meta key.
		 * @param mixed  $meta_value Meta value.
		 */
		public function updated_postmeta( $meta_id, $object_id, $meta_key = '', $meta_value = '' ) {
			if ( '_thumbnail_id' === $meta_key ) {
				$this->_flush_cache();
				return;
			}
		}


		/**
		 * Flush the cache when terms are updated.
		 * Callback for the set_object_terms action.
		 *
		 * @since 2.0.1
		 *
		 * @param int    $object_id  Object ID.
		 * @param array  $terms      An array of object terms.
		 * @param array  $tt_ids     An array of term taxonomy IDs.
		 * @param string $taxonomy   Taxonomy slug.
		 * @param bool   $append     Whether to append new terms to the old terms.
		 * @param array  $old_tt_ids Old array of term taxonomy IDs.
		 */
		public function set_object_terms( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
			sort( $tt_ids );
			sort( $old_tt_ids );
			if ( $tt_ids != $old_tt_ids ) {
				$this->_flush_cache();
			}
		}


		/**
		 * Set the cache expiration transient.
		 *
		 * @since 2.0.1
		 * @return void.
		 */
		public function set_transient() {
			set_transient( 'rpbt_related_posts_flush_cache', 1, $this->cache['expiration'] );
		}


		/**
		 * Flushes the cache before the cache transient is deleted.
		 *
		 * @since 2.1
		 * @return void.
		 */
		public function delete_cache_transient() {
			$this->flush_cache();
		}


		/**
		 * Displays cache log in the toolbar.
		 *
		 * @since 2.1
		 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
		 * @return void.
		 */
		public function display_cache_log( $wp_admin_bar ) {

			if ( is_admin() || ! is_super_admin() ) {
				return;
			}

			if ( empty( $this->cache_log ) ) {
				$this->cache_log[] = 'Cache not used';
			}

			array_unshift( $this->cache_log, 'Related Posts Cache' );

			$this->cache_log = $cache_log = array_values( $this->cache_log );
			$notices         = array( 'failed', 'flushed' );

			// Add color to toolbar nodes if needed.
			foreach ( $this->cache_log as $key => $log ) {
				foreach ( $notices as $notice ) {
					if ( false !== strpos( strtolower( $log ), $notice ) ) {
						$this->cache_log[ $key ] = "<span style='color:orange;'>$log</span>";
						break;
					}
				}
			}

			// Add color to top level node if needed.
			if ( $this->cache_log != $cache_log ) {
				$this->cache_log[0] = "<span style='color:orange;'>{$this->cache_log[0]}</span>";
			}

			for ( $i = 0; $i < count( $this->cache_log ); $i++ ) {

				$args = array(
					'id'    => 'related_posts_by_tax-' . $i,
					'title' => $this->cache_log[ $i ],
				);

				if ( $i ) {
					$args['parent'] = 'related_posts_by_tax-0';
				}

				$wp_admin_bar->add_node( $args );
			}
		}

	} // Class.
} // Class exists.
