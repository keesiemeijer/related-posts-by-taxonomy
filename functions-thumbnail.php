<?php
/**
 * Related Posts by Taxonomy Gallery.
 *
 * Altered WordPress gallery_shortcode() for displaying the related post thumbnails.
 *
 * @since 0.2
 *
 * @global string $wp_version
 * @global string $post
 * @param array   $args          Attributes of the shortcode.
 * @param array   $related_posts Array with related post objects.
 * @return string HTML content to display gallery.
 */
function km_rpbt_related_posts_by_taxonomy_gallery( $args, $related_posts = array() ) {

	$compatible = true;

	global $wp_version;
	if ( version_compare( $wp_version, "3.5", "<" ) )
		$compatible = false;

	// back compat
	if ( $compatible )
		$post = get_post();
	else
		global $post;

	static $instance = 0;
	$instance++;

	if ( empty( $related_posts ) )
		return '';

	$defaults = array(
		'id'         => $post ? $post->ID : 0,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
	);

	// attributes can be filtered by WordPress 3.6 and up
	$args = shortcode_atts( $defaults, $args, 'gallery' );

	// specific related post gallery filter for all versions
	$filtered_args = apply_filters( 'related_posts_by_taxonomy_gallery', $args );

	$args = array_merge( $defaults, (array) $filtered_args );
	extract( $args );

	$id = intval( $id );

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $related_posts as $related ) {
			$thumb_id = get_post_thumbnail_id(  $related->ID  );
			$url = get_permalink(  $related->ID );
			$post_title =  esc_attr(  $related->post_title );
			$link_text = wp_get_attachment_image( $thumb_id, $size );
			$output .=  "<a href='$url' title='$post_title'>$link_text</a>\n";
		}
		return $output;
	}

	$itemtag = tag_escape( $itemtag );
	$captiontag = tag_escape( $captiontag );
	$icontag = tag_escape( $icontag );

	// back compat
	if ( $compatible ) {
		$valid_tags = wp_kses_allowed_html( 'post' );
		if ( ! isset( $valid_tags[ $itemtag ] ) )
			$itemtag = 'dl';
		if ( ! isset( $valid_tags[ $captiontag ] ) )
			$captiontag = 'dd';
		if ( ! isset( $valid_tags[ $icontag ] ) )
			$icontag = 'dt';
	}

	$columns = intval( $columns );
	$itemwidth = $columns > 0 ? floor( 100/$columns ) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';
	if ( apply_filters( 'use_default_gallery_style', true ) )
		$gallery_style = "
		<style type='text/css'>
			#{$selector} {
				margin: auto;
			}
			#{$selector} .gallery-item {
				float: {$float};
				margin-top: 10px;
				text-align: center;
				width: {$itemwidth}%;
			}
			#{$selector} img {
				border: 2px solid #cfcfcf;
			}
			#{$selector} .gallery-caption {
				margin-left: 0;
			}
			/* see gallery_shortcode() in wp-includes/media.php */
		</style>";
	$size_class = sanitize_html_class( $size );
	$gallery_div = "<div id='$selector' class='gallery related-gallery related-galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
	$output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

	$i = 0;

	foreach ( $related_posts as $related ) {
		$thumb_id = get_post_thumbnail_id(  $related->ID  );
		$image_output = '';

		if ( $id ) {
			$url = get_permalink(  $related->ID );
			$post_title =  esc_attr(  $related->post_title );
			$link_text = wp_get_attachment_image( $thumb_id, $size );

			$image_output =  "<a href='$url' title='$post_title'>$link_text</a>";
			$image_meta  = wp_get_attachment_metadata( $thumb_id );

			$orientation = '';
			if ( isset( $image_meta['height'], $image_meta['width'] ) )
				$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';

			$output .= "<{$itemtag} class='gallery-item'>";
			$output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_output
			</{$icontag}>";
			if ( $captiontag && trim(  $related->post_title ) ) {

				$caption = apply_filters( 'related_posts_by_taxonomy_caption', wptexturize( $related->post_title ), $related );
				if ( $caption ) {
					$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption'>
				" . $caption . "
				</{$captiontag}>";
				}
			}
			$output .= "</{$itemtag}>";
			if ( $columns > 0 && ++$i % $columns == 0 )
				$output .= '<br style="clear: both" />';
		}
	}

	$output .= "
			<br style='clear: both;' />
		</div>\n";

	return $output;
}