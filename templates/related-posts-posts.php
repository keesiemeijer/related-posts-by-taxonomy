<?php
/**
 * Widget and shortcode template: posts template.
 *
 * This template is used by the plugin: Related Posts by Taxonomy.
 *
 * plugin:        https://wordpress.org/plugins/related-posts-by-taxonomy
 * Documentation: https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/
 *
 * @package Related Posts by Taxonomy
 * @since 0.1
 *
 * The following variables are available:
 *
 * @var array $related_posts Array with related posts objects or empty array.
 * @var array $rpbt_args     Widget or shortcode arguments.
 */
?>

<?php
/**
 * Note: global $post; is used before this template by the widget and the shortcode.
 */
?>

<?php if ( $related_posts ) : ?>
	<?php foreach ( $related_posts as $post ) :
		setup_postdata( $post ); ?>

		<a href="<?php the_permalink() ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a>
		<?php the_content(); ?>

	<?php endforeach; ?>
<?php else : ?>
<p><?php _e( 'No related posts found', 'related-posts-by-taxonomy' ); ?></p>
<?php endif ?>

<?php
/**
 * note: wp_reset_postdata(); is used after this template by the widget and the shortcode
 */
?>