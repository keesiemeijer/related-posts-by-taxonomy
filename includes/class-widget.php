<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class to display related posts with a widget.
 *
 * @since 0.1
 */
class Related_Posts_By_Taxonomy extends WP_Widget {

	public $plugin;

	/**
	 * Widget setup.
	 *
	 * @since 0.1
	 */
	function __construct() {

		/* Get defaults for this plugin. */
		$this->plugin = km_rpbt_plugin();

		$widget = array(
			'name' => __( 'Related Posts By Taxonomy', 'related-posts-by-taxonomy' ),
			'description' => __( 'Show a list of related posts by taxonomy.', 'related-posts-by-taxonomy' ),
		);

		/**
		 * Filter for changing the name and description of the widget.
		 *
		 * @since 0.3
		 *
		 * @param array $widget Array with widget name and description.
		 */
		$widget_filter = (array) apply_filters( 'related_posts_by_taxonomy_admin_widget', $widget );

		$widget = array_merge( $widget, $widget_filter );

		/* Widget settings. */
		$widget_ops = array(
			'classname'                   => 'related_posts_by_taxonomy',
			'description'                 => $widget['description'],
			'customize_selective_refresh' => true,
		);

		/* Widget control settings. */
		$control_ops = array( 'id_base' => 'related-posts-by-taxonomy' );

		/* Create the widget. */
		parent::__construct( 'related-posts-by-taxonomy', $widget['name'], $widget_ops, $control_ops );

		if ( ! is_admin() ) {

			/* add 'km_rpbt_related_post_id' query var for use with new WP_query */
			add_filter( 'query_vars', array( $this, 'add_related_post_id' ) );
		}
	}

	/**
	 * Displays the related posts on the front end.
	 *
	 * @since 0.1
	 * @param array $widget_args Display arguments including 'before_title', 'after_title',
	 *                                'before_widget', and 'after_widget'.
	 * @param array $args        The settings for the particular instance of the widget.
	 */
	function widget( $widget_args, $args ) {

		if ( ! $this->plugin ) {
			return '';
		}

		$args = $this->get_instance_settings( $args );

		/* don't show widget on pages other than single if singular_template is set */
		if ( $args['singular_template'] && ! is_singular() ) {
			return;
		}

		if ( empty( $args['post_id'] ) ) {
			$args['post_id'] = $this->get_the_ID();
		}

		if ( ! empty( $args['post_types'] ) ) {
			$args['post_types'] = array_keys( $args['post_types'] );
		}

		/* added in 2.0 */
		if ( $args['random'] ) {
			unset( $args['random'] );
			$args['order'] = 'RAND';
		}

		if ( 'thumbnails' === $args['format'] ) {
			$args['post_thumbnail'] = true;
		}

		/**
		 * Filter widget arguments.
		 *
		 * @since 0.1
		 *
		 * @param string $args        Widget instance.
		 * @param string $widget_args Widget arguments.
		 */
		$filter = apply_filters( 'related_posts_by_taxonomy_widget_args', $args, $widget_args );
		$args = array_merge( $args, (array) $filter );

		/* Not filterable */
		$args['type'] = 'widget';
		$args['fields'] = '';

		$related_posts = km_rpbt_get_related_posts( $args['post_id'], $args );

		/*
		 * Whether to hide the widget if no related posts are found.
		 * Set by the related_posts_by_taxonomy_widget_hide_empty filter.
		 * Default true.
		 */
		$hide_empty = (bool) km_rpbt_plugin_supports( 'widget_hide_empty' );

		if ( ! $hide_empty || ! empty( $related_posts ) ) {
			$this->widget_output( $related_posts, $args, $widget_args );
		}

		/**
		 * Fires after the related posts are displayed by the widget or shortcode.
		 *
		 * @param string Display type, widget or shortcode.
		 */
		do_action( 'related_posts_by_taxonomy_after_display', 'widget' );
	}

