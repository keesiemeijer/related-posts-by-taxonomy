<?php
add_action( 'widgets_init', 'km_rpbt_related_posts_by_taxonomy_widget' );

/**
 * Register widget.
 *
 * @since 0.1
 */
function km_rpbt_related_posts_by_taxonomy_widget() {
	register_widget( 'Related_Posts_By_Taxonomy' );
}


/**
 * Related_Posts_By_Taxonomy class.
 *
 * @since 0.1
 */
class Related_Posts_By_Taxonomy extends WP_Widget {

	public $defaults;

	/**
	 * Widget setup.
	 *
	 * @since 0.1
	 */
	function Related_Posts_By_Taxonomy() {

		/* Get defaults for this plugin. */
		$this->defaults = Related_Posts_By_Taxonomy_Defaults::get_instance();

		$widget = array(
			'name' => __( 'Related Posts By Taxonomy', 'related-posts-by-taxonomy' ),
			'description' => __( 'Show a list of related posts by taxonomy.', 'related-posts-by-taxonomy' ),
		);

		/**
		 * Filter for changing name and description of widget.
		 *
		 * @since 0.2.2
		 *
		 * @param array   $widget Array with widget name and description.
		 */
		$widget_filter = (array) apply_filters( 'related_posts_by_taxonomy_widget', $widget );
		$widget = array_merge( $widget, $widget_filter );

		/* Widget settings. */
		$widget_ops = array(
			'classname'   => 'related_posts_by_taxonomy',
			'description' => $widget['description'],
		);

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'id_base' => 'related-posts-by-taxonomy' );

		/* Create the widget. */
		$this->WP_Widget( 'related-posts-by-taxonomy',
			$widget['name'],
			$widget_ops,
			$control_ops
		);

