<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default gallery arguments.
 *
 * @since 2.7.0
 *
 * @param integer $post_id Optional. post ID. Default 0.
 * @return Default gallery arguments.
 */
function km_kpbt_get_default_gallery_args( $post_id = 0 ) {
	$html5 = current_theme_supports( 'html5', 'gallery' );

	return array(
		'id'             => absint( $post_id ),
		'size'           => 'thumbnail',
		'itemtag'        => $html5 ? 'figure' : 'dl',
		'icontag'        => $html5 ? 'div' : 'dt',
		'captiontag'     => $html5 ? 'figcaption' : 'dd',
		'show_date'      => false,
		'columns'        => 3,
		'caption'        => 'post_title', // Use 'post_title', 'post_excerpt', 'attachment_caption', attachment_alt, or a custom string.
		'link_caption'   => false,
		'gallery_type'   => 'rpbt_gallery',
		'gallery_class'  => 'gallery',
		'gallery_format' => '', // empty string or 'editor_block'
		'post_class'     => '',
		'image_crop'     => true, // Block editor default
		'type'           => '',
	);
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
 *     @type int          $id             Post ID.
 *     @type string       $itemtag        HTML tag to use for each image in the gallery.
 *                                        Default 'dl', or 'figure' when the theme registers HTML5 gallery support.
 *     @type string       $icontag        HTML tag to use for each image's icon.
 *                                        Default 'dt', or 'div' when the theme registers HTML5 gallery support.
 *     @type string       $captiontag     HTML tag to use for each image's caption.
 *                                        Default 'dd', or 'figcaption' when the theme registers HTML5 gallery support.
 *     @type boolean      $show_date      Whether to display the post date after the caption. Default false.
 *     @type int          $columns        Number of columns of images to display. Default 3.
 *     @type string|array $size           Size of the images to display. Accepts any valid image size. Default 'thumbnail'.
 *     @type string       $caption        Caption text for the post thumbnail.
 *                                        Accepts 'post_title', 'post_excerpt', 'attachment_caption', 'attachment_alt', or
 *                                        a custom string. Default 'post_title'
 *     @type boolean      $link_caption   Whether to link the caption to the related post. Default false.
 *     @type string       $gallery_format HTML format for the gallery. Accepts `editor_block` or empty string.
 *                                        Default empty string.
 *     @type string       $gallery_class  Default class for the gallery. Default 'gallery'.
 *     @type string       $post_class     CSS Class for gallery items. Default empty string.
 *     @type string       $gallery_type   Gallery type. Default 'rpbt_gallery'.
 *     @type string       $type           Feature type. (shortcode, widget, wp_rest_api)
 * }
 * @param array $related_posts Array with related post objects that have a post thumbnail.
 * @return string HTML string of a gallery.
 */
