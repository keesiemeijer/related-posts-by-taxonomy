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
 * @param array   $related_posts Array with related post objects that have a post thumbnail.
 * @return string HTML content to display gallery.
 */
function km_rpbt_related_posts_by_taxonomy_gallery( $args, $related_posts = array() ) {

	$compatible = true;

	// check WordPress gallery_shortcode changes since 3.6
	global $wp_version;
	if ( version_compare( $wp_version, "3.5", "<" ) ) {
		$compatible = false;
	}

	// back compat
	if ( $compatible ) {
		$post = get_post();
	} else {
		global $post;
	}

	static $instance = 0;
	$instance++;

	if ( empty( $related_posts ) ) {
		return '';
	}

	// WordPress >= 3.9 supports html5 tags for the gallery shortcode
	$html5 = current_theme_supports( 'html5', 'gallery' );

	$defaults = array(
		'itemtag'    => $html5 ? 'figure'     : 'dl',
		'icontag'    => $html5 ? 'div'        : 'dt',
		'captiontag' => $html5 ? 'figcaption' : 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'caption'    => 'post_title', // 'post_title', 'post_excerpt', 'attachment_caption', attachment_alt, or a custom string
	);

	// arguments can be filtered by WordPress 3.6 and up
	// filter hook shortcode_atts_gallery. see shortcode_atts()
	$args = shortcode_atts( $defaults, $args, 'gallery' );

	/**
	 * Filter the function arguments.
	 *
	 * @since 0.2.1
	 *
	 * @param array   $args Function arguments. See $defaults above.
	 */
	$filtered_args = apply_filters( 'related_posts_by_taxonomy_gallery', $args );

	$args = array_merge( $defaults, (array) $filtered_args );
	extract( $args );

	// current post id
	$id = $post ? $post->ID : 0;
	$id = intval( $id );

	if ( is_feed() ) {
		$output = "\n";
		foreach ( (array) $related_posts as $related ) {

			$thumb_id = get_post_thumbnail_id(  $related->ID  );

			/**
			 * Filter the related post thumbnail ID in the feed.
			 *
			 * @since 0.2.2
			 *
			 * @param int     $thumb_id Attachment id of related post if found.
			 * @param object  $related  Related post object
			 * @param int     $id       Post id used to get the related post object.
			 */
			$thumb_id = apply_filters( 'related_posts_by_taxonomy_rss_attachment_id', $thumb_id, $related, $id );

			if ( (bool) $thumb_id ) {
				$url = get_permalink(  $related->ID );
				$post_title =  esc_attr(  $related->post_title );
				$link_text = wp_get_attachment_image( $thumb_id, $size );
				$output .=  "<a href='$url' title='$post_title'>$link_text</a>\n";
			}
		}
		return $output;
	}

	$itemtag = tag_escape( $itemtag );
	$captiontag = tag_escape( $captiontag );
	$icontag = tag_escape( $icontag );

	// validate tags in WordPress version >= 3.6
	if ( $compatible ) {
		$valid_tags = wp_kses_allowed_html( 'post' );
		if ( ! isset( $valid_tags[ $itemtag ] ) ) {
			$itemtag = 'dl';
		}
		if ( ! isset( $valid_tags[ $captiontag ] ) ) {
			$captiontag = 'dd';
		}
		if ( ! isset( $valid_tags[ $icontag ] ) ) {
			$icontag = 'dt';
		}
	}

	$columns = intval( $columns );
	$itemwidth = $columns > 0 ? floor( 100/$columns ) : 100;
	$float = is_rtl() ? 'right' : 'left';

	$selector = "gallery-{$instance}";

	$gallery_style = $gallery_div = '';

	/**
	 * Filter whether to print default gallery styles.
	 *
	 * @since 3.1.0
	 *
	 * @param bool    $print Whether to print default gallery styles.
	 *                       Defaults to false if the theme supports HTML5 galleries.
	 *                       Otherwise, defaults to true.
	 */
	if ( apply_filters( 'use_default_gallery_style', ! $html5 ) ) {
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
		</style>\n\t\t";
	}

	$size_class = sanitize_html_class( $size );
	$gallery_div = "<div id='$selector' class='gallery related-gallery related-galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
	$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

	$i = 0;

	foreach ( (array) $related_posts as $related ) {

		$thumb_id = get_post_thumbnail_id(  $related->ID  );

		/**
		 * Filter the related post thumbnail ID.
		 *
		 * Allows you to set your own thumbnail id. Return false to show no thumbnail.
		 *
		 * @since 0.2.2
		 *
		 * @param int     $thumb_id Attachment id of related post if found.
		 * @param object  $related  Related post object
		 * @param int     $id       Post id used to get the related post object.
		 */
		$thumb_id = apply_filters( 'related_posts_by_taxonomy_attachment_id', $thumb_id, $related, $id );
		$thumb_id = absint( $thumb_id );
		$image_output = '';
		$_caption = '';

		// check if post has a post thumbnail
		if ( (bool) $thumb_id ) {

			$url = get_permalink(  $related->ID );
			$post_title =  esc_attr(  $related->post_title );

			$gallery_image = wp_get_attachment_image( $thumb_id, $size );
			$image_output =  "<a href='$url' title='$post_title'>$gallery_image</a>";

			$image_meta  = wp_get_attachment_metadata( $thumb_id );

			$orientation = '';
			if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
				$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
			}

			$output .= "<{$itemtag} class='gallery-item'>";
			$output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_output
			</{$icontag}>";

			if ( 'post_title' === $caption ) {
				$_caption = $related->post_title;
			} elseif ( 'post_excerpt' === $caption ) {
				setup_postdata( $related );
				$_caption = apply_filters( 'the_excerpt', get_the_excerpt() );
				wp_reset_postdata();
			} elseif ( 'attachment_caption' === $caption ) {
				$attachment = get_post( $thumb_id );
				$_caption = $attachment->post_excerpt;
			} elseif ( 'attachment_alt' === $caption ) {
				$_caption = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
			}
			/**
			 * Filter the related post thumbnail caption.
			 *
			 * @since 0.2.2
			 *
			 * @param string  $caption  Options 'post_title', 'attachment_caption', attachment_alt, or a custom string. Default: post_title.
			 * @param object  $related  Related post object.
			 * @param object  $thumb_id Thumbnail id.
			 * @param int     $id       Post id used to get the related post object.
			 */
			$_caption = apply_filters( 'related_posts_by_taxonomy_caption',  $_caption, $related, $thumb_id, $id );
			$_caption = trim( wp_kses_post( $_caption ) );

			if ( $captiontag && $_caption ) {
				$output .= "
				<{$captiontag} class='wp-caption-text gallery-caption'>
				" . wptexturize( $_caption ) . "
				</{$captiontag}>";
			}
			$output .= "</{$itemtag}>";
			if ( ! $html5 && $columns > 0 && ++$i % $columns == 0 ) {
				$output .= '<br style="clear: both" />';
			}
		}

	}

	if ( ! $html5 && $columns > 0 && $i % $columns !== 0 ) {
		$output .= "
			<br style='clear: both' />";
	}

	$output .= "
		</div>\n";

	return $output;
}