		if ( !is_admin() ) {

			/* hide widget on single posts pages if needed */
			add_filter( 'sidebars_widgets', array( $this, 'hide_widget' ) );

			/* add 'km_rpbt_related_post_id' query var */
			add_filter( 'query_vars', array( $this, 'add_related_post_id' ) );
		}
	}


	/**
	 * Displays the widget on the screen.
	 *
	 * @since 0.1
	 */
	function widget( $rpbt_widget_args, $rpbt_args ) {

		$i = $rpbt_args;

		/* don't show widget on pages other than single if singular_template is set */
		if ( $i['singular_template'] && !is_singular() ) {
			return;
		}

		/* 	set up arguments */

		if ( empty( $i['post_id'] ) ) {
			$i['post_id'] = $this->get_the_ID();
		}

		if ( !empty( $i['post_types'] ) ) {
			$i['post_types'] = array_keys( $i['post_types'] );
		}

		/* *
		 * back compat < version 0.2.1
		 *
		 * replaced parameters 'taxonomy' with 'taxonomies' in version 0.2.1 for consistency
		 * added parameters 'image_size' and 'columns' in 0.2.1
		 */

		if ( !isset( $i['taxonomies'] ) ) {

			$i['taxonomies'] = ( isset( $i['taxonomy'] ) ) ? $i['taxonomy'] : $this->defaults->all_tax;

			if ( 'all_taxonomies' === $i['taxonomies'] ) {
				$i['taxonomies'] = $this->defaults->all_tax;
			}

		}

		$i['image_size'] = ( isset( $i['image_size'] ) ) ? $i['image_size'] : 'thumbnail';
		$i['columns'] = ( isset( $i['columns'] ) ) ? $i['columns'] : 3;

		/* end of back compat */

		if ( $i['taxonomies'] === $this->defaults->all_tax ) {
			$i['taxonomies'] =  array_keys( $this->defaults->taxonomies );
		}

		/**
		 * Filter widget arguments to get the related posts.
		 *
		 * @since 0.1
		 *
		 * @param string  $i          Widget instance.
		 * @param string  $this->args Widget arguments.
		 */
		$instance_filter = apply_filters( 'related_posts_by_taxonomy_widget_args', $i, $rpbt_widget_args );
		$i = array_merge( $i, (array) $instance_filter );

		/* add type for use in templates */
		$i['type'] = 'widget';

		/* validate filtered arguments */

		if ( $i['taxonomies'] === $this->defaults->all_tax ) {
			$i['taxonomies'] =  array_keys( $this->defaults->taxonomies );
		}

		$i['post_thumbnail'] = false;
		if ( 'thumbnails' === $i['format'] ) {
			$i['post_thumbnail'] = true;
		}

		if ( !in_array( $i['image_size'], array_keys( $this->defaults->image_sizes )  ) ) {
			$i['image_size'] = 'thumbnail';
		}

		/* set public template variables $image_size and $columns */
		$image_size = $i['image_size'];
		$i['columns'] = absint( $i['columns'] );
		$i['columns'] = $columns = ( $i['columns'] > 0 ) ? $i['columns'] : 3;
		$i['caption'] = ( isset( $i['caption'] ) ) ? $i['caption'] : 'post_title';

		$function_args = $rpbt_args = $i;

		/* restricted arguments */
		unset( $function_args['fields'], $function_args['post_id'], $function_args['taxonomies'] );

		/* set public template variable $related posts */
		$related_posts = (array) km_rpbt_related_posts_by_taxonomy( $rpbt_args['post_id'], $rpbt_args['taxonomies'], $function_args );

		/**
		 * Filter to hide widget if no related posts are found.
		 * Default: true;
		 *
		 * @since 0.1
		 */
		$hide_empty = (bool) apply_filters( 'related_posts_by_taxonomy_widget_hide_empty', true );

		if ( !$hide_empty || !empty( $related_posts ) ) {

			$template = km_rpbt_related_posts_by_taxonomy_template( (string) $rpbt_args['format'], $rpbt_args['type'] );

			if ( $template ) {

				/* display of the widget */
				echo $rpbt_widget_args['before_widget'];

				$rpbt_args['title'] = apply_filters( 'widget_title', $rpbt_args['title'], $rpbt_args, $this->id_base );

				/* show widget title if one was set. */
				if ( '' !== trim( $rpbt_args['title'] ) ) {
					echo $rpbt_widget_args['before_title'] . $rpbt_args['title'] . $rpbt_widget_args['after_title'];
				}

				/* clean up variables before calling template */
				unset( $i, $instance_filter, $function_args, $hide_empty );

				global $post; // For setup_postdata in template
				require $template;
				wp_reset_postdata(); // Clean up global $post;
				echo $rpbt_widget_args['after_widget'];

			}
		}
	}


	/**
	 * Completely removes the widget from page requests other than singular
	 * if 'singular_template' is set.
	 *
	 * @since 0.1
	 */
	function hide_widget( $sidebars_widgets ) {

		$id_base = $this->id_base;
		$widget_args = get_option( 'widget_' . $id_base );

		if ( is_singular() || !( $widget_args && $id_base ) ) {
			return $sidebars_widgets;
		}

		foreach ( (array) $sidebars_widgets as $widget_area => $widget_list ) {

			if ( 'wp_inactive_widgets' == $widget_area || empty( $widget_list ) ) {
				continue;
			}

			foreach ( $widget_list as $pos => $widget_id ) {

				if ( !preg_match( '/' . $id_base . '-/', $widget_id ) ) {
					continue;
				}

				$arg_id = substr_replace( $widget_id, '', 0, strlen( $id_base . '-' ) );
				$arg_id = absint( $arg_id );

				if ( ( $arg_id > 0 ) && isset( $widget_args[ $arg_id ] ) ) {

					if ( isset( $widget_args[ $arg_id ]['singular_template'] ) &&
						$widget_args[ $arg_id ]['singular_template'] ) {
						unset( $sidebars_widgets[ $widget_area ][ $pos ] );
					}

				}

			}
		}


		return  $sidebars_widgets;
	}


	/**
	 * Update the widget settings.
	 *
	 * @since 0.1
	 */
	function update( $new_instance, $old_instance ) {

		$i = $old_instance;

		$i['title'] = strip_tags( $new_instance['title'] );

		$posts_per_page = (int) strip_tags( $new_instance['posts_per_page'] );

		if ( -1 === $posts_per_page ) {
			$i['posts_per_page'] = $posts_per_page;
		} else {
			$i['posts_per_page'] = ( absint( $posts_per_page ) ) ? absint( $posts_per_page ) : 5;
		}

		$i['taxonomies'] = stripslashes( $new_instance['taxonomies'] );

		$i['post_types'] = (array) $new_instance['post_types'];
		if (  empty( $i['post_types'] ) ) {
			$i['post_types']['post'] = 'on';
		}

		$i['format'] = (string) $new_instance['format'];
		if ( !in_array( $new_instance['format'], array_keys( $this->defaults->formats ) ) ) {
			$i['format'] = 'links';
		}

		$i['image_size'] = stripslashes( $new_instance['image_size'] );
		if ( 'thumbnails' === $i['format'] ) {
			$sizes = array_keys( $this->defaults->image_sizes );
			if ( !in_array( $i['image_size'], $sizes ) ) {
				$i['image_size'] = 'thumbnail';
			}
		}

		$columns = absint( strip_tags( $new_instance['columns'] ) );
		$i['columns'] = ( $columns > 0 ) ? $columns : 3;

		$i['singular_template'] = isset( $new_instance['singular_template'] ) ? (bool) $new_instance['singular_template'] : '';

		$post_id = absint( strip_tags( $new_instance['post_id'] ) );
		$i['post_id'] = ( $post_id  > 0 ) ? $post_id  : '';

		return $i;
	}


	/**
	 * Displays the widget form.
	 *
	 * @since 0.1
	 */
	function form( $instance ) {

		$i = $instance;
		$default = $this->defaults;

		// back compatibility version < 0.2.1
		if ( !isset( $i['taxonomies'] ) ) {
			/* new or updated widget */
			if ( isset( $i['taxonomy'] ) && $i['taxonomy'] ) {
				$i['taxonomies'] = $i['taxonomy'];
			} else {
				$i['taxonomies'] = $default->all_tax;
			}
		}
		// end back compatibility

		$i['title']             = ( isset( $i['title'] ) ) ? esc_attr( $i['title'] ) : 'Related Posts';
		$i['posts_per_page']    = ( isset( $i['posts_per_page'] ) ) ? (int) $i['posts_per_page'] : 5;
		$i['taxonomies']        = ( isset( $i['taxonomies'] ) ) ?  (string) $i['taxonomies'] : $default->all_tax;
		$i['format']            = ( isset( $i['format'] ) ) ?  (string) $i['format'] : 'links';
		$i['image_size']        = ( isset( $i['image_size'] ) ) ?  (string) $i['image_size'] : 'thumbnail';
		$i['columns']           = ( isset( $i['columns'] ) ) ? absint( $i['columns'] ) : 3;
		$i['singular_template'] = ( isset( $i['singular_template'] ) ) ? (bool) $i['singular_template'] : false;
		$i['post_id']           = ( isset( $i['post_id'] ) && $i['post_id'] ) ? absint( $i['post_id'] ) : '';


		/* since version 2.0.1 you can use -1 to display all posts */
		if ( -1 !== $i['posts_per_page'] ) {
			$i['posts_per_page'] = ( absint( $i['posts_per_page'] ) ) ? absint( $i['posts_per_page'] ) : 5;
		}

		if ( !isset( $i['post_types'] ) ) {
			$i['post_types']['post'] = 'on';
		}

		/* form fields */

		// title
		$field = '<p class="rpbt_title"><label for="' . $this->get_field_id( 'title' ) . '">';
		$field .= __( 'Title:', 'related-posts-by-taxonomy' ) . ' </label>';
		$field .= '<input id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" ';
		$title = $field . 'type="text" value="' . $i['title'] . '" class="widefat" /></p>';

		// posts_per_page
		$field = '<p class="rpbt_posts_per_page"><label for="' . $this->get_field_id( 'posts_per_page' ) . '">';
		$field .= __( 'Number of related posts to show:', 'related-posts-by-taxonomy' ) . ' </label>';
		$field .= '<input id="' . $this->get_field_id( 'posts_per_page' ) . '" name="' . $this->get_field_name( 'posts_per_page' ) . '" ';
		$field .= 'type="text" value="' . $i['posts_per_page'] . '" size="3" />';
		$field .= '<br/><span class="description">' . __( 'Use -1 to show all related posts.', 'related-posts-by-taxonomy' ) . '</span>';
		$posts_per_page = $field . '</p>';

		// taxonomies
		$field = '<div class="rpbt_taxonomies"><h4>' . __( 'Taxonomies', 'related-posts-by-taxonomy' ) . ' </h4>';
		$field .= '<p><label for="' . $this->get_field_id( 'taxonomies' ) . '">';
		$field .= __( 'Taxonomy:', 'related-posts-by-taxonomy' ) .'</label>';
		$field .= '<select name="' . $this->get_field_name( 'taxonomies' ) . '" id="' . $this->get_field_id( 'taxonomies' ) . '" class="widefat">';
		$field .= '<option value="' . $default->all_tax . '" ' . selected( $i['taxonomies'], $default->all_tax, false ) . '>';
		$field .= __( 'All Taxonomies', 'related-posts-by-taxonomy' ) . '</option>';
		foreach ( $default->taxonomies as $name => $label ) {
			$field .= '<option value="' . $name . '"' . selected( $i['taxonomies'], $name, false ) . '>' . $label . '</option>';
		}
		$taxonomies = $field . '</select></p></div>';

		// post types
		$field = '<div class="rpbt_post_types"><h4>' . __( 'Post Types', 'related-posts-by-taxonomy' ) .' </h4><p>';
		foreach ( $default->post_types as $name => $label ) {
			$field .= '<input type="checkbox" class="checkbox" id="' . $this->get_field_id( 'post_types' ) . "_$name" . '" ';
			$field .= 'name="' . $this->get_field_name( 'post_types' ) . "[$name]" .'" ';
			if ( isset( $i['post_types'][ $name ] ) && ( 'on' === $i['post_types'][ $name ] ) ) {
				$field .= 'checked="checked"';
			}
			$field .= ' /><label for="' .$this->get_field_id( 'post_types'  ) . "_$name" . '">' . $label . '</label><br />';
		}
		$post_types = $field . '</p></div>';

		// display
		$display = '<h4 class="rpbt_widget_display_title">' . __( 'Display', 'related-posts-by-taxonomy' ) . '</h4>';

		// format
		$field = '<p class ="rpbt_format"><label for="' . $this->get_field_id( 'format' ) . '">';
		$field .= __( 'Format:', 'related-posts-by-taxonomy' ) . '</label>';
		$field .= '<select name="' . $this->get_field_name( 'format' ) . '" id="' . $this->get_field_id( 'format' ) . '" class="widefat">';
		foreach ( $default->formats as $name => $label ) {
			$field .= '<option value="' . $name . '"' . selected( $i['format'], $name, false ) . '>' . $label . '</option>';
		}
		$format = $field . '</select></p>';

		// image_size
		$field = '<p class ="rpbt_image_size"><label for="' . $this->get_field_id( 'image_size' ) . '">';
		$field .= __( 'Format:', 'related-posts-by-taxonomy' ) . '</label>';
		$field .= '<select name="' . $this->get_field_name( 'image_size' ) . '" id="' . $this->get_field_id( 'image_size' ) . '" class="widefat">';
		foreach ( $default->image_sizes as $name => $label ) {
			$field .= '<option value="' . $name . '"' . selected( $i['image_size'], $name, false ) . '>' . $label . '</option>';
		}
		$image_size = $field . '</select></p>';

		// columns
		$field = '<p class ="rpbt_columns"><label for="' . $this->get_field_id( 'columns' ) . '">';
		$field .= __( 'Number of image columns:', 'related-posts-by-taxonomy' ) . ' </label>';
		$field .= '<input id="' . $this->get_field_id( 'columns' ) . '" name="' . $this->get_field_name( 'columns' ) . '" ';
		$columns = $field . 'type="text" value="' . $i['columns'] . '" size="3" /></p>';

		// singular_template
		$field = '<p class ="rpbt_singular">';
		$field .= '<input class="checkbox" type="checkbox" ' . checked( $i['singular_template'], 1, false ) . ' ';
		$field .= 'id="' . $this->get_field_id( 'singular_template' ) . '" name="' . $this->get_field_name( 'singular_template' ) . '" />';
		$field .= '<label for="' . $this->get_field_id( 'singular_template' ) . '">';
		$singular_template = $field . __( 'Display this widget only on single post pages', 'related-posts-by-taxonomy' ) . '</label></p>';

		// post_id
		$field = '<p class="rpbt_post_id"><label for="' . $this->get_field_id( 'post_id' ) . '">';
		$field .= __( 'Display related posts for post ID (optional):', 'related-posts-by-taxonomy' ) . ' </label>';
		$field .= '<input id="' . $this->get_field_id( 'post_id' ) . '" name="' . $this->get_field_name( 'post_id' ) . '" ';
		$post_id = $field . 'type="text" value="' . $i['post_id'] . '" size="5" /></p>';

		$pieces = array( 'title', 'posts_per_page', 'taxonomies', 'post_types', 'display', 'format', 'image_size', 'columns', 'singular_template', 'post_id' );

		/* Filter all fields at once, for convenience */
		$form_fields = (array) apply_filters_ref_array( 'related_posts_by_taxonomy_widget_form_fields', array( compact( $pieces ), $i, $this ) );

		foreach ( $pieces as $piece ) {
			echo  ( isset( $form_fields[ $piece ] ) ) ? $form_fields[ $piece ] : '';
		}

	} // end form


	/**
	 * Adds public query var km_rpbt_related_post_id
	 *
	 * @since 0.2.1
	 */
	function add_related_post_id( $query_vars ) {
		$query_vars[] = 'km_rpbt_related_post_id';
		return $query_vars;
	}


	/**
	 * Gets the post id.
	 *
	 * @since 0.2.1
	 * @return int Post id.
	 */
	function get_the_ID() {
		global $wp_query;

		// inside the loop
		$post_id = get_the_ID();

		// outside the loop
		if ( !in_the_loop() ) {

			if ( isset( $wp_query->post->ID ) ) {
				$post_id =  $wp_query->post->ID;
			}

			if ( isset( $wp_query->query_vars['km_rpbt_related_post_id'] ) ) {
				$post_id =  $wp_query->query_vars['km_rpbt_related_post_id'];
			}
		}

		return $post_id;
	}

} // end Related_Posts_By_Taxonomy class