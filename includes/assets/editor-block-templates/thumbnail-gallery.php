<?php
/**
 * Editor thumbnail gallery block.
 *
 * @package Related Posts by Taxonomy
 * @since 0.3
 *
 * The following variables are available:
 *
 * @var array  $related_posts Array with full related posts objects or empty array.
 * @var array  $rpbt_args     Array with widget or shortcode arguments.
 *
 * deprecated (since version 0.3)
 * @var string $image_size    Image size. (deprecated - use $rpbt_args['image_size'] instead)
 * @var string $columns       Columns.    (deprecated - use $rpbt_args['columns'] instead)
 */

?>

<?php
/**
 * Note: global $post; is used before this template by the widget and the shortcode.
 */
?>

<?php if ( $related_posts ) : ?>
	<?php
		$html5 = current_theme_supports( 'html5', 'gallery' );

		/**
		 * Arguments for km_rpbt_related_posts_by_taxonomy_gallery() function.
		 *
		 * Here we use the defaults for 'itemtag', 'icontag', 'captiontag'
		 */
		$args = array(

			'itemtag'       => $html5 ? 'figure' : 'dl',
			'icontag'       => $html5 ? 'div' : 'dt',
			'captiontag'    => $html5 ? 'figcaption' : 'dd',
			'id'            => $rpbt_args['post_id'],
			'columns'       => $rpbt_args['columns'],    // zero or positive number
			'size'          => $rpbt_args['image_size'], // 'thumbnail', 'medium', 'large', 'full' and custom sizes set by your theme
			'caption'       => $rpbt_args['caption'],    // 'post_title', 'post_excerpt' 'attachment_caption', attachment_alt, or a custom string
			'link_caption'  => $rpbt_args['link_caption'],
			'gallery_class' => '',
		);

		// Plugin function to display the galllery in /includes/functions-thumbnail.php
		echo km_rpbt_related_posts_by_taxonomy_gallery( $args, $related_posts );
	?>

<?php else : ?>
	<p><?php _e( 'No related posts found', 'related-posts-by-taxonomy' ); ?></p>
<?php endif; ?>

<?php
/**
 * Note: wp_reset_postdata(); is used after this template by the widget and the shortcode
 */
?>
