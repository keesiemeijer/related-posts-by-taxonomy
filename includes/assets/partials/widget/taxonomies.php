<div class="rpbt_taxonomies">
	<h4<?php echo $style ?>><?php _e( 'Taxonomies', 'related-posts-by-taxonomy' ); ?></h4>
	<p>
		<label for="<?php echo $this->get_field_id( 'taxonomies' ); ?>">
			<?php _e( 'Taxonomy', 'related-posts-by-taxonomy' ); ?>: 
		</label>
		<select name="<?php echo $this->get_field_name( 'taxonomies' ); ?>" id="<?php echo $this->get_field_id( 'taxonomies' ) ?>" class="widefat">
			<option value="<?php esc_attr_e( $plugin->all_tax ) ?>" <?php selected( $i['taxonomies'], $plugin->all_tax, true ) ?>>
				<?php  _e( 'All Taxonomies', 'related-posts-by-taxonomy' ); ?>
			</option>
			<?php foreach ( $plugin->taxonomies as $name => $label ) : ?>
				<option value="<?php esc_attr_e( $name ) ?>"<?php selected( $i['taxonomies'], $name, true ) ?>>
					<?php echo $label ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
</div>