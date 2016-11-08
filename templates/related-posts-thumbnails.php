<?php
/**
 * Widget and shortcode template: post thumbnails template
 *
 * This template is used by the plugin: Related Posts by Taxonomy.
 *
 * plugin:        https://wordpress.org/plugins/related-posts-by-taxonomy
 * Documentation: https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/
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
	/**
	 * Arguments for km_rpbt_related_posts_by_taxonomy_gallery() function.
	 *
	 * use the defaults 'itemtag', 'icontag', 'captiontag'
	 */
	$args = array(

		// 'itemtag'    => 'dl',
		// 'icontag'    => 'dt',
		// 'captiontag' => 'dd',
		
		'id'           => $rpbt_args['post_id'],
		'columns'      => $rpbt_args['columns'],    // zero or positive number
		'size'         => $rpbt_args['image_size'], // 'thumbnail', 'medium', 'large', 'full' and custom sizes set by your theme
		'caption'      => $rpbt_args['caption'],    // 'post_title', 'post_excerpt' 'attachment_caption', attachment_alt, or a custom string
		'link_caption' => $rpbt_args['link_caption'],
	);

// Plugin function in /includes/functions-thumbnail.php
echo km_rpbt_related_posts_by_taxonomy_gallery( $args, $related_posts );
?>

<?php else : ?>
<p><?php _e( 'No related posts found', 'related-posts-by-taxonomy' ); ?></p>
<?php endif; ?>

<?php
/**
 * note: wp_reset_postdata(); is used after this template by the widget and the shortcode
 */
?>