	/**
	 * Get the related posts used by the widget.
	 *
	 * @since 2.3.2
	 * @since 2.5.0 Deprecated.
	 *
	 * @param array $args Widget arguments.
	 * @return array Array with related post objects.
	 */
	function get_related_posts( $args ) {
		_deprecated_function( __FUNCTION__, '2.4.0', 'km_rpbt_get_related_posts()' );
		return km_rpbt_get_related_posts( $args );
	}

	/**
	 * Widget output
	 *
	 * @param array $related_posts    Array with related post objects.
	 * @param array $rpbt_args        Widget arguments.
	 * @param array $rpbt_widget_args Widget display arguments.
	 * @return void
	 */
	function widget_output( $related_posts, $rpbt_args, $rpbt_widget_args ) {

		/* get the template depending on the format  */
		$template = km_rpbt_get_template( (string) $rpbt_args['format'], $rpbt_args['type'] );

		if ( ! $template ) {
			return;
		}

		/* public template variables */
		$image_size = $rpbt_args['image_size']; // Deprecated in version 0.3.
		$columns    = $rpbt_args['columns']; // Deprecated in version 0.3.

		/* display of the widget */
		echo $rpbt_widget_args['before_widget'];

		$rpbt_args['title'] = apply_filters( 'widget_title', $rpbt_args['title'], $rpbt_args, $this->id_base );

		/* show widget title if one was set. */
		if ( '' !== trim( $rpbt_args['title'] ) ) {
			echo $rpbt_widget_args['before_title'] . $rpbt_args['title'] . $rpbt_widget_args['after_title'];
		}

		global $post; // Used for setup_postdata() in templates.
		require $template;
		wp_reset_postdata(); // Clean up global $post variable.

		echo $rpbt_widget_args['after_widget'];
	}

	/**
	 * Updates the widget settings.
	 *
	 * @since 0.1
	 * @param array $new_instance Current settings.
	 * @param array $old_instance Old settings.
	 */
	function update( $new_instance, $old_instance ) {

		$i = $old_instance;

		// Sanitation.
		$i['post_id']           = absint( $new_instance['post_id'] );
		$i['columns']           = absint( $new_instance['columns'] );
		$i['posts_per_page']    = (int) sanitize_text_field( $new_instance['posts_per_page'] );
		$i['title']             = sanitize_text_field( $new_instance['title'] );
		$i['format']            = sanitize_text_field( $new_instance['format'] );
		$i['taxonomies']        = sanitize_text_field( $new_instance['taxonomies'] );
		$i['image_size']        = sanitize_text_field( $new_instance['image_size'] );
		$i['post_types']        = array_map( 'sanitize_text_field', (array) $new_instance['post_types'] );
		$i['singular_template'] = isset( $new_instance['singular_template'] ) ? (bool) $new_instance['singular_template'] : '';
		$i['link_caption']      = isset( $new_instance['link_caption'] ) ? (bool) $new_instance['link_caption'] : '';
		$i['random']            = isset( $new_instance['random'] ) ? (bool) $new_instance['random'] : '';
		$i['show_date']         = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : '';

		// Validation.
		$i['post_id'] = $i['post_id'] ? $i['post_id']  : '';
		$i['columns'] = $i['columns'] ? $i['columns']  : 3;

		if ( -1 !== $i['posts_per_page'] ) {
			$posts_per_page = absint( $i['posts_per_page'] );
			$i['posts_per_page'] = $posts_per_page ? $posts_per_page : 5;
		}

		if (  empty( $i['post_types'] ) ) {
			$i['post_types']['post'] = 'on';
		}

		if ( ! in_array( $new_instance['format'], array_keys( $this->plugin->formats ) ) ) {
			$i['format'] = 'links';
		}

		if ( ! in_array( $i['image_size'], array_keys( $this->plugin->image_sizes ) ) ) {
			$i['image_size'] = 'thumbnail';
		}

		return $i;
	}

