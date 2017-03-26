<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'widgets_init', 'km_rpbt_related_posts_by_taxonomy_widget' );

/**
 * Registers the related posts by taxonomy widget.
 *
 * @since 0.1
 */
function km_rpbt_related_posts_by_taxonomy_widget() {
	register_widget( 'Related_Posts_By_Taxonomy' );
}


/**
 * Related_Posts_By_Taxonomy widget class.
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
		 * Filter for changing name and description of widget.
		 *
		 * @since 0.3
		 *
		 * @param array $widget Array with widget name and description.
		 */
		$widget_filter = (array) apply_filters( 'related_posts_by_taxonomy_widget', $widget );

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
	 * @param array $rpbt_widget_args Display arguments including 'before_title', 'after_title',
	 *                                'before_widget', and 'after_widget'.
	 * @param array $rpbt_args        The settings for the particular instance of the widget.
	 */
	function widget( $rpbt_widget_args, $rpbt_args ) {

		if ( ! $this->plugin ) {
			return '';
		}

		$i = $rpbt_args;
		$i = $this->get_instance_settings( $i );

		/* don't show widget on pages other than single if singular_template is set */
		if ( $i['singular_template'] && ! is_singular() ) {
			return;
		}

		if ( empty( $i['post_id'] ) ) {
			$i['post_id'] = $this->get_the_ID();
		}

		if ( ! empty( $i['post_types'] ) ) {
			$i['post_types'] = array_keys( $i['post_types'] );
		}

		/* added in 2.0 */
		if ( $i['random'] ) {
			unset( $i['random'] );
			$i['order'] = 'RAND';
		}

		if ( 'thumbnails' === $i['format'] ) {
			$i['post_thumbnail'] = true;
		}

		/**
		 * Filter widget arguments to get the related posts.
		 *
		 * @since 0.1
		 *
		 * @param string $i                Widget instance.
		 * @param string $rpbt_widget_args Widget arguments.
		 */
		$filter = apply_filters( 'related_posts_by_taxonomy_widget_args', $i, $rpbt_widget_args );
		$i = array_merge( $i, (array) $filter );

		/* Not filterable */
		$i['type'] = 'widget';

		$rpbt_args  = $function_args = $i;

		/* restricted arguments */
		unset( $function_args['fields'], $function_args['post_id'], $function_args['taxonomies'] );

		$cache = $this->plugin->cache instanceof Related_Posts_By_Taxonomy_Cache;

		if ( $cache && ( isset( $rpbt_args['cache'] ) && $rpbt_args['cache'] ) ) {
			$related_posts = $this->plugin->cache->get_related_posts( $rpbt_args );
		} else {
			/* get related posts */
			$related_posts = km_rpbt_related_posts_by_taxonomy( $rpbt_args['post_id'], $rpbt_args['taxonomies'], $function_args );
		}

		/**
		 * Filter whether to hide the widget if no related posts are found.
		 *
		 * @since 0.1
		 *
		 * @param bool $hide Whether to hide the widget if no related posts are found.
		 *                      Defaults to true.
		 */
		$hide_empty = (bool) apply_filters( 'related_posts_by_taxonomy_widget_hide_empty', true );

		if ( ! $hide_empty || ! empty( $related_posts ) ) {
			$this->widget_output( $related_posts, $rpbt_args, $rpbt_widget_args );
		}

		/**
		 * Fires after the related posts are displayed
		 *
		 * @param string Display type, widget or shortcode.
		 */
		do_action( 'related_posts_by_taxonomy_after_display', 'widget' );
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
		$template = km_rpbt_related_posts_by_taxonomy_template( (string) $rpbt_args['format'], $rpbt_args['type'] );

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

		$plugin = $this->plugin;

		if ( ! $plugin ) {
			printf( '<p>%s</p>', __( 'Oops, something went wrong', 'related-posts-by-taxonomy' ) );
			return;
		}

		$i = $this->get_instance_settings( $instance );

		/* widget form fields */

		$after = "\n\t";
		$before = "\t";
		$style = ' style="border-top: 1px solid #e5e5e5; padding-top: 1em;"';

		// Title.
		$field = "\t" . '<p class="rpbt_title"><label for="' . $this->get_field_id( 'title' ) . '">';
		$field .= __( 'Title', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<input id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" ';
		$title = $before . $field . 'type="text" value="' . esc_attr( $i['title'] ) . '" class="widefat" /></p>' . $after;

		// Field: posts_per_page.
		$field = '<p class="rpbt_posts_per_page"><label for="' . $this->get_field_id( 'posts_per_page' ) . '">';
		$field .= __( 'Number of related posts to show', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<input id="' . $this->get_field_id( 'posts_per_page' ) . '" name="' . $this->get_field_name( 'posts_per_page' ) . '" ';
		$field .= 'value="' . esc_attr( $i['posts_per_page'] ) . '" size="3" class="tiny-text" step="1" min="-1" type="number" />';
		$field .= '<br/><span class="description">' . __( 'Use -1 to show all related posts.', 'related-posts-by-taxonomy' ) . '</span>';
		$posts_per_page = $before . $field . '</p>' . $after;

		$field = '<p>';
		$field .= '<input class="checkbox" type="checkbox" ' . checked( $i['random'], 1, false ) . ' ';
		$field .= 'id="' . $this->get_field_id( 'random' ) . '" name="' . $this->get_field_name( 'random' ) . '" />';
		$field .= ' <label for="' . $this->get_field_id( 'random' ) . '">';
		$random = $before . $field . __( 'Randomize related posts.', 'related-posts-by-taxonomy' ) . '</label></p>' . $after;

		// Field: taxonomies.
		$field = '<div class="rpbt_taxonomies"><h4' . $style . '>' . __( 'Taxonomies', 'related-posts-by-taxonomy' ) . ' </h4>';
		$field .= '<p><label for="' . $this->get_field_id( 'taxonomies' ) . '">';
		$field .= __( 'Taxonomy', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<select name="' . $this->get_field_name( 'taxonomies' ) . '" id="' . $this->get_field_id( 'taxonomies' ) . '" class="widefat">';
		$field .= '<option value="' . esc_attr( $plugin->all_tax ) . '" ' . selected( $i['taxonomies'], $plugin->all_tax, false ) . '>';
		$field .= __( 'All Taxonomies', 'related-posts-by-taxonomy' ) . '</option>';
		foreach ( $plugin->taxonomies as $name => $label ) {
			$field .= '<option value="' . esc_attr( $name ) . '"' . selected( $i['taxonomies'], $name, false ) . '>' . $label . '</option>';
		}
		$taxonomies = $before . $field . '</select></p></div>' . $after;

		// Field: post types.
		$field = '<div class="rpbt_post_types"><h4' . $style . '>' . __( 'Post Types', 'related-posts-by-taxonomy' ) . ' </h4><p>';
		foreach ( $plugin->post_types as $name => $label ) {
			$field .= '<input type="checkbox" class="checkbox" id="' . $this->get_field_id( 'post_types' ) . "_$name" . '" ';
			$field .= 'name="' . $this->get_field_name( 'post_types' ) . "[$name]" . '"';
			if ( isset( $i['post_types'][ $name ] ) && ( 'on' === $i['post_types'][ $name ] ) ) {
				$field .= ' checked="checked"';
			}
			$field .= ' /> <label for="' . $this->get_field_id( 'post_types' ) . "_$name" . '">' . $label . '</label><br />';
		}
		$post_types = $before . $field . '</p></div>' . $after;

		// Display.
		$display = $before . '<h4 class="rpbt_display_title"' . $style . '>' . __( 'Display', 'related-posts-by-taxonomy' ) . '</h4>' . $after;

		// Field: format.
		$field = '<p class="rpbt_format"><label for="' . $this->get_field_id( 'format' ) . '">';
		$field .= __( 'Format', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<select name="' . $this->get_field_name( 'format' ) . '" id="' . $this->get_field_id( 'format' ) . '" class="widefat">';
		foreach ( $plugin->formats as $name => $label ) {
			$field .= '<option value="' . esc_attr( $name ) . '"' . selected( $i['format'], $name, false ) . '>' . $label . '</option>';
		}
		$format = $before . $field . '</select></p>' . $after;

		$image_display = $before . '<h4 class="rpbt_widget_image_display_title" ' . $style . '>' . __( 'Image Display', 'related-posts-by-taxonomy' ) . '</h4>' . $after;

		// Field: image_size.
		$field = '<p class="rpbt_image_size"><label for="' . $this->get_field_id( 'image_size' ) . '">';
		$field .= __( 'Image Size', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<select name="' . $this->get_field_name( 'image_size' ) . '" id="' . $this->get_field_id( 'image_size' ) . '" class="widefat">';
		foreach ( $plugin->image_sizes as $name => $label ) {
			$field .= '<option value="' . esc_attr( $name ) . '"' . selected( $i['image_size'], $name, false ) . '>' . $label . '</option>';
		}
		$image_size = $before . $field . '</select></p>' . $after;

		// Field columns.
		$field = '<p class="rpbt_columns"><label for="' . $this->get_field_id( 'columns' ) . '">';
		$field .= __( 'Number of image columns', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<input id="' . $this->get_field_id( 'columns' ) . '" name="' . $this->get_field_name( 'columns' ) . '" ';
		$field .= 'value="' . esc_attr( $i['columns'] ) . '" size="3" class="tiny-text" step="1" min="0" type="number" />';
		$columns = $before . $field . '</p>' . $after;

		// Field: link_caption.
		$field = '<p class="rpbt_link_caption">';
		$field .= '<input class="checkbox" type="checkbox" ' . checked( $i['link_caption'], 1, false ) . ' ';
		$field .= 'id="' . $this->get_field_id( 'link_caption' ) . '" name="' . $this->get_field_name( 'link_caption' ) . '" />';
		$field .= ' <label for="' . $this->get_field_id( 'link_caption' ) . '">';
		$field = $field . __( 'Link image captions to posts', 'related-posts-by-taxonomy' ) . '</label>';
		$link_caption = $before . $field . '</p>' . $after;

		$widget_display = $before . '<h4 class="rpbt_widget_display_title" ' . $style . '>' . __( 'Widget Display', 'related-posts-by-taxonomy' ) . '</h4>' . $after;

		// Field: singular_template.
		$field = '<p class="rpbt_singular">';
		$field .= '<input class="checkbox" type="checkbox" ' . checked( $i['singular_template'], 1, false ) . ' ';
		$field .= 'id="' . $this->get_field_id( 'singular_template' ) . '" name="' . $this->get_field_name( 'singular_template' ) . '" />';
		$field .= ' <label for="' . $this->get_field_id( 'singular_template' ) . '">';
		$singular_template = $before . $field . __( 'Display this widget on single post pages only', 'related-posts-by-taxonomy' ) . '</label></p>' . $after;

		// Field: post_id.
		$field = '<p class="rpbt_post_id"><label for="' . $this->get_field_id( 'post_id' ) . '">';
		$field .= __( 'Display related posts for post ID (optional)', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<input id="' . $this->get_field_id( 'post_id' ) . '" name="' . $this->get_field_name( 'post_id' ) . '" ';
		$post_id = $before . $field . 'type="text" value="' . $i['post_id'] . '" size="5" /></p>' . "\n";

		$pieces = array(
			'title',
			'posts_per_page',
			'random',
			'taxonomies',
			'post_types',
			'display',
			'format',
			'image_display',
			'image_size',
			'columns',
			'link_caption',
			'widget_display',
			'singular_template',
			'post_id',
		);

		/* Filter all fields at once, for convenience */
		$form_fields = (array) apply_filters_ref_array( 'related_posts_by_taxonomy_widget_form_fields', array( compact( $pieces ), $i, $this ) );

		foreach ( $pieces as $piece ) {
			echo  ( isset( $form_fields[ $piece ] ) ) ? $form_fields[ $piece ] : '';
		}

	} // end form


	/**
	 * Adds public query var km_rpbt_related_post_id.
	 * called by filter hook 'query_vars'
	 *
	 * @since 0.2.1
	 * @param unknown $query_vars Query var.
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
