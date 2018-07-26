<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Related posts by taxonomy thumbnail gallery.
 *
 * Similar to the WordPress gallery_shortcode().
 *
 * @since 0.2
 *
 * @global string $wp_version
 * @global string $post
 *
 * @param array $args          {
 *     Arguments of the related posts thumbnail gallery.
 *
 *     @type int          $id            Post ID.
 *     @type string       $itemtag       HTML tag to use for each image in the gallery.
 *                                       Default 'dl', or 'figure' when the theme registers HTML5 gallery support.
 *     @type string       $icontag       HTML tag to use for each image's icon.
 *                                       Default 'dt', or 'div' when the theme registers HTML5 gallery support.
 *     @type string       $captiontag    HTML tag to use for each image's caption.
 *                                       Default 'dd', or 'figcaption' when the theme registers HTML5 gallery support.
 *     @type boolean      $show_date     Whether to display the post date after the caption. Default false.
 *     @type int          $columns       Number of columns of images to display. Default 3.
 *     @type string|array $size          Size of the images to display. Accepts any valid image size. Default 'thumbnail'.
 *     @type string       $caption       Caption text for the post thumbnail.
 *                                       Accepts 'post_title', 'post_excerpt', 'attachment_caption', 'attachment_alt', or
 *                                       a custom string. Default 'post_title'
 *     @type boolean      $link_caption  Whether to link the caption to the related post. Default false.
 *     @type string       $gallery_class Default class for the gallery. Default 'gallery'.
 *     @type string       $type          Gallery type. Default gallery type 'rpbt_gallery'.
 * }
 * @param array $related_posts Array with related post objects that have a post thumbnail.
 * @return string HTML string of a gallery.
 */
