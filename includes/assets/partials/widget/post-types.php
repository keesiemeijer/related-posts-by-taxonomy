<div class="rpbt_post_types">
	<h4<?php echo $style; ?>>
		<?php _e( 'Post Types', 'related-posts-by-taxonomy' ); ?>
	</h4>
	<p>
		<?php foreach ( $plugin->post_types as $name => $label ) : ?>
			<?php
				$checked = '';
				if ( isset( $i['post_types'][ $name ] ) && ( 'on' === $i['post_types'][ $name ] ) ) {
					$checked = ' checked="checked"';
				} 
			?>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'post_types' ) . '_' . $name; ?>" name="<?php echo $this->get_field_name( 'post_types' ) . "[$name]"; ?>"<?php echo $checked; ?> />
			<label for="<?php echo $this->get_field_id( 'post_types' ) . '_' . $name; ?>">
				<?php echo $label; ?>
			</label><br />
		<?php endforeach; ?>
	</p>
</div>