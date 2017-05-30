<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds WordPress REST API endpoint to get related posts.
 */
class Related_Posts_By_Taxonomy_Rest_API extends WP_REST_Controller {

	/**
	 * Arguments used for a related posts query.
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
	 * Get one item from the collection
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

		if ( ! isset( $args['id'] ) || ( (int) $args['id'] <= 0 ) ) {
			return $error;
		}

		$post = get_post( (int) $args['id'] );
		if ( empty( $post ) || empty( $post->ID ) ) {
			return $error;
		}

		$defaults = km_rpbt_get_default_settings( 'wp_rest_api' );

		/**
		 * Filter default wp_rest_api arguments.
		 *
		 * @since 2.3.0
		 *
		 * @param array $defaults Default wp_rest_api arguments.
		 */
		$defaults = apply_filters( 'related_posts_by_taxonomy_wp_rest_api_defaults', $defaults );

		$args['post_id'] = $args['id'];
		$args = array_merge( $defaults, (array) $args );

		/* Validates args. Sets the post types and post id if not set in filter above */
		$validated_args         = km_rpbt_validate_shortcode_atts( (array) $args );
		$validated_args['type'] = 'wp-rest-api';

		/**
		 * Filter wp_rest_api arguments.
		 *
		 * @since  2.3.0
		 *
		 * @param array $args wp_rest_api arguments.
		 */
		$args = apply_filters( 'related_posts_by_taxonomy_wp_rest_api_args', $validated_args );
		$args = array_merge( $validated_args, (array) $args );
		$args['type']  = 'wp-rest-api';

		$data = $this->prepare_item_for_response( $args, $request );
		return rest_ensure_response( $data );
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
		return apply_filters( 'related_posts_by_taxonomy_wp_rest_api', false );
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
	 * @param WP_REST_Request $request Request object.
	 * @return mixed
	 */
	public function prepare_item_for_response( $args, $request ) {

		$post_id       = $args['post_id'];
		$taxonomies    = $args['taxonomies'];
		$related_posts = $this->get_related_posts( $post_id, $taxonomies, $args );

		$data = array(
			'posts'         => $related_posts,
			'termcount'     => isset( $this->filter_args['termcount'] ) ? $this->filter_args['termcount'] : array(),
			'post_id'       => $post_id,
			'post_types'    => $args['post_types'],
			'taxonomies'    => km_rpbt_get_taxonomies( $taxonomies ),
			'related_terms' => isset( $this->filter_args['related_terms'] ) ? $this->filter_args['related_terms'] : array(),
			'rendered'      => km_rpbt_shortcode_output( $related_posts, $args ),
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
	 * Returns arguments used for the related posts query.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param array $results    Related posts. Array with Post objects or post IDs or post titles or post slugs.
	 * @param int   $post_id    Post id used to get the related posts.
	 * @param array $taxonomies Taxonomies used to get the related posts.
	 * @param array $args       Function arguments used to get the related posts.
	 * @return array            Related Posts.
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
	 * @param int   $post_id    Post id used to get the related posts.
	 * @param array $taxonomies Taxonomies used to get the related posts.
	 * @param array $args       Function arguments used to get the related posts.
	 * @return array            Related Posts.
	 */
	public function get_related_posts( $post_id, $taxonomies, $args ) {
		$function_args = $args;
		$plugin = km_rpbt_plugin();

		unset( $function_args['post_id'], $function_args['taxonomies'], $args['id'] );

		$cache = $plugin->cache instanceof Related_Posts_By_Taxonomy_Cache;

		add_filter( 'related_posts_by_taxonomy', array( $this, 'get_filter_args' ), 10, 4 );

		if ( $cache && ( isset( $args['cache'] ) && $args['cache'] ) ) {
			$related_posts = $plugin->cache->get_related_posts( $args );
		} else {
			/* get related posts */
			$related_posts = km_rpbt_related_posts_by_taxonomy( $post_id, $taxonomies, $function_args );
		}

		remove_filter( 'related_posts_by_taxonomy', array( $this, 'get_filter_args' ), 10, 4 );

		return $related_posts;
	}
}
