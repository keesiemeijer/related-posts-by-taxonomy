<?php
/**
 * Cache class.
 *
 * Notice: This file is only loaded if you activate the cache with the filter related_posts_by_taxonomy_cache.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'Related_Posts_By_Taxonomy_Cache' ) ) {
	class Related_Posts_By_Taxonomy_Cache {

		/**
		 * Cache arguments
		 *
		 * @var array
		 */
		public $cache;

		/**
		 * Current post arguments
		 *
		 * @var array
		 */
		private $current;


		public function __construct() {
			$this->setup();
		}

		/**
		 * Setup actions and filters for the cache
		 *
		 * @since 2.1
		 * @return void
		 */
		private function setup() {
			delete_option ( 'rpbt_flushed_cache' );

			$this->cache = $this->get_cache_options();

			// Enable cache for the shortcode and widget.
			add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'add_cache' ) );
			add_filter( 'related_posts_by_taxonomy_widget_args',    array( $this, 'add_cache' ) );

			if ( !$this->cache['flush_manually'] ) {

				// Flush the cache when a post is deleted.
				add_action( 'after_delete_post', array( $this, 'flush_cache' ) );
				add_action( 'trashed_post',      array( $this, 'flush_cache' ) );
				add_action( 'untrashed_post',    array( $this, 'flush_cache' ) );

				// Flush the cache when terms are updated.
				add_action( 'set_object_terms',  array( $this, 'set_object_terms' ), 10, 6 );

				// Flush the cache if a post thumbnail is added, updated or deleted.
				add_action( 'updated_post_meta', array( $this, 'updated_postmeta' ), 10, 4 );
				add_action( "deleted_post_meta", array( $this, 'updated_postmeta' ), 10, 4 );
				add_action( "added_post_meta",   array( $this, 'updated_postmeta' ), 10, 4 );
			}

			// Flush cache by expiration date if set
			if ( $this->cache['expiration'] ) {
				$cache = get_transient( 'rpbt_related_posts_flush_cache' );
				if ( !$cache ) {
					$this->flush_cache();
				}
			}
		}


		/**
		 * Get the cache arguments.
		 *
		 * @since 2.1
		 * @return array Array with cache arguments.
		 */
		private function get_cache_options() {
			return apply_filters( 'related_posts_by_taxonomy_cache_args', array(
					'expiration'     => 0,
					'flush_manually' => false,
				)
			);
		}


		/**
		 * Add 'cache' to the widget and shortcode arguments.
		 *
		 * @since 2.1
		 * @param array   $args Array with widget or shortcode args.
		 * @return array Array with widget or shortcode args.
		 */
		public function add_cache( $args ) {
			$args['cache'] = true;
			return $args;
		}


		/**
		 * Get related posts from cache.
		 *
		 * @since 2.1
		 * @param array   $args Array with widget or shortcode args.
		 * @return array Array Array with related post objects.
		 */
		public function get_related_posts( $args ) {

			// Get cached post ids (if they exist)
			$posts = $this->get_post_meta( $args );

			// $posts = '';
			if ( empty( $posts ) ) {
				// If $posts is an empty array the current post doesn't have any related posts.
				// If $posts is an empty string the related posts are not cached.

				if ( !is_array( $posts ) ) {
					// Posts are not cached yet
					// set_cache() returns an array with related post objects
					$posts = $this->set_cache( $args );
				} else {
					$posts = array();
				}

			} else {
				// Cached post ids are found
				// get_cache() returns an array with related post objects
				$posts = $this->get_cache( $args, $posts );
			}

			return $posts;
		}


		/**
		 * Cache related posts
		 *
		 * @since 2.1.0
		 * @param array   $args Widget or shortcode args
		 * @return array Array with related post objects that are cached.
		 */
		private function set_cache( $args ) {

			$function_args = $this->get_function_args( $args );
			$key           = $this->get_post_meta_key( $args );

			// Add a filter to get the function arguments for the current post
			add_filter( 'related_posts_by_taxonomy', array( $this, 'current_post' ), 99, 4 );

			// Get related posts
			$posts = km_rpbt_related_posts_by_taxonomy( $args['post_id'], $args['taxonomies'], $function_args );

			// Remove the filter
			remove_filter( 'related_posts_by_taxonomy', array( $this, 'current_post' ), 99, 4 );

			// Create the array with cached post ids, and add the related term count.
			$posts_arr = array();
			foreach ( $posts as  $post ) {
				$posts_arr[ $post->ID ] = isset( $post->termcount ) ? $post->termcount : 0;
			}

			// Add the properties used in the related_posts_by_taxonomy filter.
			if ( !empty( $posts_arr ) ) {
				$posts_arr['rpbt_current']['taxonomies'] = $args['taxonomies'];
				$posts_arr['rpbt_current']['post_id']    = $args['post_id'];
				if ( isset( $this->current['related_terms'] ) ) {
					$posts_arr['rpbt_current']['related_terms'] = $this->current['related_terms'];
				}
			}

			// Reset current arguments.
			$this->current = array();

			// Cache the post ids.
			update_post_meta( $args['post_id'], $key, $posts_arr );

			return $posts;
		}


		/**
		 * Get related posts from cache
		 *
		 * @since 2.1.0
		 * @param array   $args  Widget or shortcode arguments.
		 * @param array   $posts Array with cached post ids.
		 * @return array Array with related post objects.
		 */
		private function get_cache( $args, $posts ) {

			if ( empty( $posts ) ) {
				return array();
			}

			$defaults =  array(
				'post_id'       => 0,
				'taxonomies'    => array(),
				'related_terms' => array(),
			);

			// Get the related_posts_by_taxonomy filter properties
			$current = isset( $posts['rpbt_current'] ) ? $posts['rpbt_current'] : array();
			$current = array_merge( $defaults, (array) $current );

			// set the function arguments for the related_posts_by_taxonomy filter
			$function_args                  = $this->get_function_args( $args );
			$function_args['related_terms'] = $current['related_terms'];

			// remove the related_posts_by_taxonomy filter properties
			unset( $posts['rpbt_current'] );

			$post_ids = array_keys( $posts );

			$_args = array(
				'posts_per_page'         => $args['posts_per_page'],
				'post__in'               => $post_ids,
				'orderby'                => 'post__in',
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			);

			// Get related posts.
			$_posts = get_posts( $_args );

			if ( !empty( $_posts ) ) {

				// Add the termcount back to the found posts
				foreach ( $_posts as $key => $post ) {
					if ( isset( $posts[ $post->ID ] ) ) {
						$_posts[ $key ]->termcount = $posts[ $post->ID ];
					}
				}

				// Apply the same filter as the km_rpbt_related_posts_by_taxonomy function
				$posts = $_posts;
			} else {
				$posts = array();
			}

			return apply_filters( 'related_posts_by_taxonomy', $posts, $current['post_id'], $current['taxonomies'], $function_args );
		}


		/**
		 * Returns the related posts post meta.
		 *
		 * @since 2.1
		 * @param array   $args Array with widget or shortcode args.
		 * @return array Array with Related posts ids, empty string, or empty array.
		 */
		public function get_post_meta( $args ) {
			$key = $this->get_post_meta_key( $args );
			return get_post_meta( $args['post_id'], $key, true );
		}


		/**
		 * Create a meta key from args.
		 *
		 * @since 2.1
		 * @param array   $args Array with widget or shortcode args.
		 * @return string Meta key created from args.
		 */
		public function get_post_meta_key( $args ) {
			$key = md5( maybe_serialize ( $args ) );
			return "_rpbt_related_posts:$key";
		}


		/**
		 * Get related post arguments for the current post.
		 *
		 * @since 2.1
		 * @param array   $args Array with widget or shortcode args.
		 * @return array Array with widget or shortcode args.
		 */
		public function current_post( $results, $post_id, $taxonomies, $args ) {
			$this->current = !empty( $args ) ? $args : array();
			return $results;
		}


		/**
		 * Get arguments with restricted arguments removed.
		 *
		 * @since 2.1.0
		 * @param array   $args Array with widget or shortcode args.
		 * @return array Array with function args.
		 */
		public function get_function_args( $args ) {
			unset( $args['post_id'], $args['taxonomies'], $args['fields'] );
			return $args;
		}


		/**
		 * Flush the related posts cache.
		 *
		 * @since 2.1.0
		 * @return void.
		 */
		public function flush_cache() {
			global $wpdb;
			update_option ( 'rpbt_flushed_cache', 'yes' );
			$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_rpbt_related_posts%'" );

			if ( $this->cache['expiration'] ) {
				$this->set_transient();
			}
		}


		/**
		 * Flush the cache if a post thumbnail is added, updated or deleted.
		 * Callback for the post meta actions.
		 *
		 * @since 2.1
		 */
		public function updated_postmeta( $meta_id, $object_id, $meta_key = '', $meta_value = '' ) {
			if ( ( '_thumbnail_id' === $meta_key ) ) {
				$this->flush_cache();
			}
		}


		/**
		 * Flush the cache when terms are updated.
		 * Callback for the set_object_terms action.
		 *
		 * @since 2.1
		 */
		public function set_object_terms( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
			sort( $tt_ids );
			sort( $old_tt_ids );
			if ( $tt_ids != $old_tt_ids ) {
				$this->flush_cache();
			}
		}


		/**
		 * Set the cache expiration transient.
		 *
		 * @since 2.1
		 * @return void.
		 */
		public function set_transient() {
			set_transient( 'rpbt_related_posts_flush_cache', 1, $this->cache['expiration'] );
		}

	} // Class
} // Class exists