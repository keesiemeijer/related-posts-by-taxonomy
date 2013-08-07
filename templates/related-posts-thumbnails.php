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
 * @var array $related_posts Array with full related posts objects or empty array.
 */
?>

<?php
/**
 * Note: global $post; is run before this template by the widget and the shortcode.
 */
?>

<?php if ( $related_posts ) : ?>

<?php

	// all default related posts gallery arguments
	$args = array(
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
	);

// see the documentation of this plugin on how to use this function.
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