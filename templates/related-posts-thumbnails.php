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
 * @var array  $related_posts Array with full related posts objects or empty array.
 * @var string $image_size    Image size (since 0.2.1).
 * @var string $columns       Columns for image gallery (since 0.2.1).
 */
?>

<?php
/**
 * Note: global $post; is run before this template by the widget and the shortcode.
 */
?>

<?php if ( $related_posts ) : ?>

<?php
// check if $colums is set
$columns = ( isset( $columns ) && $columns ) ? $columns : 3;

// check if $image_size is set
$image_size = ( isset( $image_size ) && $image_size ) ? $image_size : 'thumbnail';

// related posts gallery arguments
$args = array(
	'itemtag'    => 'dl',
	'icontag'    => 'dt',
	'captiontag' => 'dd',
	'columns'    => $columns,
	'size'       => $image_size,
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