<p class="rpbt_columns">
	<label for="<?php echo $this->get_field_id( 'columns' ); ?>">
 		<?php _e( 'Number of image columns', 'related-posts-by-taxonomy' ); ?>: 
 	</label>
	<input id="<?php echo $this->get_field_id( 'columns' ); ?>" name="<?php echo $this->get_field_name( 'columns' ) ?>" value="<?php esc_attr_e( $i['columns'] ); ?>" size="3" class="tiny-text" step="1" min="0" type="number" />
</p>