<p class="rpbt_show_date">
	<input class="checkbox" type="checkbox"<?php checked( $i['show_date'], 1, true ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
	<label for="<?php echo $this->get_field_id( 'show_date' ); ?>">
		<?php _e( 'Display post date', 'related-posts-by-taxonomy' ); ?>
	</label>
</p>