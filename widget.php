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

	private $defaults;
	private $args;

	/**
	 * Widget setup.
	 *
	 * @since 0.1
	 */
	function Related_Posts_By_Taxonomy() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'related_posts_by_taxonomy', 'description' => __( 'Show a list of related posts by taxonomy.', 'related-posts-by-taxonomy' ) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'id_base' => 'related-posts-by-taxonomy' );

		/* Create the widget. */
		$this->WP_Widget( 'related-posts-by-taxonomy', __( 'Related Posts By Taxonomy', 'related-posts-by-taxonomy' ), $widget_ops, $control_ops );

		/* Get defaults for this plugin. */
		$this->defaults = Related_Posts_By_Taxonomy_Defaults::get_instance();


		if ( !is_admin() ) {

			/* hide widget on single posts pages if needed */
			add_filter( 'sidebars_widgets', array( $this, 'show_widget_only_on_single_post' ) );

			/* add 'km_rpbt_related_post_id' query var */
			add_filter( 'query_vars', array( $this, 'add_related_post_id' ) );
		}
	}


	/**
	 * Displays the widget on the screen.
	 *
	 * @since 0.1
	 */
	function widget( $widget_args, $instance ) {

		$this->args = $widget_args;

		/* just to make sure */
		if ( $instance['singular_template'] && !is_singular() )
			return;

		/* 	set up arguments */

		if ( empty( $instance['post_id'] ) )
			$instance['post_id'] = $this->get_the_ID();

		if ( !empty( $instance['post_types'] ) )
			$instance['post_types'] = array_keys( $instance['post_types'] );

		/* *
		 * back compat
		 * replaced $instance['taxonomy'] with $instance['taxonomies'] in version 0.2.1
		 */

		if ( !isset( $instance['taxonomies'] ) ) {

			$instance['taxonomies'] = ( isset( $instance['taxonomy'] ) ) ? $instance['taxonomy'] : $this->defaults->all_tax;

			if ( 'all_taxonomies' == $instance['taxonomies'] )
				$instance['taxonomies'] = $this->defaults->all_tax;

		}

		/* added two more options in 2.0.1 */
		$instance['image_size'] = ( isset( $instance['image_size'] ) ) ? $instance['image_size'] : 'thumbnail';
		$instance['columns'] = ( isset( $instance['columns'] ) ) ? $instance['columns'] : 3;

		/* end of back compatiblity */

		if ( $instance['taxonomies'] == $this->defaults->all_tax )
			$instance['taxonomies'] =  array_keys( $this->defaults->taxonomies );

		/* 	let user filter the arguments */

		$instance_filter = apply_filters( 'related_posts_by_taxonomy_widget_args', $instance, $this->args );
		$instance = array_merge( $instance, (array) $instance_filter );

		/* validate filtered arguments */

		if ( $instance['taxonomies'] == $this->defaults->all_tax )
			$instance['taxonomies'] =  array_keys( $this->defaults->taxonomies );

		if ( !in_array( $instance['format'], array_keys( $this->defaults->formats ) ) )
			$instance['format'] = 'links';

		$instance['post_thumbnail'] = false;
		if ( 'thumbnails' == $instance['format'] ) {
			$instance['post_thumbnail'] = true;

			if ( !in_array( $instance['image_size'], array_keys( $this->defaults->image_sizes )  ) )
				$instance['image_size'] = 'thumbnail';

			$image_size = $instance['image_size'];
			$columns = $instance['columns'];
		}

		/* function km_rpbt_related_posts_by_taxonomy arguments */

		$f_args = $instance;
		$f_defaults = array(
			'post_types', 'posts_per_page', 'order', // 'fields',
			'limit_posts', 'limit_year', 'limit_month',
			'orderby', 'exclude_terms', 'exclude_posts',
			'post_thumbnail', 'common_terms'
		);

		foreach ( $f_args as $arg => $value ) {
			if ( !in_array( $arg, $f_defaults ) )
				unset( $f_args[ $arg ] );
		}

		/* get related posts */
		$related_posts = (array) km_rpbt_related_posts_by_taxonomy( $instance['post_id'], $instance['taxonomies'], $f_args );

		/* if no related posts where found hide the widget? default: true */
		$hide_empty = (bool) apply_filters( 'related_posts_by_taxonomy_widget_hide_empty', true );

		if ( !$hide_empty || !empty( $related_posts ) ) {

			unset( $instance_filter, $f_args, $f_defaults, $arg, $value );

			$template = km_rpbt_related_posts_by_taxonomy_template( (string) $instance['format'], 'widget' );

			if ( $template ) {

				/* display of the widget */
				echo $this->args['before_widget'];

				$instance['title'] = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

				/* widget title if one was input. */
				if ( trim( $instance['title'] ) != '' )
					echo $this->args['before_title'] . $instance['title'] . $this->args['after_title'];

				global $post; // for setup_postdata in $template
				require $template;
				wp_reset_postdata(); // clean up global $post;
				echo $this->args['after_widget'];

			}
		}
	}


	/**
	 * Completely removes the widget from page requests other than singular
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
					if ( 'wp_inactive_widgets' == $widget_area || empty( $widget_list ) ) continue;
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
	 *
	 * @since 0.1
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		$posts_per_page = (int) strip_tags( $new_instance['posts_per_page'] );

		if ( -1 === $posts_per_page )
			$instance['posts_per_page'] = $posts_per_page;
		else
			$instance['posts_per_page'] = ( absint( $posts_per_page ) ) ? absint( $posts_per_page ) : 5;

		$instance['taxonomies'] = stripslashes( $new_instance['taxonomies'] );

		$instance['post_types'] = (array) $new_instance['post_types'];
		if (  empty( $instance['post_types'] ) )
			$instance['post_types']['post'] = 'on';

		$instance['format'] = (string) $new_instance['format'];
		if ( !in_array( $new_instance['format'], array_keys( $this->defaults->formats ) ) )
			$instance['format'] = 'links';

		$instance['image_size'] = stripslashes( $new_instance['image_size'] );
		if ( 'thumbnails' == $instance['format'] ) {
			$sizes = array_keys( $this->defaults->image_sizes );
			if ( !in_array( $instance['image_size'], $sizes ) )
				$instance['image_size'] = 'thumbnail';
		}

		$columns = absint( strip_tags( $new_instance['columns'] ) );
		$instance['columns'] = ( $columns > 0 ) ? $columns : 3;

		$instance['singular_template'] = (bool) $new_instance['singular_template'];

		$post_id = absint( strip_tags( $new_instance['post_id'] ) );
		$instance['post_id'] = ( $post_id  > 0 ) ? $post_id  : '';

		return $instance;
	}


	/**
	 * Displays the widget form.
	 *
	 * @since 0.1
	 */
	function form( $instance ) {

		$title             = ( isset( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : 'Related Posts';
		if ( !isset( $instance['taxonomies'] ) ) {
			// new or updated widget
			if ( isset( $instance['taxonomy'] ) && $instance['taxonomy'] )
				$instance['taxonomies'] = $instance['taxonomy'];
			else
				$instance['taxonomies'] = $this->defaults->all_tax;
		}
		$taxonomy          = ( isset( $instance['taxonomies'] ) ) ?  (string) $instance['taxonomies'] : $this->defaults->all_tax;
		$format            = ( isset( $instance['format'] ) ) ?  (string) $instance['format'] : 'links';
		$size              = ( isset( $instance['image_size'] ) ) ?  (string) $instance['image_size'] : 'thumbnail';
		$columns           = ( isset( $instance['columns'] ) ) ? absint( $instance['columns'] ) : 3;
		$singular_template = ( isset( $instance['singular_template'] ) ) ? (bool) $instance['singular_template'] : false;
		$post_id           = ( isset( $instance['post_id'] ) && $instance['post_id'] ) ? absint( $instance['post_id'] ) : '';

		$posts_per_page    = ( isset( $instance['posts_per_page'] ) ) ? (int) $instance['posts_per_page'] : 5;

		/* since 2.0.1 you can use -1 to display all posts */
		if ( -1 !== $posts_per_page )
			$posts_per_page = ( absint( $posts_per_page ) ) ? absint( $posts_per_page ) : 5;

		if ( !isset( $instance['post_types'] ) )
			$instance['post_types']['post'] = 'on';
?>

		<!-- Widget Form Fields -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'related-posts-by-taxonomy' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>"  />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php _e( 'Number of related posts to show:', 'related-posts-by-taxonomy' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="text" value="<?php echo $posts_per_page; ?>" size="3" />
			<br/><span class="description"><?php _e( 'Use -1 to show all related posts.', 'related-posts-by-taxonomy' ) ?></span>
		</p>

		<?php

		// (todo) let settings plugin add a field here
		do_action( 'related_posts_by_taxonomy_default_taxonomies', '', $instance, $this );

?>

		<p>
			<label for="<?php echo $this->get_field_id( 'taxonomies' ); ?>"><?php _e( 'Taxonomy:', 'related-posts-by-taxonomy' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'taxonomies' ); ?>" id="<?php echo $this->get_field_id( 'taxonomies' ); ?>" class="widefat">
				<option value="<?php echo $this->defaults->all_tax; ?>"<?php selected( $taxonomy, $this->defaults->all_tax ); ?>><?php _e( 'All Taxonomies', 'related-posts-by-taxonomy' ) ?></option>
				<?php foreach ( $this->defaults->taxonomies as $name => $label ) : ?>
				<option value="<?php echo $name; ?>"<?php selected( $taxonomy, $name ); ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<?php _e( 'Post types to include:', 'related-posts-by-taxonomy' ) ?><br />
			<?php

		foreach ( $this->defaults->post_types as $name => $label ) : ?>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'post_types' ) . "_$name"; ?>" name="<?php echo $this->get_field_name( 'post_types' ) . "[$name]"; ?>"
<?php if ( isset( $instance['post_types'][ $name ] ) && $instance['post_types'][ $name ] == 'on' ) { echo 'checked="checked"'; } ?> />
			<label for="<?php echo $this->get_field_id( 'post_types'  ) . "_$name"; ?>"><?php echo $label; ?></label><br />
			<?php endforeach; ?>
    	</p>

    	<h4><?php _e( 'Display', 'related-posts-by-taxonomy' ) ?></h4>

    	<p>
			<label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e( 'Format:', 'related-posts-by-taxonomy' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'format' ); ?>" id="<?php echo $this->get_field_id( 'format' ); ?>" class="widefat">
			<?php foreach ( $this->defaults->formats as $name => $label ) : ?>
				<option value="<?php echo $name; ?>"<?php selected( $format, $name ); ?>><?php echo $label; ?></option>
			<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e( 'Image size:', 'related-posts-by-taxonomy' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'image_size' ); ?>" id="<?php echo $this->get_field_id( 'image_size' ); ?>" class="widefat">
			<?php foreach ( $this->defaults->image_sizes as $name => $label ) : ?>
				<option value="<?php echo $name; ?>"<?php selected( $size, $name ); ?>><?php echo $label; ?></option>
			<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _e( 'Number of image columns:', 'related-posts-by-taxonomy' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ); ?>" type="text" value="<?php echo $columns; ?>" size="3" />
		</p>

		<p>
    		<?php _e( 'This widget needs a post ID for it to display the related posts. The post ID is available on single post pages or inside a loop. If the widget is outside the loop  (like in a sidebar) it gets the first post ID for that page request. (i.e. on a category archive page it will get the post ID from the first post of that category archive page).', 'related-posts-by-taxonomy' ); ?>
    	</p>

    	<p>
    		<input class="checkbox" type="checkbox" <?php checked( $singular_template, 1 ); ?> id="<?php echo $this->get_field_id( 'singular_template' ); ?>" name="<?php echo $this->get_field_name( 'singular_template' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'singular_template' ); ?>"><?php _e( 'Display this widget only on single post pages', 'related-posts-by-taxonomy' ); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'post_id' ); ?>"><?php _e( 'Display related posts for post ID (optional):', 'related-posts-by-taxonomy' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'post_id' ); ?>" name="<?php echo $this->get_field_name( 'post_id' ); ?>" type="text" value="<?php echo $post_id; ?>" size="5" />
		</p>

	<?php
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

			if ( isset( $wp_query->post->ID ) )
				$post_id =  $wp_query->post->ID;

			if ( isset( $wp_query->query_vars['km_rpbt_related_post_id'] ) )
				$post_id =  $wp_query->query_vars['km_rpbt_related_post_id'];
		}

		return $post_id;
	}

} // end Related_Posts_By_Taxonomy class
