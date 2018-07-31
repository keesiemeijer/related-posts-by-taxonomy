<h4 class="rpbt_display_title"<?php echo $style; ?>>
	<?php _e( 'Display', 'related-posts-by-taxonomy' ); ?>
</h4>
<p class="rpbt_format">
	<label for="<?php echo $this->get_field_id( 'format' ); ?>">
		<?php _e( 'Format', 'related-posts-by-taxonomy' ) ?>: 
	</label>
	<select name="<?php echo $this->get_field_name( 'format' ); ?>" id="<?php echo $this->get_field_id( 'format' ); ?>" class="widefat">
		<?php foreach ( $plugin->formats as $name => $label ) : ?>
			<option value="<?php esc_attr_e( $name ); ?>"<?php selected( $i['format'], $name, true ); ?>>
				<?php echo $label; ?>
			</option>
		<?php endforeach; ?>
	</select>
</p>