<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The related posts widget.
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
			'name'        => __( 'Related Posts By Taxonomy', 'related-posts-by-taxonomy' ),
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

		$settings = km_rpbt_get_default_settings( 'widget' );

		/**
		 * Filter widget defaults.
		 *
		 * @since 2.7.0
		 *
		 * @param array $defaults Default widget arguments. See km_rpbt_related_posts_by_taxonomy_shortcode() for
		 *                        for more information about default widget arguments.
		 */
		$defaults = apply_filters( "related_posts_by_taxonomy_widget_defaults", $settings );
		$defaults = array_merge( $settings, (array) $defaults );

		$args = $this->get_instance_settings( $args, $widget_args, $defaults );

		/* don't show widget on pages other than single if singular_template is set */
		if ( $args['singular_template'] && ! is_singular() ) {
			return;
		}

		if ( ! empty( $args['post_types'] ) ) {
			$args['post_types'] = array_keys( $args['post_types'] );
		}

		if ( empty( $args['post_id'] ) ) {
			$args['post_id'] = $this->get_the_ID();
		}

		if ( $args['random'] ) {
			unset( $args['random'] );
			$args['order'] = 'RAND';
		}

		if ( 'thumbnails' === $args['format'] ) {
			$args['post_thumbnail'] = true;
		}

		// Get allowed fields for use in templates
		$args['fields'] = km_rpbt_get_template_fields( $args );

		/**
		 * Filter widget arguments.
		 *
		 * @since 0.1
		 *
		 * @param string $args        Widget instance.
		 * @param string $widget_args Widget arguments.
		 */
		$filter = apply_filters( 'related_posts_by_taxonomy_widget_args', $args, $widget_args );
		$args   = array_merge( $args, (array) $filter );

		$args['title'] = apply_filters( 'widget_title', $args['title'], $args, $this->id_base );

		// Not filterable
		$args['type'] = 'widget';

		echo km_rpbt_get_feature_html( 'widget', $args );
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
		$i['singular_template'] = isset( $new_instance['singular_template'] ) ? (bool) $new_instance['singular_template'] : false;
		$i['link_caption']      = isset( $new_instance['link_caption'] ) ? (bool) $new_instance['link_caption'] : false;
		$i['random']            = isset( $new_instance['random'] ) ? (bool) $new_instance['random'] : false;
		$i['show_date']         = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;

		// Validation.
		$i['taxonomies'] = $this->is_all_taxonomies( $i['taxonomies'] ) ? '' : $i['taxonomies'];
		$i['post_id']    = $i['post_id'] ? $i['post_id']  : '';
		$i['columns']    = $i['columns'] ? $i['columns']  : 3;

		if ( -1 !== $i['posts_per_page'] ) {
			$posts_per_page = absint( $i['posts_per_page'] );
			$i['posts_per_page'] = $posts_per_page ? $posts_per_page : 5;
		}

		if ( empty( $i['post_types'] ) ) {
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

		/**
		 * Filter widget form instance settings.
		 *
		 * @since 2.7.0
		 *
		 * @param array $instance Widget form instance. See km_rpbt_related_posts_by_taxonomy_widget() for
		 *                        for more information about default feature arguments.
		 */
		$instance = apply_filters( "related_posts_by_taxonomy_widget_form_instance", $instance );
		$i        = $this->get_instance_settings( $instance );

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
			$file             = str_replace( '_', '-', $piece );
			$fields[ $piece ] = $this->get_field( $file, $i );
		}

		/* Filter all fields at once, for convenience */
		$fields = (array) apply_filters_ref_array( 'related_posts_by_taxonomy_widget_form_fields', array( $fields, $i, $this ) );

		foreach ( $pieces as $piece ) {
			echo  ( isset( $fields[ $piece ] ) ) ? $fields[ $piece ] : '';
		}
	} // end form

	/**
	 * Returns all widget instance settings.
	 *
	 * @since 2.2.2
	 * @param array $instance Widget instance.
	 * @return array Widget instance
	 */
	function get_instance_settings( $instance, $widget_args = array(), $defaults = array() ) {
		$i = $this->back_compat_settings( $instance );

		if ( ! $defaults ) {
			$defaults = km_rpbt_get_default_settings( 'widget' );
		}

		$defaults['post_types'] = array( 'post' => 'on' );

		$allowed = array(
			'before_widget',
			'after_widget',
			'before_title',
			'after_title',
		);

		// widget settings
		$widget_args = array_intersect_key( $widget_args, array_flip( $allowed ) );
		$defaults    = array_merge( $defaults, $widget_args );

		return array_merge( $defaults, $i );
	}

	/**
	 * Returns correct settings for changed instance settings.
	 *
	 * This function provides back compatiblity for **upgrading** from older versions.
	 *
	 * In version 0.2.2 the variable taxonomy changed to taxonomies.
	 * in version 2.7.0 the "all taxonomies" value is saved as an empty string.
	 *
	 * @param array $instance Widget instance.
	 * @return array Widget instance.
	 */
	function back_compat_settings( $instance ) {
		$i = $instance;

		if ( ! $i ) {
			return $i;
		}

		if ( isset( $i['taxonomies'] ) ) {
			// Taxonomies argument exist.
			$all_tax = ! $i['taxonomies'] || $this->is_all_taxonomies( $i['taxonomies'] );
			$i['taxonomies'] = $all_tax ? '' : $i['taxonomies'];
			return $i;
		}

		if ( isset( $i['taxonomy'] ) && $i['taxonomy'] ) {
			$i['taxonomies'] = ( 'all_taxonomies' === $i['taxonomy'] ) ? '' : $i['taxonomy'];
			unset( $i['taxonomy'] );
		} else {
			// Return default empty value for all taxonomies.
			$i['taxonomies'] = '';
		}

		return $i;
	}

	/**
	 * Get form field
	 *
	 * @since 2.5.1
	 *
	 * @param string $field Field name.
	 * @param array  $i     Widget instance settings.
	 * @return string String with field HYML.
	 */
	function get_field( $field, $i ) {
		$plugin = $this->plugin;
		$style  = ' style="border-top: 1px solid #e5e5e5; padding-top: 1em;"';
		$file   = RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/assets/partials/widget/' . $field . '.php';
		if ( ! is_readable( $file ) ) {
			return '';
		}

		ob_start();
		include $file;
		return ob_get_clean();
	}

	/**
	 * Returns the current post id to get related posts for.
	 *
	 *
	 * @since 0.2.1
	 * @since 2.5.0 Moved logic to km_rpbt_get_widget_post_id().
	 *
	 * @return int Post id.
	 */
	function get_the_ID() {
		return km_rpbt_get_widget_post_id();
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
	 * Check if all taxonomies is selected.
	 *
	 * The all_tax property is no longer used since version 2.7.0.
	 * Use empty string or 'km_rpbt_all_tax' for all taxonomies option in the widget.
	 *
	 * @since 2.7.0
	 *
	 * @param array|string $taxonomies Taxonomies.
	 * @return boolean True if all taxonomies is selected.
	 */
	function is_all_taxonomies( $taxonomies ) {
		if ( ( 'km_rpbt_all_tax' === $taxonomies ) || ( $this->plugin->all_tax === $taxonomies ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Get the related posts used by the widget.
	 *
	 * @since 2.3.2
	 * @deprecated 2.5.0 Use km_rpbt_get_related_posts() instead.
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
	 * @deprecated 2.6.0 Use km_rpbt_get_related_posts_html() instead.
	 *
	 * @param array $related_posts Array with related post objects.
	 * @param array $args          Widget arguments.
	 * @param array $widget_args   Widget display arguments.
	 * @return void
	 */
	function widget_output( $related_posts, $args, $widget_args ) {
		_deprecated_function( __FUNCTION__, '2.6.0', 'km_rpbt_get_related_posts_html()' );
		echo km_rpbt_get_related_posts_html( $related_posts, $args );
	}

} // end Related_Posts_By_Taxonomy class
