<p class="rpbt_post_id">
	<label for="<?php echo $this->get_field_id( 'post_id' ) ?>">
		<?php _e( 'Display related posts for post ID (optional)', 'related-posts-by-taxonomy' ); ?>: 
	</label>
	<input id="<?php echo $this->get_field_id( 'post_id' ); ?>" name="<?php echo $this->get_field_name( 'post_id' ) ?>" type="text" value="<?php echo $i['post_id']; ?>" size="5" />
</p>