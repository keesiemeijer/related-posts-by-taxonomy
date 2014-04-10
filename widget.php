<?php
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
		 * @since 0.3
		 *
		 * @param array   $widget Array with widget name and description.
		 */
		$widget_filter = (array) apply_filters( 'related_posts_by_taxonomy_widget', $widget );

		$widget = array_merge( $widget, $widget_filter );

		/* Widget settings. */
		$widget_ops = array( 'classname'   => 'related_posts_by_taxonomy', 'description' => $widget['description'] );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'id_base' => 'related-posts-by-taxonomy' );

		/* Create the widget. */
		$this->WP_Widget( 'related-posts-by-taxonomy', $widget['name'], $widget_ops, $control_ops );

		if ( !is_admin() ) {

			/* add 'km_rpbt_related_post_id' query var for use with new WP_query */
			add_filter( 'query_vars', array( $this, 'add_related_post_id' ) );
		}
	}


	/**
	 * Displays the related posts on the front end.
	 *
	 * @since 0.1
	 */
	function widget( $rpbt_widget_args, $rpbt_args ) {

		$i = $rpbt_args;

		/* don't show widget on pages other than single if singular_template is set */
		if ( $i['singular_template'] && !is_singular() ) {
			return;
		}

		if ( empty( $i['post_id'] ) ) {
			$i['post_id'] = $this->get_the_ID();
		}

		if ( !empty( $i['post_types'] ) ) {
			$i['post_types'] = array_keys( $i['post_types'] );
		}

		// back compat
		if ( !isset( $i['taxonomies'] ) ) {
			$i = $this->update_rpbt_widget( $i );
		}

		/* Added in 0.3 (not part of the widget settings). Can be filtered below */
		$i['caption'] = 'post_title';

		/**
		 * Filter widget arguments to get the related posts.
		 *
		 * @since 0.1
		 *
		 * @param string  $i                Widget instance.
		 * @param string  $rpbt_widget_args Widget arguments.
		 */
		$instance_filter = apply_filters( 'related_posts_by_taxonomy_widget_args', $i, $rpbt_widget_args );
		$i = array_merge( $i, (array) $instance_filter );

		/* add type for use in templates */
		$i['type'] = 'widget';

		/* convert "all" to array with all public taxonomies */
		if ( $i['taxonomies'] === $this->defaults->all_tax ) {
			$i['taxonomies'] =  array_keys( $this->defaults->taxonomies );
		}

		$i['post_thumbnail'] = false;
		if ( 'thumbnails' === $i['format'] ) {
			$i['post_thumbnail'] = true;
		}

		/* public template variables $image_size and $columns (deprecated in version 0.3) */
		$image_size = $i['image_size'];
		$columns = $i['columns'];

		$function_args = $rpbt_args = $i;

		/* restricted arguments */
		unset( $function_args['fields'], $function_args['post_id'], $function_args['taxonomies'] );

		/* $related_posts varaiable for use in templates */
		$related_posts = (array) km_rpbt_related_posts_by_taxonomy( $rpbt_args['post_id'], $rpbt_args['taxonomies'], $function_args );

		/**
		 * Filter whether to hide the widget if no related posts are found.
		 *
		 * @since 0.1
		 *
		 * @param bool    $hide Whether to hide the widget if no related posts are found.
		 *                      Defaults to true.
		 */
		$hide_empty = (bool) apply_filters( 'related_posts_by_taxonomy_widget_hide_empty', true );

		if ( !$hide_empty || !empty( $related_posts ) ) {

			/* get the template depending on the format  */
			$template = km_rpbt_related_posts_by_taxonomy_template( (string) $rpbt_args['format'], $rpbt_args['type'] );

			if ( !$template ) {
				return;
			}

			/* display of the widget */
			echo $rpbt_widget_args['before_widget'];

			$rpbt_args['title'] = apply_filters( 'widget_title', $rpbt_args['title'], $rpbt_args, $this->id_base );

			/* show widget title if one was set. */
			if ( '' !== trim( $rpbt_args['title'] ) ) {
				echo $rpbt_widget_args['before_title'] . $rpbt_args['title'] . $rpbt_widget_args['after_title'];
			}

			/* clean up variables before calling the template */
			unset( $i, $instance_filter, $function_args, $hide_empty );

			global $post; // used for setup_postdata() in templates
			require $template;
			wp_reset_postdata(); // Clean up global $post variable;
			echo $rpbt_widget_args['after_widget'];
		}
	}


	/**
	 * Updates the widget settings.
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

		$i['columns'] = absint( strip_tags( $new_instance['columns'] ) );

		$i['singular_template'] = isset( $new_instance['singular_template'] ) ? (bool) $new_instance['singular_template'] : '';

		$post_id = absint( strip_tags( $new_instance['post_id'] ) );
		$i['post_id'] = ( $post_id  > 0 ) ? $post_id  : '';

		return $i;
	}


	/**
	 * Displays the widget form in /wp-admin/widgets.php.
	 *
	 * @since 0.1
	 */
	function form( $instance ) {

		$i = $instance;
		$default = $this->defaults;

		// back compat
		if ( !isset( $i['taxonomies'] ) ) {
			$i = $this->update_rpbt_widget( $i );
		}

		$i['title']             = ( isset( $i['title'] ) ) ? esc_attr( $i['title'] ) : 'Related Posts';
		$i['posts_per_page']    = ( isset( $i['posts_per_page'] ) ) ? (int) $i['posts_per_page'] : 5;
		$i['taxonomies']        = ( isset( $i['taxonomies'] ) ) ?  (string) $i['taxonomies'] : $default->all_tax;
		$i['format']            = ( isset( $i['format'] ) ) ?  (string) $i['format'] : 'links';
		$i['image_size']        = ( isset( $i['image_size'] ) ) ?  (string) $i['image_size'] : 'thumbnail';
		$i['columns']           = ( isset( $i['columns'] ) ) ? absint( $i['columns'] ) : 3;
		$i['singular_template'] = ( isset( $i['singular_template'] ) ) ? (bool) $i['singular_template'] : false;
		$i['post_id']           = ( isset( $i['post_id'] ) && $i['post_id'] ) ? absint( $i['post_id'] ) : '';

		/* since version 0.2.1 you can use -1 to display all posts */
		if ( -1 !== $i['posts_per_page'] ) {
			$i['posts_per_page'] = ( absint( $i['posts_per_page'] ) ) ? absint( $i['posts_per_page'] ) : 5;
		}

		if ( !isset( $i['post_types'] ) ) {
			$i['post_types']['post'] = 'on';
		}

		/* widget form fields */

		$after = "\n\t";
		$before = "\t";

		// title
		$field = "\t" . '<p class="rpbt_title"><label for="' . $this->get_field_id( 'title' ) . '">';
		$field .= __( 'Title', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<input id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" ';
		$title = $before . $field . 'type="text" value="' . $i['title'] . '" class="widefat" /></p>' . $after;

		// posts_per_page
		$field = '<p class="rpbt_posts_per_page"><label for="' . $this->get_field_id( 'posts_per_page' ) . '">';
		$field .= __( 'Number of related posts to show', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<input id="' . $this->get_field_id( 'posts_per_page' ) . '" name="' . $this->get_field_name( 'posts_per_page' ) . '" ';
		$field .= 'type="text" value="' . $i['posts_per_page'] . '" size="3" />';
		$field .= '<br/><span class="description">' . __( 'Use -1 to show all related posts.', 'related-posts-by-taxonomy' ) . '</span>';
		$posts_per_page = $before . $field . "</p>" . $after;

		// taxonomies
		$field = '<div class="rpbt_taxonomies"><h4>' . __( 'Taxonomies', 'related-posts-by-taxonomy' ) . ' </h4>';
		$field .= '<p><label for="' . $this->get_field_id( 'taxonomies' ) . '">';
		$field .= __( 'Taxonomy', 'related-posts-by-taxonomy' ) .': </label>';
		$field .= '<select name="' . $this->get_field_name( 'taxonomies' ) . '" id="' . $this->get_field_id( 'taxonomies' ) . '" class="widefat">';
		$field .= '<option value="' . $default->all_tax . '" ' . selected( $i['taxonomies'], $default->all_tax, false ) . '>';
		$field .= __( 'All Taxonomies', 'related-posts-by-taxonomy' ) . '</option>';
		foreach ( $default->taxonomies as $name => $label ) {
			$field .= '<option value="' . $name . '"' . selected( $i['taxonomies'], $name, false ) . '>' . $label . '</option>';
		}
		$taxonomies = $before . $field . '</select></p></div>' . $after;

		// post types
		$field = '<div class="rpbt_post_types"><h4>' . __( 'Post Types', 'related-posts-by-taxonomy' ) .' </h4><p>';
		foreach ( $default->post_types as $name => $label ) {
			$field .= '<input type="checkbox" class="checkbox" id="' . $this->get_field_id( 'post_types' ) . "_$name" . '" ';
			$field .= 'name="' . $this->get_field_name( 'post_types' ) . "[$name]" .'"';
			if ( isset( $i['post_types'][ $name ] ) && ( 'on' === $i['post_types'][ $name ] ) ) {
				$field .= ' checked="checked"';
			}
			$field .= ' /> <label for="' .$this->get_field_id( 'post_types'  ) . "_$name" . '">' . $label . '</label><br />';
		}
		$post_types = $before . $field . '</p></div>' . $after;

		// display
		$display = $before . '<h4 class="rpbt_widget_display_title">' . __( 'Display', 'related-posts-by-taxonomy' ) . '</h4>' . $after;

		// format
		$field = '<p class="rpbt_format"><label for="' . $this->get_field_id( 'format' ) . '">';
		$field .= __( 'Format', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<select name="' . $this->get_field_name( 'format' ) . '" id="' . $this->get_field_id( 'format' ) . '" class="widefat">';
		foreach ( $default->formats as $name => $label ) {
			$field .= '<option value="' . $name . '"' . selected( $i['format'], $name, false ) . '>' . $label . '</option>';
		}
		$format = $before . $field . '</select></p>' . $after;

		// image_size
		$field = '<p class="rpbt_image_size"><label for="' . $this->get_field_id( 'image_size' ) . '">';
		$field .= __( 'Image Size', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<select name="' . $this->get_field_name( 'image_size' ) . '" id="' . $this->get_field_id( 'image_size' ) . '" class="widefat">';
		foreach ( $default->image_sizes as $name => $label ) {
			$field .= '<option value="' . $name . '"' . selected( $i['image_size'], $name, false ) . '>' . $label . '</option>';
		}
		$image_size = $before . $field . '</select></p>' . $after;

		// columns
		$field = '<p class="rpbt_columns"><label for="' . $this->get_field_id( 'columns' ) . '">';
		$field .= __( 'Number of image columns', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<input id="' . $this->get_field_id( 'columns' ) . '" name="' . $this->get_field_name( 'columns' ) . '" ';
		$field .= 'type="text" value="' . $i['columns'] . '" size="3" />';
		$field .= '<br/><span class="description">' . __( 'Use 0 for no columns.', 'related-posts-by-taxonomy' ) . '</span>';
		$columns = $before . $field . '</p>' . $after;

		// singular_template
		$field = '<p class="rpbt_singular">';
		$field .= '<input class="checkbox" type="checkbox" ' . checked( $i['singular_template'], 1, false ) . ' ';
		$field .= 'id="' . $this->get_field_id( 'singular_template' ) . '" name="' . $this->get_field_name( 'singular_template' ) . '" />';
		$field .= ' <label for="' . $this->get_field_id( 'singular_template' ) . '">';
		$singular_template = $before . $field . __( 'Display widget on single post pages only', 'related-posts-by-taxonomy' ) . '</label></p>' . $after;

		// post_id
		$field = '<p class="rpbt_post_id"><label for="' . $this->get_field_id( 'post_id' ) . '">';
		$field .= __( 'Display related posts for post ID (optional)', 'related-posts-by-taxonomy' ) . ': </label>';
		$field .= '<input id="' . $this->get_field_id( 'post_id' ) . '" name="' . $this->get_field_name( 'post_id' ) . '" ';
		$post_id = $before . $field . 'type="text" value="' . $i['post_id'] . '" size="5" /></p>' . "\n";

		$pieces = array( 'title', 'posts_per_page', 'taxonomies', 'post_types', 'display', 'format', 'image_size', 'columns', 'singular_template', 'post_id' );

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


	/**
	 * Updates widget settings if taxonomies is not defined
	 *
	 * provides back compatiblity for upgading from version 0.2.1
	 * taxonomy changed to taxonomies in version 0.2.2
	 * image_size and columns were added in version 0.2.2
	 *
	 * @param array   $i Widget instance
	 * @return array    Widget instance
	 */
	function update_rpbt_widget( $i ) {

		if ( isset( $i['taxonomy'] ) && $i['taxonomy'] ) {
			$i['taxonomies'] = ( 'all_taxonomies' === $i['taxonomy'] ) ? $this->defaults->all_tax : $i['taxonomy'];
			unset( $i['taxonomy'] );
		} else {
			$i['taxonomies'] = $this->defaults->all_tax;
		}

		$i['image_size'] = ( isset( $i['image_size'] ) ) ? $i['image_size'] : 'thumbnail';
		$i['columns'] = ( isset( $i['columns'] ) ) ? $i['columns'] : 3;

		$instances = get_option( 'widget_' . $this->id_base );

		if ( isset( $instances[ $this->number ] ) ) {
			unset( $instances[ $this->number ]['taxonomy'] );
			$instances[ $this->number ]['taxonomies'] = $i['taxonomies'];
			$instances[ $this->number ]['image_size'] = $i['image_size'];
			$instances[ $this->number ]['columns'] = $i['columns'];
			update_option( 'widget_' . $this->id_base, $instances );
		}

		return $i;
	}

} // end Related_Posts_By_Taxonomy class