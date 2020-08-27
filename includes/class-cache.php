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
	/**
	 * Class to manage the persistent cache feature.
	 *
	 * @since 2.0.0
	 */
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

			$this->default_args = km_rpbt_get_query_vars();
			$this->cache        = $this->get_cache_settings();

			// Enable cache for the shortcode and widget.
			add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'add_cache' ), 9 );
			add_filter( 'related_posts_by_taxonomy_widget_args',    array( $this, 'add_cache' ), 9 );

			if ( $this->cache['display_log'] ) {
				// Add link to admin bar.
				add_action( 'admin_bar_menu', array( $this, 'display_cache_log' ), 999 );

				// Display cache results in footer.
				add_action( 'wp_footer', array( $this, 'wp_footer' ), 99 );
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
		 * Get the cache settings.
		 *
		 * @since 2.0.1
		 * @since 2.5.0 Moved logic to km_rpbt_get_default_settings().
		 *
		 * @return array Array with cache arguments.
		 */
		private function get_cache_settings() {
			$settings = array(
				'expiration'     => DAY_IN_SECONDS * 5, // Five days.
				'flush_manually' => false,
				'display_log'    => km_rpbt_plugin_supports( 'display_cache_log' ),
			);

			return apply_filters( 'related_posts_by_taxonomy_cache_args', $settings );
		}

		/**
		 * Add 'cache' to the widget and shortcode arguments.
		 *
		 * @since 2.0.1
		 * @param array $args Array with widget or shortcode args.
		 *                    See km_rpbt_get_related_posts() for more
		 *                    information on accepted arguments.
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
		 *                    See km_rpbt_get_related_posts() for more
		 *                    information on accepted arguments.
		 * @return array  Array with related post objects .
		 */
		public function get_related_posts( $args ) {
			$args = km_rpbt_sanitize_args( $args );

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
					$this->cache_log[] = sprintf( 'Post ID %d - cache exists (no related posts found)', $args['post_id'] );
				} else {
					// Cached and related post ids are found!
					$posts = $this->get_cache( $args, $cache );
				}
			} else {
				// Related posts are not cached yet.
				$posts = $this->set_cache( $args );
			}

			if ( 'ids' === km_rpbt_get_template_fields( $args ) ) {
				// The query was for post IDs
				return $posts;
			}

			$allowed_fields = array(
				'ids'   => 'ID',
				'names' => 'post_title',
				'slugs' => 'post_name',
			);

			if ( $posts && in_array( $args['fields'], array_keys( $allowed_fields ) ) ) {
				/* Get the field used in the query */
				$posts = wp_list_pluck( $posts, $allowed_fields[ $args['fields'] ] );
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
		 *                    See km_rpbt_get_related_posts() for more
		 *                    information on accepted arguments.
		 * @return array Array with related post objects that are cached.
		 */
		private function set_cache( $args ) {
			$query_args = $args;
			$key        = $this->get_post_meta_key( $args );
			$cache      = array( 'ids' => array(), 'args' => array() );

			$query_args['fields'] = km_rpbt_get_template_fields( $query_args );

			// Restricted query arguments.
			unset( $query_args['taxonomies'], $query_args['post_id'] );

			// Add a filter to get the current arguments with related terms found.
			add_filter( 'related_posts_by_taxonomy', array( $this, 'current_post' ), 99, 4 );

			// Get related posts.
			$posts = km_rpbt_query_related_posts( $args['post_id'], $args['taxonomies'], $query_args );

			// Remove the filter.
			remove_filter( 'related_posts_by_taxonomy', array( $this, 'current_post' ), 99, 4 );


			// Create the array with cached post ids
			// and add the related term count.
			foreach ( $posts as $post ) {
				$post_id = isset( $post->ID ) ? $post->ID : $post;
				$cache['ids'][ $post_id ] = isset( $post->termcount ) ? $post->termcount : 0;
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
		 *                     See km_rpbt_get_related_posts() for more
		 *                     information on accepted arguments.
		 * @param array $cache Array with cached post ids.
		 * @return array Array with related post objects.
		 */
		private function get_cache( $args, $cache ) {
			if ( ! isset( $cache['ids'] ) ) {
				return array();
			}

			if ( empty( $cache['ids'] ) ) {
				$this->cache_log[] = sprintf( 'Post ID %d - cache exists (no related posts found)', $args['post_id'] );
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

			// set the query arguments for the related_posts_by_taxonomy filter.
			$query_args                  = $args;
			$query_args['related_terms'] = $cache_args['related_terms'];
			$query_args['termcount']     = array();

			/** This filter is documented in includes/query.php */
			$related_posts = apply_filters( 'related_posts_by_taxonomy_pre_related_posts', null, $query_args );
			if ( is_array( $related_posts ) ) {
				return $related_posts;
			}

			// Restricted query arguments.
			unset( $query_args['taxonomies'], $query_args['post_id'] );

			$wp_query_args = array(
				'posts_per_page'         => $args['posts_per_page'],
				'post_type'              => $args['post_types'],
				'post__in'               => array_keys( $cache['ids'] ),
				'orderby'                => 'post__in',
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			);

			$fields = km_rpbt_get_template_fields( $args );
			if ( 'ids' === $fields ) {
				$wp_query_args['fields'] = 'ids';
			}

			// Get related posts with get_posts().
			$posts = get_posts( $wp_query_args );

			if ( ! empty( $posts ) ) {
				if ( '' === $fields ) {
					// Add defaults back to the found posts.
					foreach ( $posts as $key => $post ) {
						if ( isset( $post->ID ) && isset( $cache['ids'][ $post->ID ] ) ) {
							$posts[ $key ]->termcount  = $cache['ids'][ $post->ID ];
							$query_args['termcount'][] = $cache['ids'][ $post->ID ];
						}

						$posts[ $key ]->rpbt_current    = $args['post_id'];
						$posts[ $key ]->rpbt_post_class = '';
						$posts[ $key ]->rpbt_type       = isset( $args['type'] ) ? $args['type'] : '';
					}
				}

				$this->cache_log[] = sprintf( 'Post ID %d - cache exists', $args['post_id'] );
			} else {
				$posts = array();
				$this->cache_log[] = sprintf( 'Post ID %d - cache exists (no related posts found)', $args['post_id'] );
			}

			$post_id    = $cache_args['post_id'];
			$taxonomies = km_rpbt_get_taxonomies( $cache_args['taxonomies'] );

			/** This filter is documented in includes/query.php */
			return apply_filters( 'related_posts_by_taxonomy', $posts, $post_id, $taxonomies, $query_args );
		}

		/**
		 * Checks if post id and taxonomies are in arguments.
		 *
		 * @since 2.1.2
		 * @param array $args Widget or Shortcode arguments.
		 *                    See km_rpbt_get_related_posts() for more
		 *                    information on accepted arguments.
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
		 * Removes arguments not needed for the km_rpbt_query_related_posts() function.
		 * Arguments are stored as the cache meta key.
		 *
		 * @since 2.1
		 * @param array $args Array with widget or shortcode argument.
		 *                    See km_rpbt_get_related_posts() for more
		 *                    information on accepted arguments.
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
		 * @return array      Sorted arguments.
		 */
		public function order_cache_args( $args ) {
			$args = km_rpbt_nested_array_sort( $args );
			foreach ( $args as $key => $value ) {
				if ( is_array( $args[ $key ] ) ) {
					// Convert to string to keep cache value small.
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
		 *                    See km_rpbt_get_related_posts() for more
		 *                    information on accepted arguments.
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
		 *                    See km_rpbt_get_related_posts() for more
		 *                    information on accepted arguments.
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
		 * @param array $args       Query arguments used to get the related posts.
		 *                          See km_rpbt_get_related_posts() for more
		 *                          information on accepted arguments.
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
		 * Adds link to cache log in the toolbar.
		 *
		 * @since 2.1
		 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
		 * @return void.
		 */
		public function display_cache_log( $wp_admin_bar ) {
			if ( is_admin() || ! is_super_admin() ) {
				return;
			}

			$args = array(
				'id'    => 'related_posts_by_tax-0' ,
				'title' => 'Related Posts Cache',
				'href' => "#rpbt-cache-results",
			);

			$wp_admin_bar->add_node( $args );
		}

		/**
		 * Displays cache results in the footer.
		 *
		 * @since  2.7.4
		 *
		 * @return void
		 */
		function wp_footer() {
			if ( is_admin() || ! is_super_admin() ) {
				return;
			}

			$style = 'border:0 none;outline:0 none;padding:20px;margin:0;';
			$style .= 'color: #333;background: #f5f5f5;font-family: monospace;font-size: 16px;font-style: normal;';
			$style .= 'font-weight: normal;line-height: 1.5;white-space: pre;overflow:auto;';
			$style .= 'width:100%;display:block;float:none;clear:both;text-align:left;z-index: 999;position:relative;';

			if ( empty( $this->cache_log ) ) {
				$message = 'This page has no related posts';
				if ( km_rpbt_plugin_supports( 'lazy_loading' ) ) {
					$message = "Disable the lazy_loading feature to see the cache log";
				}
				$this->cache_log[] = $message;
			}


			echo "<pre id='rpbt-cache-results' style='{$style}'>";
			echo "Related Posts by Taxonomy Cache Results \n";
			echo str_repeat( "-", 39 ) . "\n";

			foreach ( $this->cache_log as $value ) {
				echo "{$value}\n";
			}

			echo '</pre>';
		}

	} // Class.
} // Class exists.
