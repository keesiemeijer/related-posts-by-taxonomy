<h4 class="rpbt_widget_image_display_title" <?php echo  $style; ?>>
	<?php _e( 'Image Display', 'related-posts-by-taxonomy' ); ?>
</h4>
<p class="rpbt_image_size">
	<label for="<?php echo $this->get_field_id( 'image_size' ); ?>">
		<?php _e( 'Image Size', 'related-posts-by-taxonomy' ); ?>: 
	</label>
	<select name="<?php echo $this->get_field_name( 'image_size' ); ?>" id="<?php echo $this->get_field_id( 'image_size' ); ?>" class="widefat">
		<?php foreach ( $plugin->image_sizes as $name => $label ) : ?>
			<option value="<?php esc_attr_e( $name ) ?>"<?php selected( $i['image_size'], $name, true ); ?>>
				<?php echo $label; ?>
			</option>'
		<?php endforeach; ?>
	</select>
</p>