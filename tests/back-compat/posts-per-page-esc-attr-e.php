<?php
/**
 * In plugin version 2.7.9 and below the widget posts_per_page field used esc_attr_e() instead of esc_attr() to escape the value.
 * This file uses the old esc_attr_e() to escape the value and should only be used in PHPUnit tests.
 */
?>
<p class="rpbt_posts_per_page">
	<label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>">
		<?php _e( 'Number of related posts to show', 'related-posts-by-taxonomy' ); ?>	
	</label>
	<input id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" value="<?php echo esc_attr_e( $i['posts_per_page'] ); ?>" size="3" class="tiny-text" step="1" min="-1" type="number" />
	<br/>
	<span class="description">
		<?php _e( 'Use -1 to show all related posts.', 'related-posts-by-taxonomy' ); ?>
	</span>
</p>