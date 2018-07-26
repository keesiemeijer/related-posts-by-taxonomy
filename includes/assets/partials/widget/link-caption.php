<p class="rpbt_link_caption">
	<input class="checkbox" type="checkbox" <?php checked( $i['link_caption'], 1, true ); ?> id="<?php echo $this->get_field_id( 'link_caption' ); ?>" name="<?php echo $this->get_field_name( 'link_caption' ); ?>" />
 	<label for="<?php echo $this->get_field_id( 'link_caption' ); ?>">
 		<?php _e( 'Link image captions to posts', 'related-posts-by-taxonomy' ); ?>
	</label>
</p>