function km_rpbt_related_posts_by_taxonomy_gallery( $args, $related_posts = array() ) {

	if ( empty( $related_posts ) ) {
		return '';
	}

	$post = get_post();

	static $instance = 0;
	$instance++;

	// WordPress >= 3.9 supports html5 tags for the gallery shortcode.
	$html5 = current_theme_supports( 'html5', 'gallery' );

	$defaults = array(
		'id'            => $post ? $post->ID : 0,
		'itemtag'       => $html5 ? 'figure' : 'dl',
		'icontag'       => $html5 ? 'div' : 'dt',
		'captiontag'    => $html5 ? 'figcaption' : 'dd',
		'show_date'     => false,
		'columns'       => 3,
		'size'          => 'thumbnail',
		'caption'       => 'post_title', // Use 'post_title', 'post_excerpt', 'attachment_caption', attachment_alt, or a custom string.
		'link_caption'  => false,
		'gallery_class' => 'gallery',
		'type'          => 'rpbt_gallery',
	);

	/* Can be filtered in WordPress > 3.5 (hook: shortcode_atts_gallery) */
	$args = shortcode_atts( $defaults, $args, 'gallery' );

	/**
	 * Filter the related post thumbnail gallery arguments.
	 *
	 * @since 0.2.1
	 *
	 * @param array $args Function arguments.
	 */
	$filtered_args = apply_filters( 'related_posts_by_taxonomy_gallery', $args );

	$args = array_merge( $defaults, (array) $filtered_args );

	$id = intval( $args['id'] );

	if ( is_feed() ) {
		$args['type'] = 'rpbt_gallery_feed';
		$output = "\n";
		foreach ( (array) $related_posts as $related ) {

			$thumbnail_id = get_post_thumbnail_id( $related->ID );
			$thumbnail    = wp_get_attachment_image( $thumbnail_id, $args['size'] );
			$permalink    = km_rpbt_get_permalink( $related->ID, $args );
			$title_attr   = apply_filters( 'the_title', esc_attr( $related->post_title ), $related->ID );

			$image_link = ( $thumbnail ) ? "<a href='$permalink' title='$title_attr'>$thumbnail</a>" : '';

			/**
			 * Filter the related post gallery image in the feed.
			 *
			 * @since 0.3
			 *
			 * @param string $post_thumbnail Html image tag or empty string.
			 * @param object $related        Related post object
			 * @param array  $args           Function arguments.
			 */
			$image_link = apply_filters( 'related_posts_by_taxonomy_rss_post_thumbnail_link', $image_link, $related, $args );

			if ( $image_link ) {
				$output .= $image_link . "\n";
			}
		}
		return $output;
	}

	$itemtag    = tag_escape( $args['itemtag'] );
	$captiontag = tag_escape( $args['captiontag'] );
	$icontag    = tag_escape( $args['icontag'] );
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

	$columns       = intval( $args['columns'] );
	$itemwidth     = $columns > 0 ? floor( 100 / $columns ) : 100;
	$float         = is_rtl() ? 'right' : 'left';
	$selector      = "rpbt-related-gallery-{$instance}";
	$gallery_class = is_string( $args['gallery_class'] ) ? trim( $args['gallery_class'] ) : 'gallery';
	$gallery_class = $gallery_class ? $gallery_class . ' ' : '';
	$gallery_style = '';

	/**
	 * Filter whether to print default gallery styles.
	 *
	 * Note: This is a WordPress core filter hook
	 *
	 * @since 3.1.0
	 *
	 * @param bool $print Whether to print default gallery styles.
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

	$size_class = sanitize_html_class( $args['size'] );
	$gallery_div = "<div id='$selector' class='{$gallery_class}related-gallery related-galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
	$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

	$i = 0;
	$item_output = '';

	foreach ( (array) $related_posts as $related ) {

		$caption       = '';
		$thumbnail_id  = get_post_thumbnail_id( $related->ID );
		$title         = apply_filters( 'the_title', $related->post_title, $related->ID );

		if ( 'post_title' === $args['caption'] ) {
			$date    = $args['show_date'] ? ' ' . km_rpbt_get_post_date( $related ) : '';
			$caption = $title . $date;

			if ( (bool) $args['link_caption'] ) {
				$caption = km_rpbt_get_post_link( $related, $args );
			}
		} elseif ( 'post_excerpt' === $args['caption'] ) {
			global $post;
			$post = $related;
			setup_postdata( $related );
			$caption = apply_filters( 'the_excerpt', get_the_excerpt() );
			wp_reset_postdata();
		} elseif ( $thumbnail_id && ( 'attachment_caption' === $args['caption'] ) ) {
			$attachment = get_post( $thumbnail_id );
			$caption = ( isset( $attachment->post_excerpt ) ) ? $attachment->post_excerpt : '';
		} elseif ( $thumbnail_id && ( 'attachment_alt' === $args['caption'] ) ) {
			$caption = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
		} else {
			$caption = (string) $args['caption'];
		}

		/**
		 * Filter the related post thumbnail caption.
		 *
		 * @since 0.3
		 *
		 * @param string $caption Options 'post_title', 'attachment_caption', attachment_alt, or a custom string. Default: post_title.
		 * @param object $related Related post object.
		 * @param array  $args    Function arguments.
		 */
		$caption = apply_filters( 'related_posts_by_taxonomy_caption',  wptexturize( $caption ), $related, $args );

		$describedby = ( trim( $caption ) ) ? array(
			'aria-describedby' => "{$selector}-{$related->ID}",
		) : '';
		$thumbnail   = wp_get_attachment_image( $thumbnail_id, $args['size'], false, $describedby );
		$permalink   = km_rpbt_get_permalink(  $related, $args );
		$title_attr  = esc_attr( $title );
		$image_link  = ( $thumbnail ) ? "<a href='$permalink' title='$title_attr'>$thumbnail</a>" : '';
		$image_attr  = compact( 'thumbnail_id', 'thumbnail', 'permalink', 'describedby', 'title_attr' );

		/**
		 * Filter the gallery image link.
		 *
		 * @since 0.3
		 *
		 * @param string $post_thumbnail Html image tag or empty string.
		 * @param object $related        Related post object
		 * @param array  $args           Function arguments.
		 */
		$image_link = apply_filters( 'related_posts_by_taxonomy_post_thumbnail_link', $image_link, $image_attr, $related, $args );

		if ( ! $image_link ) {
			continue;
		}

		/**
		 * Filter the related posts gallery item CSS classes.
		 *
		 * @since
		 *
		 * @param string $classes Classes used for a gallery item.
		 * @param object $related Related post object
		 * @param array  $args    Function arguments.
		 */
		$itemclass  = apply_filters( 'related_posts_by_taxonomy_gallery_item_class', 'gallery-item', $related, $args );
		$itemclass  = km_rpbt_get_post_classes( $related, $itemclass );
		$image_meta = wp_get_attachment_metadata( $thumbnail_id );

		$orientation = '';
		if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? 'portrait' : 'landscape';
		}

		$item_output .= "<{$itemtag} class='{$itemclass}'>";
		$item_output .= "
			<{$icontag} class='gallery-icon {$orientation}'>
				$image_link
			</{$icontag}>";

		if ( $captiontag && trim( $caption ) ) {
			$item_output .= "
				<{$captiontag} class='wp-caption-text gallery-caption' id='{$selector}-{$related->ID}'>
				" . $caption . "
				</{$captiontag}>";
		}
		$item_output .= "</{$itemtag}>";

		if ( ! $html5 && ( $columns > 0 ) && ( ++$i % $columns == 0 ) ) {
			$item_output .= '<br style="clear: both" />';
		}
	}

	if ( ! $item_output ) {
		return '';
	}

	$output .= $item_output;

	if ( ! $html5 && ( $columns > 0 ) && ( $i % $columns !== 0 ) ) {
		$output .= "
			<br style='clear: both' />";
	}

	$output .= "
		</div>\n";

	return $output;
}
