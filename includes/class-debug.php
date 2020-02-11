<?php
/**
 * Class to display debug information for admins.
 *
 * Notice: This file is only loaded if you activate debugging with the filter related_posts_by_taxonomy_debug.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Related_Posts_By_Taxonomy_Debug' ) ) {
	/**
	 * Class to debug this plugin.
	 *
	 * Adds links for debugging shortcodes and widget.
	 * Displays debug information (to admins) in the footer of a website.
	 *
	 * @since 2.0.0
	 */
	class Related_Posts_By_Taxonomy_Debug {

		public $debug             = array();
		public $results           = array();
		public $post_types        = array();
		public $taxonomies        = array();
		public $plugin            = 0;
		public $widget_counter    = 0;
		public $shortcode_counter = 0;

		function __construct() {
			$this->debug_setup();
		}

		/**
		 * Calls plugin filters for debugging.
		 *
		 * @since 2.0.0
		 * @return void
		 */
		function debug_setup() {
			/*
			 * Check if the current user can view debug results.
			 * True if the current user is an admin or super admin.
			 * OR the current user has the 'view_rpbt_debug_results' capability.
			 */

			if ( ! ( is_super_admin() || current_user_can( 'view_rpbt_debug_results' ) ) ) {
				return;
			}

			$this->debug['cache'] = 'none';

			// Get all post types when widget or shortcode is called.
			$post_types = array_keys( get_post_types() );
			$this->post_types = array_merge( $this->post_types, $post_types );
			$this->post_types = array_unique( $this->post_types );

			// Get all taxonomies when widget or shortcode is called.
			$taxonomies = array_keys( get_taxonomies() );
			$this->taxonomies = array_merge( $this->taxonomies, $taxonomies );
			$this->taxonomies = array_unique( $this->taxonomies );

			$this->plugin  = km_rpbt_plugin();
			$this->cache   = km_rpbt_is_cache_loaded();

			// Display debug results in footer.
			add_action( 'wp_footer', array( $this, 'wp_footer' ), 99 );

			// Add debug link before the widget title.
			add_filter( 'dynamic_sidebar_params', array( $this, 'widget_params' ), 99 );

			// Get widget and shortcode args.
			add_filter( 'related_posts_by_taxonomy_widget_args',    array( $this, 'debug_start' ), 99, 2 );
			add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'debug_start' ), 99, 2 );

			// Bail, the page has already loaded when using lazy loading.
			if ( km_rpbt_plugin_supports( 'lazy_loading' ) ) {
				return;
			}

			// Show widget and shortcode even if no posts were found.
			add_filter( 'related_posts_by_taxonomy_widget_hide_empty',    array( $this, 'hide_empty' ), 99 );
			add_filter( 'related_posts_by_taxonomy_shortcode_hide_empty', array( $this, 'hide_empty' ), 99 );

			// Get current post terms, taxonomies and post ID.
			add_filter( 'related_posts_by_taxonomy_pre_related_posts', array( $this, 'pre_related_posts' ), 99, 2 );

			// Get query and related terms.
			add_filter( 'related_posts_by_taxonomy_posts_clauses', array( $this, 'posts_clauses' ), 99, 4 );

			// Get Related posts
			add_filter( 'related_posts_by_taxonomy', array( $this, 'posts_found' ) );

			// Get the requested template.
			add_filter( 'related_posts_by_taxonomy_template', array( $this, 'get_template' ), 99, 2 );

			// Store the results.
			add_action( 'related_posts_by_taxonomy_after_display', array( $this, 'after_display' ) );
		}

		/**
		 * Starts debugging at the arguments hook for widget and shortcode.
		 * Adds debug link to shortcode
		 *
		 * @since 2.0.0
		 *
		 * @param array $args   Widget or shortcode arguments.
		 * @param array $widget Widget arguments.
		 * @return array Array with widget or shortcode arguments.
		 */
		function debug_start( $args, $widget = '' ) {

			/* First filter called for debugging. */

			// Force a title if empty.
			$args['title'] = empty( $args['title'] ) ? 'Related Posts Debug Title' : $args['title'];

			if ( 'related_posts_by_taxonomy_widget_args' === current_filter() ) {
				$this->debug['type']        = 'widget';
				$this->debug['widget arguments'] = $args;
				$this->debug['widget']      = $widget;

			} else {
				$this->debug['type']           = 'shortcode';
				$this->debug['shortcode args'] = $args;
				$args['before_shortcode']      = '<div class="rpbt_shortcode">' . $this->debug_link( 'shortcode' ) . '<br/>';
				$args['after_shortcode']       = '</div>';
			}

			return $args;
		}

		/**
		 * Check if related posts are cached.
		 *
		 * @since 2.1
		 * @param array $args Array with widget or shortcode arguments.
		 * @return void
		 */
		function check_cache( $args ) {

			// Get cached post ids (if they exist).
			$cache = $this->plugin->cache->get_post_meta( $args );

			if ( isset( $cache['ids'] ) ) {

				// Related posts were cached for this post.
				$cache_args                       = $cache['args'];
				$post_ids                         = array_keys( (array) $cache['ids'] );
				$this->debug['cache']             = 'current post cached';
				$this->debug['cached post ids']   = ! empty( $post_ids ) ? implode( ', ', $post_ids ) : '';
				$defaults                         = km_rpbt_get_query_vars();
				$this->debug['function args']     = array_intersect_key( $args , $defaults );
				$taxonomies                       = km_rpbt_get_taxonomies( $cache_args['taxonomies'] );
				$this->debug['cached taxonomies'] = implode( ', ', $taxonomies );
				$this->debug['current post id']   = isset( $args['post_id'] ) ? $args['post_id'] : '';
				if ( isset( $cache_args['related_terms'] ) ) {
					$this->debug['cached terms'] = $this->get_terms_names( $cache_args['related_terms'] );
				} else {
					// not in cache for post that doesn't have related posts???
					$this->debug['cached terms'] = 'Could not find cached terms';
				}
			} else {

				// Related posts not yet in cache.
				$this->debug['cache'] = 'current post is not yet cached';
			}
		}

		/**
		 * Adds debug link before widget title.
		 *
		 * @since 2.0.0
		 * @param array $params Array with widget parameters.
		 * @return array Array with widget parameters.
		 */
		function widget_params( $params ) {
			if ( ! isset( $params[0]['widget_id'] ) ) {
				return $params;
			}

			if ( 0 !== strpos( $params[0]['widget_id'], 'related-posts-by-taxonomy' ) ) {
				return $params;
			}

			$params[0]['before_widget'] = $params[0]['before_widget'] . $this->debug_link() . '<br/>';

			return $params;
		}

		/**
		 * Creates a debug link.
		 *
		 * @since 2.0.0
		 * @param string $type Type shortcode or widget.
		 * @return string Link to debug information,
		 */
		function debug_link( $type = 'widget' ) {
			$counter = ( 'widget' === $type ) ? ++$this->widget_counter : ++$this->shortcode_counter;

			if ( km_rpbt_plugin_supports( 'lazy_loading' ) ) {
				$this->debug['debug_id'] = 'rpbt-debug-notice';
			} else {
				$this->debug['debug_id'] = 'rpbt-' . $type . '-debug-' . $counter;
			}
			$this->debug['debug_link'] = '(<a href="#' . $this->debug['debug_id'] . '">Debug ' . ucfirst( $type ) . '</a>)';

			return $this->debug['debug_link'];
		}

		/**
		 * Gets debug information
		 * Callback function for filter related_posts_by_taxonomy_pre_related_posts.
		 *
		 * @since 2.6.0
		 */
		function pre_related_posts( $posts, $args ) {
			$post_id    = $args['post_id'];
			$taxonomies = $args['taxonomies'];

			$terms = get_terms( array( 'fields' => 'names', 'object_ids' => array( $post_id ) ) );
			$terms = ! is_wp_error( $terms ) ? $terms : array();


			$back_compat   = is_bool( $args['related'] );
			$include_terms = ( $args['include_terms'] || $args['terms'] );
			if ( $back_compat && $include_terms && ! $args['related'] ) {
				$taxonomies = 'No taxonomies used in query (related - false)';
			} elseif ( ! $back_compat && $include_terms ) {
				$taxonomies = 'No taxonomies used in query (related - null)';
			}

			$taxonomies = is_array( $taxonomies ) ? implode( ', ', $taxonomies ) : $taxonomies;
			$term_names = $this->get_terms_names( $args['related_terms'] );

			$this->debug['terms used for query']         = $term_names;
			$this->debug['current post id']              = $post_id;
			$this->debug['taxonomies used for query']    = $taxonomies;
			$this->debug['terms found for current post'] = implode( ', ', $terms );

			if ( $this->cache ) {
				$this->check_cache( $args );
			}

			return $posts;
		}

		/**
		 * Shows widget or shortcode even if no related posts were found.
		 * Removes filter wp_get_object_terms.
		 *
		 * @since 2.0.0
		 * @return false.
		 */
		function hide_empty() {
			// Always show the widget or shortcode.
			return false;
		}

		/**
		 * Gets query and function args from km_rpbt_query_related_posts().
		 * adds filter to related_posts_by_taxonomy.
		 *
		 * @since 2.0.0
		 * @return array Array with sql clauses.
		 */
		function posts_clauses( $pieces, $post_id, $taxonomies, $args ) {
			global  $wpdb;

			extract( $pieces );

			if ( ! empty( $group_by_sql ) ) {
				$group_by_sql = 'GROUP BY ' . $group_by_sql;
			}

			if ( ! empty( $order_by_sql ) ) {
				$order_by_sql = 'ORDER BY ' . $order_by_sql;
			}

			$query = "SELECT {$select_sql} FROM $wpdb->posts {$join_sql} {$where_sql} {$group_by_sql} {$order_by_sql} {$limit_sql}";

			// Remove prefix
			$query = str_replace( $wpdb->prefix , '' , $query );

			// Format query
			$query = preg_replace( "/ INNER JOIN /", " \nINNER JOIN ", $query );
			$query = preg_replace( "/ WHERE /", " \nWHERE ", $query );
			$query = preg_replace( "/ AND /", " \nAND ", $query );

			unset( $args['related_terms'] );

			$defaults = km_rpbt_get_query_vars();
			$this->debug['function args'] = array_intersect_key( $args , $defaults );
			$this->debug['related posts query'] = $query;

			return $pieces;
		}

		/**
		 * Gets the post ids if related posts where found.
		 *
		 * @since 2.0.0
		 * @param array $results Array with post objects.
		 * @return array Array with with post objects.
		 */
		function posts_found( $results ) {
			if ( ! is_array( $results ) ) {
				return $this->debug['related post ids found']['error'] = $results;
			}

			if ( ! empty( $results ) ) {
				if ( isset( $results[0]->ID ) ) {
					// Assume array with post objects.
					$post_ids = wp_list_pluck( $results, 'ID' );
					$this->debug['related post ids found'] = implode( ', ', $post_ids );
				} else {
					// Assume array with ids, names or slugs.
					$this->debug['related post ids found'] = implode( ', ', $results );
				}
			} else {
				// Only reached if terms were found for the query
				$this->debug['related post ids found'] = 'no related post ids found';
			}

			return $results;
		}

		/**
		 * Gets the requested template.
		 *
		 * @since 2.0.0
		 * @param string $template Template.
		 * @return string Template.
		 */
		function get_template( $template, $type ) {
			$this->debug['requested template'] = $template;
			return $template;
		}

		/**
		 * Stores the result after display
		 *
		 * @since 2.1.1
		 */
		function after_display() {
			$this->results[] = $this->debug;

			// Reset debug array.
			// This is the last debug action.
			$this->debug = array();
		}

		/**
		 * Get the term names from ids.
		 *
		 * @since 2.4.0
		 *
		 * @param array $term_ids Array with term ids.
		 * @return string Term_names.
		 */
		function get_terms_names( $term_ids ) {
			if ( ! $term_ids ) {
				return array();
			}

			$args = array(
				'include' => $term_ids,
				'fields'  => 'names',
			);

			$term_names = get_terms( $args );

			if ( is_wp_error( $term_names ) || empty( $term_names ) ) {
				return 'No terms found';
			}

			return implode( ', ', $term_names );
		}

		/**
		 * Returns a fancy header for the debug information.
		 *
		 * @since 2.3.1
		 *
		 * @param string $type Type of debug.
		 * @return string Fancy header.
		 */
		function get_header( $type = '' ) {
			static $shortcode = 0;
			static $widget = 0;

			$type_count =  '';
			$debug_type =  '';
			if ( 'shortcode' === $type ) {
				$type_count = ' ' . ++$shortcode;
				$debug_type = 'Debug: ';
			} elseif ( 'widget' === $type ) {
				$type_count = ' ' . ++$widget;
				$debug_type = 'Debug: ';
			}

			//$type  = ( 'Supports' !== $type ) ? 'Debug: ' . $type : $type;
			$title = 'Related Posts by Taxonomy ' . $debug_type . $type . $type_count;
			$title = '**    ' . $title . '    **';
			$length = strlen( $title );
			$rows = str_repeat( "*", $length ) . "\n";
			$rows .= '**' . str_repeat( " ", $length - 4 ) . "**\n";
			$rows .= $title . "\n";
			$rows .= '**' . str_repeat( " ", $length - 4 ) . "**\n";
			$rows .= str_repeat( "*", $length ) . "\n";
			return $rows;
		}

		/**
		 * Returns supported plugin features
		 *
		 * @since 2.3.1
		 *
		 * @return array Array with supported plugin features.
		 */
		function get_supports() {
			// Remove filters before calling km_rpbt_get_plugin_supports().
			remove_filter( 'related_posts_by_taxonomy_widget_hide_empty',    array( $this, 'hide_empty' ), 99 );
			remove_filter( 'related_posts_by_taxonomy_shortcode_hide_empty', array( $this, 'hide_empty' ), 99 );

			$supports = km_rpbt_get_plugin_supports();
			foreach ( $supports as $key => $support ) {
				$supports[ $key ] = km_rpbt_plugin_supports( $key );
			}

			return $supports;
		}

		/**
		 * Get inline style for HTML pre tag.
		 *
		 * @since 2.7.3
		 *
		 * @return string Inline styles.
		 */
		function get_style() {
			$style = 'border:0 none;outline:0 none;padding:20px;margin:0;';
			$style .= 'color: #333;background: #f5f5f5;font-family: monospace;font-size: 16px;font-style: normal;font-weight: normal;line-height: 1.5;white-space: pre;overflow:auto;';
			$style .= 'width:100%;display:block;float:none;clear:both;text-align:left;z-index: 999;position:relative;';
			return $style;
		}

		/**
		 * Get formatted debug section HTML.
		 *
		 * @since 2.7.3
		 *
		 * @param string|array $value Debug section value.
		 * @return string Formatted debug section HTML.
		 */
		function get_section_html( $value ) {
			$section = '';
			if ( is_array( $value ) ) {
				$style = $this->get_style();
				// create string from array
				$value = var_export( $value, true );
				// clean up arrays
				$value = preg_replace( "/\=>\s*array\s*\(/", '=> array(', $value );
				$value = preg_replace( "/array\s*\(\s*\),/", 'array(),', $value );
				// convert spaces to tabs
				$value = preg_replace( "/(?<![^\s]{2})  /", "\t", $value );
				$section  = "<pre style='{$style}'>" . htmlspecialchars( $value ) . '</pre>';
			} else {
				$section = "{$value}\n";
			}

			return "{$section}\n";
		}

		/**
		 * Displays the results in the footer
		 */
		function wp_footer() {
			$seperator = str_repeat( "-", 43 ) . "\n";

			$order = array(
				'type', 'cache', 'current post id', 'terms found for current post',
				'taxonomies used for query', 'cached taxonomies',
				'terms used for query', 'cached terms',
				'related post ids found', 'cached post ids',
				'widget arguments', 'shortcode args', 'function args',
				'related posts query',
				'requested template', 'widget'
			);
			$order = array_fill_keys( $order , '' );
			$style = $this->get_style();

			echo "<pre style='{$style}'>" . $this->get_header( 'General Debug Information' ) . "\n\n";
			echo "Plugin Supports \n\n";
			echo $this->get_section_html( $this->get_supports() );
			echo $seperator;
			echo "All post types found (public and private)\n\n";
			$post_types = implode( ', ', $this->post_types );
			echo $post_types  . "\n";
			echo $seperator;
			echo "All taxonomies found (public and private)\n\n";
			$taxonomies = implode( ', ', $this->taxonomies );
			echo $taxonomies;
			echo "</pre>";

			if ( ! empty( $this->results ) ) {
				echo '<p>';
				foreach ( (array) $this->results as $debug_arr ) {

					$id = '';
					if ( isset( $debug_arr['debug_id'] ) ) {
						$id = ' id="' . $debug_arr['debug_id'] . '"';
					}

					echo "<pre{$id} style='{$style}'>" .  $this->get_header( $debug_arr['type'] ) . "\n\n";

					unset( $debug_arr['debug_id'], $debug_arr['debug_link'] );

					$_order = $order;

					if ( 'widget' === $debug_arr['type'] ) {
						unset( $_order['shortcode args'] );
					}

					if ( 'shortcode' === $debug_arr['type'] ) {
						unset( $_order['widget arguments'], $_order['widget'] );
					}

					// reorder debug array.
					$debug_arr = array_merge( $_order, $debug_arr );

					unset( $debug_arr['type'] );

					if ( $this->cache ) {
						if ( $debug_arr['cache'] === 'current post is not yet cached' ) {
							unset( $debug_arr['cached taxonomies'] );
							unset( $debug_arr['cached terms'] );
							unset( $debug_arr['cached post ids'] );
						} else {
							unset( $debug_arr['taxonomies used for query'] );
							unset( $debug_arr['terms used for query'] );
							unset( $debug_arr['related post ids found'] );
							unset( $debug_arr['related posts query'] );
						}
					} else {
						unset( $debug_arr['cache'] );
						unset( $debug_arr['cached post ids'] );
						unset( $debug_arr['cached terms'] );
						unset( $debug_arr['cached taxonomies'] );
					}

					foreach ( $debug_arr as $key => $value ) {
						$title = $key;
						if ( 'function args' === $title ) {
							$title = 'Related posts query arguments';
						}

						echo $title . ":\n\n";
						echo $this->get_section_html( $value );
						echo $seperator;
					}
					echo '</pre>';
				}
				echo '<p>';
			} else {
				$message = '<p><pre id="rpbt-debug-empty">' . $this->get_header() . "\n\nNo widget or shortcode found to debug on this page</pre></p>";
				if ( km_rpbt_plugin_supports( 'lazy_loading' ) ) {
					$message = '<p><pre id="rpbt-debug-notice">' . $this->get_header( 'Debug Notice' ) . "\n\nPlease disable the lazy_loading feature to debug widgets and shortcodes</pre></p>";
				}
				echo $message;
			}
		}

	} // class
} // class exists
