<h4 class="rpbt_widget_display_title"<?php echo $style;  ?>>
	<?php _e( 'Widget Display', 'related-posts-by-taxonomy' ); ?>
</h4>
<p class="rpbt_singular_template">
	<input class="checkbox" type="checkbox" <?php checked( $i['singular_template'], 1, true ); ?> id="<?php echo $this->get_field_id( 'singular_template' ); ?>" name="<?php echo $this->get_field_name( 'singular_template' ); ?>" />
	<label for="<?php echo $this->get_field_id( 'singular_template' ) ?>">
		<?php _e( 'Display this widget on single post pages only', 'related-posts-by-taxonomy' ); ?>
	</label>
</p>