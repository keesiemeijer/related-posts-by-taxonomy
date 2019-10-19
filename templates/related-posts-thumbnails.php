<?php
/**
 * Widget and shortcode template: post thumbnails template
 *
 * This template is used by the plugin: Related Posts by Taxonomy.
 *
 * plugin:        https://wordpress.org/plugins/related-posts-by-taxonomy
 * Documentation: https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/
 *
 * Only edit this file after you've copied it to your (child) theme's related-post-plugin folder.
 * See: https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/templates/
 *
 * @package Related Posts by Taxonomy
 * @since 0.3
 *
 * The following variables are available:
 *
 * @var array $related_posts Array with related post objects or related post IDs.
 *                           Empty array if no related posts are found.
 * @var array $rpbt_args     Array with widget, shortcode, rest API or editor block arguments.
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
		 * Use the $rpbt_args from the shortcode, widget or rest API.
		 *
		 * See plugin function documentation.
		 * https://keesiemeijer.github.io/related-posts-by-taxonomy/functions/km_rpbt_related_posts_by_taxonomy_gallery
		 */

		echo km_rpbt_related_posts_by_taxonomy_gallery( $rpbt_args, $related_posts );
	?>

<?php else : ?>
	<p><?php km_rpbt_no_posts_found_notice( $rpbt_args ); ?></p>
<?php endif; ?>

<?php
/**
 * Note: wp_reset_postdata(); is used after this template by the widget and the shortcode
 */
?>