	/**
	 * Displays the widget form in /wp-admin/widgets.php.
	 *
	 * @since 0.1
	 * @param array $instance Current settings.
	 */
	function form( $instance ) {
		$i = $this->get_instance_settings( $instance );

		$fields = array();
		$pieces = array(
			'title',
			'posts_per_page',
			'random',
			'taxonomies',
			'post_types',
			'format',
			'show_date',
			'image_size',
			'columns',
			'link_caption',
			'singular_template',
			'post_id',
		);
		foreach ( $pieces as $piece ) {
			$file = str_replace( '_', '-', $piece );
			$fields[ $piece ] = $this->get_field( $file, $i );
		}

		/* Filter all fields at once, for convenience */
		$fields = (array) apply_filters_ref_array( 'related_posts_by_taxonomy_widget_form_fields', array( $fields, $i, $this ) );

		foreach ( $pieces as $piece ) {
			echo  ( isset( $fields[ $piece ] ) ) ? $fields[ $piece ] : '';
		}
	} // end form

	/**
	 * Get form field
	 *
	 * @since  2.5.1
	 *
	 * @param string $field Field name.
	 * @param array  $i     Widget instance settings.
	 * @return string String with field HYML.
	 */
	function get_field( $field, $i ) {
		$plugin = $this->plugin;
		$style = ' style="border-top: 1px solid #e5e5e5; padding-top: 1em;"';
		$file = RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/assets/partials/widget/' . $field . '.php';
		if ( ! is_readable( $file ) ) {
			return '';
		}

		ob_start();
		include $file;
		return ob_get_clean();
	}


	/**
	 * Adds public query var km_rpbt_related_post_id.
	 * called by filter hook 'query_vars'
	 *
	 * @since 0.2.1
	 * @param array $query_vars Array with query vars.
	 */
	function add_related_post_id( $query_vars ) {
		$query_vars[] = 'km_rpbt_related_post_id';
		return $query_vars;
	}

	/**
	 * Returns the current post id to get related posts for.
	 *
	 * @since 0.2.1
	 * @return int Post id.
	 */
	function get_the_ID() {
		global $wp_query;

		// Inside the loop.
		$post_id = get_the_ID();

		// Outside the loop.
		if ( ! in_the_loop() ) {

			if ( isset( $wp_query->post->ID ) ) {
				$post_id = $wp_query->post->ID;
			}

			if ( isset( $wp_query->query_vars['km_rpbt_related_post_id'] ) ) {
				$post_id = $wp_query->query_vars['km_rpbt_related_post_id'];
			}
		}

		return $post_id;
	}

	/**
	 * Returns all widget instance settings.
	 *
	 * @since 2.2.2
	 * @param array $instance Widget instance.
	 * @return array Widget instance
	 */
	function get_instance_settings( $instance ) {
		$i = $this->back_compat_settings( $instance );
		$defaults = km_rpbt_get_default_settings( 'widget' );
		// Set default post type.
		$defaults['post_types'] = array( 'post' => 'on' );

		return array_merge( $defaults, $i );
	}

	/**
	 * Returns correct settings if taxonomies argument is not defined.
	 *
	 * Provides back compatiblity for **upgading** from version 0.2.1.
	 * The variable taxonomy changed to taxonomies in version 0.2.2.
	 *
	 * @param array $i Widget instance.
	 * @return array Widget instance.
	 */
	function back_compat_settings( $i ) {

		if ( ! $i ) {
			return $i;
		}

		if ( isset( $i['taxonomies'] ) ) {
			// Taxonomies argument exist.
			return $i;
		}

		if ( isset( $i['taxonomy'] ) && $i['taxonomy'] ) {
			$i['taxonomies'] = ( 'all_taxonomies' === $i['taxonomy'] ) ? $this->plugin->all_tax : $i['taxonomy'];
			unset( $i['taxonomy'] );
		} else {
			// Taxonomy and taxonomies argument doesn't exist.
			$i['taxonomies'] = $this->plugin->all_tax;
		}

		return $i;
	}

} // end Related_Posts_By_Taxonomy class
