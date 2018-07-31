<p class="rpbt_title">
	<label for="<?php echo $this->get_field_id( 'title' ); ?>">
		<?php _e( 'Title', 'related-posts-by-taxonomy' ); ?>: 
	</label>
	<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo  $this->get_field_name( 'title' ); ?>" type="text" value="<?php  esc_attr_e( $i['title'] ); ?>" class="widefat" />
</p>