function km_rpbt_related_posts_by_taxonomy_gallery( $args, $related_posts = array() ) {
	if ( empty( $related_posts ) ) {
		return '';
	}

	static $instance = 0;
	$instance++;

	$post     = get_post();
	$post_id  = isset( $post->ID ) ? $post->ID : 0;
	$defaults = km_kpbt_get_default_gallery_args( $post_id );

	// Back compat with WP gallery_shortcode() and plugin filters.
	$args['id']   = isset( $args['id'] ) ? $args['id'] : $defaults['id'];
	$args['id']   = isset( $args['post_id'] ) ? $args['post_id'] : $args['id'];

	$format = isset( $args['gallery_format'] ) && $args['gallery_format'];
	if ( $format && ( 'editor_block' === $args['gallery_format'] ) ) {
		// Default class for block editor galleries
		$defaults['gallery_class'] = 'wp-block-gallery';
	}

	$args['size'] = isset( $args['size'] ) ? $args['size'] : $defaults['size'];
	$args['size'] = isset( $args['image_size'] ) ? $args['image_size'] : $args['size'];

	$args_raw = $args;

	/* Filter hook: shortcode_atts_gallery */
	$args = shortcode_atts( $defaults, $args, 'gallery' );
	$args = array_merge( $defaults, $args );

	/**
	 * Filter the related post thumbnail gallery arguments.
	 *
	 * @since 0.2.1
	 *
	 * @param array $args     Function arguments.
	 * @param array $args_raw Function arguments before filters.
	 */
	$filtered_args = apply_filters( 'related_posts_by_taxonomy_gallery', $args, $args_raw );
	$args          = array_merge( $defaults, (array) $filtered_args );

	if ( is_feed() ) {
		$args['gallery_type'] = 'rpbt_gallery_feed';
		$output = "\n";
		foreach ( (array) $related_posts as $related ) {
			$related = is_object( $related ) ? $related : get_post( $related );
			if ( ! isset( $related->ID, $related->post_title ) ) {
				continue;
			}

			$attachment_id = get_post_thumbnail_id( $related->ID );
			$image         = wp_get_attachment_image( $attachment_id, $args['size'] );
			$permalink     = km_rpbt_get_permalink( $related->ID, $args );
			$title_attr    = apply_filters( 'the_title', esc_attr( $related->post_title ), $related->ID );

			$image_link = ( $image ) ? "<a href='$permalink' title='$title_attr'>$image</a>" : '';

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

	if ( 'editor_block' === $args['gallery_format'] ) {
		return km_rpbt_get_gallery_editor_block_html( $related_posts, $args );
	}

	return km_kpbt_get_gallery_shortcode_html( $related_posts, $args, $instance );
}

/**
 * Gallery HTML similar to the WordPress gallery shortcode.
 *
 * @since 2.7.0
 *
 * @param array   $related_posts Array with related post objects that have a post thumbnail.
 * @param array   $args          Otional arguments. See km_rpbt_related_posts_by_taxonomy_gallery() for
 *                               accepted arguments.
 * @param integer $instance      Gallery instance number for gallery CSS ids.
 * @return string Gallery HTML.
 */
function km_kpbt_get_gallery_shortcode_html( $related_posts, $args = array(), $instance = 0 ) {
	if ( empty( $related_posts ) ) {
		return '';
	}

	$valid_tags    = wp_kses_allowed_html( 'post' );
	$args          = km_rpbt_validate_gallery_args( $args, $valid_tags );
	$instance      = absint( $instance );
	$html5         = current_theme_supports( 'html5', 'gallery' );
	$float         = is_rtl() ? 'right' : 'left';
	$selector      = "rpbt-related-gallery-{$instance}";
	$gallery_style = '';
	$itemwidth     = $args['columns'] > 0 ? floor( 100 / $args['columns'] ) : 100;

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
		$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

		$gallery_style = "
		<style{$type_attr}>
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

	$size_class    = sanitize_html_class( $args['size'] );
	$gallery_class = km_rpbt_sanitize_classes( $args['gallery_class'] );
	$gallery_class = $gallery_class ? $gallery_class . ' ' : '';

	$gallery_div = "<div id='$selector' class='{$gallery_class}related-gallery related-galleryid-{$args['id']} gallery-columns-{$args['columns']} gallery-size-{$size_class}'>";

	/** This filter is documented in wp-includes/media.php */
	$output = apply_filters( 'gallery_style', $gallery_style . $gallery_div );

	$i = 0;
	$item_output = '';

	foreach ( (array) $related_posts as $related ) {
		$related = is_object( $related ) ? $related : get_post( $related );
		if ( ! isset( $related->ID, $related->post_title ) ) {
			continue;
		}

		$attachment_id  = get_post_thumbnail_id( $related->ID );
		$caption        = km_rpbt_get_gallery_image_caption( $attachment_id, $related, $args );
		$describedby    = ( trim( $caption ) ) ? array(
			'aria-describedby' => "{$selector}-{$related->ID}",
		) : '';

		$role = 'figure';
		if ( 'figure' === $args['itemtag'] ) {
			$role = 'group';
		}

		$label = esc_attr( strip_tags( $caption ) );
		if ( empty( $label ) ) {
			$label = __( 'Gallery image', 'related-posts-by-taxonomy' );
		}

		$atts = " role='{$role}' aria-label='{$label}'";

		$image_link = km_rpbt_get_gallery_image_link( $attachment_id, $related, $args, $describedby );
		if ( ! $image_link ) {
			continue;
		}

		$itemclass  = km_rpbt_get_gallery_post_class( $related, $args, 'gallery-item' );
		$itemclass  = $itemclass ? " class='{$itemclass}'" : '';
		$image_meta = wp_get_attachment_metadata( $attachment_id );

		$orientation = '';
		if ( isset( $image_meta['height'], $image_meta['width'] ) ) {
			$orientation = ( $image_meta['height'] > $image_meta['width'] ) ? ' portrait' : ' landscape';
		}

		$item_output .= "<{$args['itemtag']}{$itemclass}{$atts}>";

		$item_output .= "
			<{$args['icontag']} class='gallery-icon{$orientation}'>
				$image_link
			</{$args['icontag']}>";

		if ( $args['captiontag'] && trim( $caption ) ) {
			$item_output .= "
				<{$args['captiontag']} class='wp-caption-text gallery-caption' id='{$selector}-{$related->ID}'>
				" . $caption . "
				</{$args['captiontag']}>";
		}
		$item_output .= "</{$args['itemtag']}>";

		if ( ! $html5 && ( $args['columns'] > 0 ) && ( ++$i % $args['columns'] == 0 ) ) {
			$item_output .= '<br style="clear: both" />';
		}
	}

	if ( ! $item_output ) {
		return '';
	}

	$output .= $item_output;

	if ( ! $html5 && ( $args['columns'] > 0 ) && ( $i % $args['columns'] !== 0 ) ) {
		$output .= "
			<br style='clear: both' />";
	}

	$output .= "
		</div>\n";

	return $output;
}

/**
 * Gallery HTML similar to the Gutenberg gallery block.
 *
 * @since 2.7.0
 * @param array $related_posts Array with related post objects that have a post thumbnail.
 * @param array $args          Otional arguments. See km_rpbt_related_posts_by_taxonomy_gallery() for
 *                             accepted arguments.
 * @return string Gallery HTML
 */
function km_rpbt_get_gallery_editor_block_html( $related_posts, $args = array(), $describedby = '' ) {
	if ( empty( $related_posts ) ) {
		return '';
	}

	$html = '';
	$args = km_rpbt_validate_gallery_args( $args );

	// Default to 1 if columns is 0. (zero is allowed for the normal gallery)
	$args['columns'] = ( 0 === $args['columns'] ) ? 1 : $args['columns'];

	foreach ( (array) $related_posts as $related ) {
		$related = is_object( $related ) ? $related : get_post( $related );
		if ( ! isset( $related->ID, $related->post_title ) ) {
			continue;
		}

		$attachment_id  = get_post_thumbnail_id( $related->ID );
		$caption        = km_rpbt_get_gallery_image_caption( $attachment_id, $related, $args );

		$label = esc_attr( strip_tags( $caption ) );
		if ( empty( $label ) ) {
			$label = __( 'Gallery image', 'related-posts-by-taxonomy' );
		}

		$image = km_rpbt_get_gallery_image_link( $attachment_id, $related, $args );
		if ( ! $image ) {
			continue;
		}

		$post_class = km_rpbt_get_gallery_post_class( $related, $args, 'blocks-gallery-item' );
		$post_class = $post_class ? ' class="' . $post_class . '"' : '';

		$html .= "<li{$post_class}>\n<figure role='figure' aria-label='$label'>\n{$image}\n";
		if ( $caption ) {
			$html .= '<figcaption class="blocks-gallery-item__caption">' . $caption . "</figcaption>\n";
		}
		$html .= "</figure>\n</li>\n";
	}

	if ( ! $html ) {
		return '';
	}

	$gallery_class = km_rpbt_sanitize_classes( $args['gallery_class'] );

	// The 'gallery' CSS selector is used by normal WP galleries.
	$gallery_class = ( 'gallery' === $gallery_class  ) ? 'wp-block-gallery' : $gallery_class;
	$gallery_class = $gallery_class ? $gallery_class . ' ' : '';

	$class = "{$gallery_class}rpbt-related-block-gallery columns-{$args['columns']}";
	$class .= $args['image_crop'] ? ' is-cropped' : '';

	$label = __( 'Gallery images', 'related-posts-by-taxonomy' );
	$atts  = 'class="' . $class  . '" role="group" aria-label="' . $label . '"';

	$html = '<ul class="blocks-gallery-grid">' . "\n{$html}</ul>";
	$html = "<figure {$atts}>\n{$html}\n</figure>\n";


	if ( function_exists( 'wp_filter_content_tags' ) ) {
		// since WP 5.5
		$html = wp_filter_content_tags( $html );
	} elseif ( function_exists( 'wp_make_content_images_responsive' ) ) {
		// since WP 4.4.0
		$html = wp_make_content_images_responsive( $html );
	}

	return $html;
}

/**
 * Validation of gallery arguments.
 *
 * @since 2.7.0
 *
 * @param array $args Arguments to validate. See km_rpbt_related_posts_by_taxonomy_gallery() for
 *                    accepted arguments.
 * @return array Validated arguments.
 */
function km_rpbt_validate_gallery_args( $args, $valid_tags = array() ) {
	$defaults = km_kpbt_get_default_gallery_args();
	$args     = array_merge( $defaults, $args );

	$args['id']         = absint( $args['id'] );
	$args['itemtag']    = tag_escape( $args['itemtag'] );
	$args['captiontag'] = tag_escape( $args['captiontag'] );
	$args['icontag']    = tag_escape( $args['icontag'] );

	if ( ! isset( $valid_tags[ $args['itemtag'] ] ) ) {
		$args['itemtag'] = 'dl';
	}
	if ( ! isset( $valid_tags[ $args['captiontag'] ] ) ) {
		$args['captiontag'] = 'dd';
	}
	if ( ! isset( $valid_tags[ $args['icontag'] ] ) ) {
		$args['icontag'] = 'dt';
	}

	$args['columns']       = absint( $args['columns'] );
	$args['caption']       = is_string( $args['caption'] ) ? $args['caption'] : 'post_title';
	$args['gallery_class'] = is_string( $args['gallery_class'] ) ? $args['gallery_class'] : 'gallery';

	$args = km_rpbt_validate_booleans( $args, $defaults );

	return $args;
}

/**
 * CSS class for gallery items.
 *
 * @since 2.7.0
 *
 * @param array  $related_posts Array with related post objects that have a post thumbnail.
 * @param array  $args          Otional arguments. See km_rpbt_related_posts_by_taxonomy_gallery() for
 *                               accepted arguments.
 * @param string $default_class Default CSS class for gallery items. Default empty string.
 * @return string CSS classes for gallery items.
 */
function km_rpbt_get_gallery_post_class( $related, $args, $default_class = '' ) {
	$defaults      = km_kpbt_get_default_gallery_args();
	$args          = array_merge( $defaults, $args );
	$default_class = sanitize_html_class( $default_class );

	/**
	 * Filter the related posts gallery item CSS classes.
	 *
	 * Use this filter to remove the `gallery-item` class if you need to.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classes Classes used for a gallery item. Default 'gallery-item'
	 * @param object $related Related post object
	 * @param array  $args    Gallery arguments.
	 */
	$itemclass  = apply_filters( 'related_posts_by_taxonomy_gallery_item_class', $default_class, $related, $args );
	$itemclass .= is_string( $args['post_class'] ) ? ' ' . $args['post_class'] : '';

	$args['post_class'] = trim( $itemclass );
	return km_rpbt_get_post_classes( $related, $args );
}

/**
 * Get the gallery item link.
 *
 * @since 2.7.0
 *
 * @param int    $attachment_id Thumbnail ID.
 * @param object $related       Related post object.
 * @param array  $args          Otional arguments. See km_rpbt_related_posts_by_taxonomy_gallery() for
 *                             accepted arguments.
 * @param array  $describedby   Array with aria-describedby attribute.
 * @return string HTML link for a gallery item.
 */
function km_rpbt_get_gallery_image_link( $attachment_id, $related, $args = array(), $describedby = '' ) {
	$defaults = km_kpbt_get_default_gallery_args();
	$args     = array_merge( $defaults, $args );

	$block_format = ( 'editor_block' === $args['gallery_format'] );
	if ( $block_format ) {
		$image = km_rpbt_get_editor_block_image( $attachment_id, $args );
	} else {
		$image = wp_get_attachment_image( $attachment_id, $args['size'], false, $describedby );
	}

	$permalink = km_rpbt_get_permalink( $related, $args );

	$title = '';
	if ( isset( $related->post_title, $related->ID ) ) {
		$title = apply_filters( 'the_title', $related->post_title, $related->ID );
	}

	$image_link = ( $image && $permalink ) ? "<a href='$permalink'>$image</a>" : '';

	// back compat
	$title_attr   = esc_attr( $title );
	$thumbnail    = $image;
	$thumbnail_id = $attachment_id;

	$image_attr = compact( 'thumbnail_id', 'thumbnail', 'permalink', 'describedby', 'title_attr' );

	/**
	 * Filter the gallery image link.
	 *
	 * @since 0.3
	 *
	 * @param string $image_link Html image link or empty string.
	 * @param array  $image_attr Image attributes.
	 * @param object $related    Related post object
	 * @param array  $args       Widget, shortcode, rest API or editor block arguments.
	 */
	return apply_filters( 'related_posts_by_taxonomy_post_thumbnail_link', $image_link, $image_attr, $related, $args );
}

/**
 * Image HTML similar to the Gutenberg gallery block images.
 *
 * @since 2.7.0
 *
 * @param int   $attachment_id Thumbnail ID.
 * @param array $args          Otional arguments. See km_rpbt_related_posts_by_taxonomy_gallery() for
 *                             accepted arguments.
 * @return string Image HTML string.
 */
function km_rpbt_get_editor_block_image( $attachment_id, $args = array() ) {
	$defaults = km_kpbt_get_default_gallery_args();
	$args     = array_merge( $defaults, $args );
	$html     = '';

	$image = wp_get_attachment_image_src( $attachment_id, $args['size'] );
	if ( ! ( isset( $image[0] ) && $image[0] ) ) {
		return '';
	}

	$image_full = $image;
	if ( 'full' !== $args['size'] ) {
		$image_full = wp_get_attachment_image_src( $attachment_id, 'full' );
	}

	$image_full = isset( $image_full[0] ) && $image_full[0] ? $image_full[0] : '';
	$image_link = get_attachment_link( $attachment_id );
	$alt        = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );

	$html .= '<img src="' . esc_attr( $image[0] ) . '"';
	$html .= $alt ? ' alt="' . esc_attr( $alt ) . '"' : '';
	$html .= ' data-id="' . esc_attr( $attachment_id ) . '"';
	$html .= $image_full ? ' data-full-url="' . esc_attr( $image_full ) . '"' : '';
	$html .= $image_link ? ' data-link="' . esc_attr( $image_link ) . '"' : '';
	$html .= ' class="wp-image-' . esc_attr( $attachment_id ) . '" />';

	return $html;
}

/**
 * Get the gallery image caption
 *
 * @since 2.7.0
 *
 * @param int    $attachment_id Thumbnail ID.
 * @param object $related       Related post object.
 * @param array  $args          Otional arguments. See km_rpbt_related_posts_by_taxonomy_gallery() for
 *                             accepted arguments.
 * @return string Image caption.
 */
function km_rpbt_get_gallery_image_caption( $attachment_id, $related, $args = array() ) {
	$defaults      = km_kpbt_get_default_gallery_args();
	$args          = array_merge( $defaults, $args );
	$caption       = '';
	$attachment_id  = absint( $attachment_id );

	$title = '';
	if ( isset( $related->post_title, $related->ID ) ) {
		$title = apply_filters( 'the_title', $related->post_title, $related->ID );
	}

	if ( 'post_title' === $args['caption'] ) {
		$date    = $args['show_date'] ? ' ' . km_rpbt_get_post_date( $related ) : '';
		$caption = $title ? $title . $date : '';

		if ( (bool) $args['link_caption'] ) {
			$caption = km_rpbt_get_post_link( $related, $args );
		}
	} elseif ( 'post_excerpt' === $args['caption'] ) {
		global $post;
		$post = $related;
		setup_postdata( $related );
		$caption = apply_filters( 'the_excerpt', get_the_excerpt() );
		wp_reset_postdata();
	} elseif ( $attachment_id && ( 'attachment_caption' === $args['caption'] ) ) {
		$attachment = get_post( $attachment_id );
		$caption = ( isset( $attachment->post_excerpt ) ) ? $attachment->post_excerpt : '';
	} elseif ( $attachment_id && ( 'attachment_alt' === $args['caption'] ) ) {
		$caption = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
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
	return apply_filters( 'related_posts_by_taxonomy_caption',  wptexturize( $caption ), $related, $args );
}
