<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a WordPress REST API endpoint to get related posts.
 *
 * Registered endpoint: /wp-json/related-posts-by-taxonomy/v1/posts/{$post_id}
 *
 * @since 2.3.0
 */
class Related_Posts_By_Taxonomy_Rest_API extends WP_REST_Controller {

	/**
	 * Arguments used by the related posts query.
	 *
	 * @since 2.3.0
	 * @var array
	 */
	public $filter_args;

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @since 2.3.0
	 */
	public function register_routes() {

		$version = '1';
		$namespace = 'related-posts-by-taxonomy/v' . $version;
		$base = 'posts';

		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
				'args' => array(
					'id' => array(
						'description' => __( 'Unique identifier for the object.', 'related-posts-by-taxonomy' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
					'args'                => array(
						'context' => $this->get_context_param( array( 'default' => 'view' ) ),
					),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Get one item from the collection.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$args  = $request->get_params();
		$error = new WP_Error( 'rest_post_invalid_id', __( 'Invalid post ID.', 'related-posts-by-taxonomy' ), array( 'status' => 404 ) );

		if ( ! isset( $args['id'] ) || ! absint( $args['id'] ) ) {
			return $error;
		}

		$post = get_post( absint( $args['id'] ) );
		if ( ! $post ) {
			return $error;
		}

		$args['post_id'] = $post->ID;

		$args = $this->filter_request( $args );

		if ( ! $args ) {
			$error = new WP_Error( 'rest_post_invalid_parameters', __( 'Invalid parameters.', 'related-posts-by-taxonomy' ), array( 'status' => 404 ) );
			return $error;
		}

		$data = $this->prepare_item_for_response( $args, $request );
		return rest_ensure_response( $data );
	}

	/**
	 * Filter the request arguments.
	 *
	 * Set defaults for every requests with the related_posts_by_taxonomy_wp_rest_api_defaults filter.
	 * Filter validated request arguments with the related_posts_by_taxonomy_wp_rest_api_args filter.
	 *
	 * @since 2.5.1
	 *
	 * @param array $args Request arguments
	 *                    See km_rpbt_get_related_posts() for for more
	 *                    information on accepted arguments.
	 * @return array|false Filtered request arguments or false when invalid
	 *                     taxonomies or post types are used int the request.
	 */
	private function filter_request( $args ) {
		$type     = 'wp_rest_api';
		$post_id  = $args['post_id'];
		$defaults = km_rpbt_get_default_settings( $type );

		/**
		 * Filter default wp_rest_api arguments.
		 *
		 * @since 2.3.0
		 *
		 * @param array   $defaults Default wp_rest_api arguments.
		 * @param WP_Post Post object to get the releated posts for
		 */
		$defaults = apply_filters( 'related_posts_by_taxonomy_wp_rest_api_defaults', $defaults, $post_id );
		$args = array_merge( $defaults, (array) $args );

		// Un-filterable arguments.
		$args['type']    = $type;
		$args['post_id'] = $post_id;

		// Check taxonomies.
		$taxonomies         = ! empty( $args['taxonomies'] );
		$args['taxonomies'] = km_rpbt_get_taxonomies( $args['taxonomies'] );
		$tax_fail           = $taxonomies && ! $args['taxonomies'];

		// Check post types
		$post_types = ! empty( $args['post_types'] );

		// Default to the post type from the current post if no post types are in the request.
		if ( ! $post_types ) {
			$args['post_types'] = get_post_type( $post_id );
		}

		$args['post_types'] = km_rpbt_get_post_types( $args['post_types'] );
		$type_fail          = $post_types && ! $args['post_types'];

		// Set post_thumbnail argument depending on format.
		if ( 'thumbnails' === $args['format'] ) {
			$args['post_thumbnail'] = true;
		}

		/**
		 * Filter wp_rest_api arguments.
		 *
		 * @since  2.3.0
		 *
		 * @param array $args wp_rest_api arguments.
		 */
		$args = apply_filters( 'related_posts_by_taxonomy_wp_rest_api_args', $args );
		$args = array_merge( $defaults, (array) $args );

		// Un-filterable arguments.
		$args['type']    = $type;
		$args['post_id'] = $post_id;

		$error = false;

		// Validate taxonomies again (could be set by wp_rest_api_args filter).
		if ( $tax_fail ) {
			$args['taxonomies'] = km_rpbt_get_taxonomies( $args['taxonomies'] );
			$error = ! $args['taxonomies'];
		}

		// Validate post types again (could be set by wp_rest_api_args filter).
		if ( $type_fail ) {
			$args['post_type'] = km_rpbt_get_post_types( $args['post_types'] );
			$error = $error ? $error : ! $args['post_types'];
		}

		if ( $error ) {
			return false;
		}

		return $args;
	}

	/**
	 * Check if a given request has access to get items
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_items_permissions_check( $request ) {
		if ( km_rpbt_plugin_supports( 'wp_rest_api' ) || is_user_logged_in() ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a given request has access to get a specific item
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|bool
	 */
	public function get_item_permissions_check( $request ) {
		return $this->get_items_permissions_check( $request );
	}

	/**
	 * Prepare the item for the REST response
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param array           $args    WP Rest API arguments of the item.
	 *                                 See km_rpbt_get_related_posts() for for more
	 *                                 information on accepted request arguments.
	 * @param WP_REST_Request $request Request object.
	 * @return mixed
	 */
	public function prepare_item_for_response( $args, $request ) {
		$related_posts = $this->get_related_posts( $args );

		$rendered = '';
		$fields   = strtolower( $args['fields'] );
		if ( $related_posts && ! in_array( $fields, array( 'ids', 'names', 'slugs' ) ) ) {
			// Render posts if the query was for post objects.
			$rendered = km_rpbt_shortcode_output( $related_posts, $args );
		}

		/* Default to all taxonomies if none were provided. */
		if ( ! $args['taxonomies'] ) {
			$args['taxonomies'] = km_rpbt_get_public_taxonomies();
		}

		$data = array(
			'posts'         => $related_posts,
			'post_id'       => $args['post_id'],
			'post_types'    => $args['post_types'],
			'taxonomies'    => $args['taxonomies'],
			'termcount'     => isset( $this->filter_args['termcount'] ) ? $this->filter_args['termcount'] : array(),
			'related_terms' => isset( $this->filter_args['related_terms'] ) ? $this->filter_args['related_terms'] : array(),
			'rendered'      => $rendered,
		);

		// Reset filter_args.
		$this->filter_args = array();

		$context = ! empty( $request['context'] ) ? $request['context'] : 'view';
		$data    = $this->add_additional_fields_to_object( $data, $request );
		$data    = $this->filter_response_by_context( $data, $context );

		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );

		return $response;
	}

	/**
	 * Retrieves the related post's schema, conforming to JSON Schema.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return array Item schema data.
	 */
	public function get_item_schema() {
		$schema = array(
			'$schema'    => 'http://json-schema.org/schema#',
			'title'      => 'related_posts_by_tax',
			'type'       => 'object',
			'properties' => array(
				'posts'            => array(
					'description' => __( 'The related posts.', 'related-posts-by-taxonomy' ),
					'type'        => 'array',
					'items'       => array(
						'type'    => 'object|string|integer',
					),
					'context'     => array( 'view' ),
				),
				'termcount'            => array(
					'description' => __( 'Number of related terms in common with the post.', 'related-posts-by-taxonomy' ),
					'type'        => 'array',
					'items'       => array(
						'type'    => 'integer',
					),
					'context'     => array( 'view' ),
				),
				'post_id'            => array(
					'description' => __( 'The Post ID to get related posts for.', 'related-posts-by-taxonomy' ),
					'type'        => 'integer',
					'context'     => array( 'view' ),
				),
				'post_types'            => array(
					'description' => __( 'Post types used in query for related posts.', 'related-posts-by-taxonomy' ),
					'type'        => 'array',
					'items'       => array(
						'type'    => 'string',
					),
					'context'     => array( 'view' ),
				),
				'taxonomies'            => array(
					'description' => __( 'Taxonomies used in query for related posts.', 'related-posts-by-taxonomy' ),
					'type'        => 'array',
					'items'       => array(
						'type'    => 'string',
					),
					'context'     => array( 'view' ),
				),
				'related_terms'            => array(
					'description' => __( 'Related term ids used in query for related posts.', 'related-posts-by-taxonomy' ),
					'type'        => 'array',
					'items'       => array(
						'type'    => 'integer',
					),
					'context'     => array( 'view' ),
				),
				'rendered'            => array(
					'description' => __( 'Rendered related posts HTML', 'related-posts-by-taxonomy' ),
					'type'        => 'string',
					'context'     => array( 'view' ),
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Returns arguments used by the related posts query.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param array $results    Related posts. Array with Post objects or post IDs or post titles or post slugs.
	 * @param int   $post_id    Post id used to get the related posts.
	 * @param array $taxonomies Taxonomies used to get the related posts.
	 * @param array $args       Query arguments used to get the related posts.
	 * @return array Related Posts.
	 */
	public function get_filter_args( $results, $post_id, $taxonomies, $args ) {
		$this->filter_args = $args;
		return $results;
	}

	/**
	 * Returns related posts from database or cache.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param array $args Query arguments used to get the related posts.
	 *                    See km_rpbt_get_related_posts() for for more
	 *                    information on accepted arguments.
	 * @return array Related Posts.
	 */
	public function get_related_posts( $args ) {
		add_filter( 'related_posts_by_taxonomy', array( $this, 'get_filter_args' ), 10, 4 );

		unset( $args['id'] );

		$related_posts = km_rpbt_get_related_posts( $args['post_id'], $args );
		remove_filter( 'related_posts_by_taxonomy', array( $this, 'get_filter_args' ), 10, 4 );

		return $related_posts;
	}
}
