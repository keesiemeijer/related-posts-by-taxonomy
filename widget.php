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

	private $formats;
	private $taxonomies;

	/**
	 * Widget setup.
	 */
	function Related_Posts_By_Taxonomy() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'related_posts_by_taxonomy', 'description' => __( 'Show a list of related posts by taxonomy.', 'related-posts-by-taxonomy' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'id_base' => 'related-posts-by-taxonomy' );

		/* Create the widget. */
		$this->WP_Widget( 'related-posts-by-taxonomy', __( 'Related Posts By Taxonomy', 'related-posts-by-taxonomy' ), $widget_ops, $control_ops );
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects', 'and' );

		$tax = array();
		foreach ( (array) $taxonomies as $key => $value ) {
			$tax[$key] = $value->label;
		}
		
		$this->taxonomies = $tax;

		$this->formats = array(
			'links' => __( 'links', 'related-posts-by-taxonomy' ),
			'posts' => __( 'posts', 'related-posts-by-taxonomy' ),
			'excerpts' => __( 'excerpts', 'related-posts-by-taxonomy' )
		);

		if ( !is_admin() )
			add_filter( 'sidebars_widgets', array( $this, 'show_widget_only_on_single_post' ) );
	}


	/**
	 * How to display the widget on the screen.
	 *
	 * @since 0.1
	 */
	function widget( $args, $instance ) {
		extract( $args );
		$args_related =  array();

		/* just to make sure */
		if ( $instance['singular_template'] && !is_singular() )
			return;

		/* 	set up 	the args for km_rpbt_related_posts_by_taxonomy() */
		$args_related['post_id'] = $instance['post_id'];
		if ( empty( $instance['post_id'] ) ) {
			
			if ( in_the_loop() ) {
				$args_related['post_id'] = get_the_ID();
			} else {
				/* 
				 * not inside the loop 
				 * try to get the first post id off $wp_query (outside of the loop) 
				 */
				global $wp_query;
				if ( isset( $wp_query->posts[0]->ID ) )
					$args_related['post_id'] = $wp_query->posts[0]->ID;
				else
					$args_related['post_id'] = get_the_ID();
			}
		}

		if ( !empty( $instance['post_types'] ) )
			$args_related['post_types'] = array_keys( $instance['post_types'] );

		$args_related['taxonomies'] = $instance['taxonomy'];
		if ( $instance['taxonomy'] == 'all_taxonomies' ) {
			$args_related['taxonomies'] =  array_keys( $this->taxonomies );
		}

		if ( !empty( $instance['posts_per_page'] ) )
			$args_related['posts_per_page'] = $instance['posts_per_page'];

		$instance['widget_id'] = $args['widget_id'];

		$args_related = apply_filters( 'related_posts_by_taxonomy_widget_args', $args_related, $instance );
		
		$post_id = ( isset( $args_related['post_id'] ) ) ? $args_related['post_id'] : ''; // maybe filtered
		if ( isset( $args_related['taxonomies'] ) ) { // maybe filtered
			$taxonomies = $args_related['taxonomies'];
		} else {
			$taxonomies = array_keys( $this->taxonomies ); // all taxonomies
		}
		unset( $args_related['post_id'], $args_related['taxonomy'] ); // not needed

		/* get related posts */
		$related_posts = (array) km_rpbt_related_posts_by_taxonomy( $post_id, $taxonomies, $args_related );

		/* if no related posts where found show the widget? default: true */
		$show_empty = (bool) apply_filters( 'related_posts_by_taxonomy_widget_hide_empty', true );

		if ( !$show_empty || !empty( $related_posts ) ) {
			$template = km_rpbt_related_posts_by_taxonomy_template( (string) $instance['format'], 'widget' );
			if ( $template ) {

				/* display of the widget */
				echo $before_widget;
				$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
				/* widget title if one was input. */
				if ( trim( $title ) != '' )
					echo $before_title . $title . $after_title;
				global $post; // for setup_postdata in $template
				require $template;
				wp_reset_postdata(); // clean up global $post;
				echo $after_widget;

			}
		}
	}


	/**
	 * Completely removes the widget from page requests other that singular
	 * if 'singular_template' is set.
	 *
	 * @since 0.1
	 */
	function show_widget_only_on_single_post( $sidebars_widgets ) {
		if ( !is_singular() ) {
			$id_base = $this->id_base;
			$widget_args = get_option( 'widget_' . $id_base );
			if ( $widget_args && $id_base ) {
				foreach ( (array) $sidebars_widgets as $widget_area => $widget_list ) {
					if ( $widget_area == 'wp_inactive_widgets' || empty( $widget_list ) ) continue;
					foreach ( $widget_list as $pos => $widget_id ) {
						if ( preg_match( '/' . $id_base . '-/', $widget_id ) ) {
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
				}
			}
		}
		return  $sidebars_widgets;
	}


	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		$posts_per_page = absint( strip_tags( $new_instance['posts_per_page'] ) );
		$instance['posts_per_page'] = ( $posts_per_page > 0 ) ? $posts_per_page : 5;

		$instance['taxonomy'] = stripslashes( $new_instance['taxonomy'] );

		if ( in_array( $new_instance['format'], array_keys( $this->formats ) ) ) {
			$instance['format'] = $new_instance['format'];
		} else {
			$instance['format'] = 'links';
		}

		$instance['post_types'] = (array) $new_instance['post_types'];
		if (  empty( $instance['post_types'] ) ) {
			$instance['post_types']['post'] = 'on';
		}

		$instance['singular_template'] = (bool) $new_instance['singular_template'];

		$post_id = absint( strip_tags( $new_instance['post_id'] ) );
		$instance['post_id'] = ( $post_id  > 0 ) ? $post_id  : '';

		return $instance;
	}


	/**
	 * Displays the widget form.
	 */
	function form( $instance ) {

		$post_types = get_post_types( array( 'public' => true, '_builtin' => false ), 'objects', 'and' );
		$post_types = array( 'post' => get_post_type_object( 'post' ) ) + $post_types;

		/* Set up some default widget settings. */
		$defaults = array(
			'title' => __( 'Related Posts', 'related-posts-by-taxonomy' ),
			'posts_per_page' => 5,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$posts_per_page  = isset( $instance['posts_per_page'] ) ? (int) $instance['posts_per_page']  : 5;
		$selected_tax = ( isset( $instance['taxonomy'] ) ) ?  (string) $instance['taxonomy'] : 'all_taxonomies';
		$selected_format = ( isset( $instance['format'] ) ) ?  (string) $instance['format'] : 'links';
		if ( !isset( $instance['post_types'] ) ) {
			$instance['post_types']['post'] = 'on';
		}
		$singular_template = isset( $instance['singular_template'] ) ? (bool) $instance['singular_template'] : false;
		$post_id = isset( $instance['post_id'] ) ? $instance['post_id'] : '';
?>

		<!-- Widget Form Fields -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'related-posts-by-taxonomy' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"  />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php _e( 'Number of related posts to show:', 'related-posts-by-taxonomy' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="text" value="<?php echo $posts_per_page; ?>" size="3" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'Taxonomy:', 'related-posts-by-taxonomy' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" class="widefat">
			<option value="all_taxonomies"<?php selected( $selected_tax, 'all_taxonomies' ); ?>><?php _e( 'All Taxonomies', 'related-posts-by-taxonomy' ) ?></option>
			<?php foreach ( $this->taxonomies as $name => $label ) : ?>
				<option value="<?php echo $name; ?>"<?php selected( $selected_tax, $name ); ?>><?php echo $label; ?></option>
			<?php endforeach; ?>
			</select>
		</p>

		<p>
		<?php _e( 'Post types to include:', 'related-posts-by-taxonomy' ) ?><br />
		<?php
		$post_type_names = array_keys( wp_list_pluck( $post_types , 'name' ) );
		$i = 0;
		foreach ( $post_types as $pt ) : ?>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'post_types' ) . "[$post_type_names[$i]]"; ?>" name="<?php echo $this->get_field_name( 'post_types' ) . "[$post_type_names[$i]]"; ?>"
<?php if ( isset( $instance['post_types'][ $pt->name ] ) && $instance['post_types'][ $pt->name ] == 'on' ) { echo 'checked="checked"'; } ?> />
		<label for="<?php echo $this->get_field_id( 'post_types'  ) . "[$post_type_names[$i]]"; ?>"><?php echo $pt->label; ?></label><br />
    <?php ++$i; ?>
		<?php endforeach; ?>
    </p>

    <h4><?php _e( 'Display', 'related-posts-by-taxonomy' ) ?></h4>

    <p>
			<label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e( 'Format:', 'related-posts-by-taxonomy' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'format' ); ?>" id="<?php echo $this->get_field_id( 'format' ); ?>" class="widefat">
			<?php foreach ( array_keys( $this->formats ) as $format ) : ?>
				<option value="<?php echo $format; ?>"<?php selected( $selected_format, $format ); ?>><?php echo $format; ?></option>
			<?php endforeach; ?>
			</select>
		</p>

    <p><?php _e( 'This widget needs a post ID for it to display the related posts. The post ID is available on single post pages or inside a loop. If the widget is outside the loop  (like in a sidebar) it gets the first post ID for that page request. (i.e. on a category archive page it will get the post ID from the first post of that category archive page).', 'related-posts-by-taxonomy' ); ?></p>
    <p>
    <input class="checkbox" type="checkbox" <?php checked( $singular_template, 1 ); ?> id="<?php echo $this->get_field_id( 'singular_template' ); ?>" name="<?php echo $this->get_field_name( 'singular_template' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'singular_template' ); ?>"><?php _e( 'Display only on single post pages' ); ?></label>
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'post_id' ); ?>"><?php _e( 'Display related posts for post ID (optional):' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'post_id' ); ?>" name="<?php echo $this->get_field_name( 'post_id' ); ?>" type="text" value="<?php echo $post_id; ?>" size="5" />
		</p>

	<?php
	} // end form

} // end Related_Posts_By_Taxonomy class