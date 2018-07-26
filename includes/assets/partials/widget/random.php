<p class="rpbt_random">
	<input class="checkbox" type="checkbox" <?php checked( $i['random'], 1, true ); ?> id="<?php echo $this->get_field_id( 'random' ); ?>" name="<?php echo $this->get_field_name( 'random' ); ?>" />
	<label for="<?php echo $this->get_field_id( 'random' ); ?>">
		<?php _e( 'Randomize related posts.', 'related-posts-by-taxonomy' ); ?>
	</label>
</p>