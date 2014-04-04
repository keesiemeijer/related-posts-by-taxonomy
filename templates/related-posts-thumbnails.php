<?php
/**
 * Template for Related Posts by Taxonomy widget and shortcode - post thumbnail template
 * This template uses a function that comes whith the plugin. See the documentation
 * See http://codex.wordpress.org/Post_Thumbnails if you want to use your own html markup.
 * See the documentation on how you can use your own templates.
 *
 * @since 0.2
 *
 * @package related posts by taxonomy
 *
 * The following variables are available:
 * @var array  $related_posts Array with full related posts objects or empty.
 * @var array  $rpbt_args     Array with widget or shortcode arguments.
 * @var string $image_size    Image size. (deprecated - use $rpbt_args['image_size'])
 * @var string $columns       Columns.    (deprecated - use $rpbt_args['columns'])
 */
?>

<?php
/**
 * Note: global $post; is run before this template by the widget and the shortcode.
 */
?>

<?php if ( $related_posts ) : ?>

<?php
	/**
	 * Arguments for km_rpbt_related_posts_by_taxonomy_gallery().
	 *
	 * function documentation: http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/functions/#km_rpbt_related_posts_by_taxonomy_gallery
	 * defaults set by theme 'itemtag', 'icontag', 'captiontag'
	 */
	$args = array(

		// 'itemtag'    => 'dl',
		// 'icontag'    => 'dt',
		// 'captiontag' => 'dd',

		'columns'    => $rpbt_args['columns'], // positive integer
		'size'       => $rpbt_args['image_size'], // 'thumbnail', 'medium', 'large', 'full' and custom sizes set by your theme
		'caption'    => $rpbt_args['caption'], // 'post_title', 'post_excerpt' 'attachment_caption', attachment_alt, or a custom string
	);

// see the documentation.
echo km_rpbt_related_posts_by_taxonomy_gallery( $args, $related_posts );
?>

<?php else : ?>
<p><?php _e( 'No related posts found', 'related-posts-by-taxonomy' ); ?></p>
<?php endif; ?>

<?php
/**
 * note: wp_reset_postdata(); is run after this template by the widget and the shortcode
 */
?>