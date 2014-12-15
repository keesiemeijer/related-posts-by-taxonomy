=== Related Posts by Taxonomy ===
Contributors: keesiemeijer
Tags: posts,related,related posts,related thumbnails,similar,similar posts,widget,shortcode,taxonomy,taxonomies,post type,post types,category,categories,tag,tags,post thumbnail,post thumbnails,thumbnails,featured,featured image,image,images
Requires at least: 3.7
Tested up to: 4.1
Stable tag: 0.4.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This lightweight WordPress plugin provides a widget and shortcode to display related posts as thumbnails, links, excerpts or as full posts. 

== Description ==

Display related posts as thumbnails, links, excerpts or as full posts with a widget or shortcode. Posts with the **most terms in common** will display at the top. Use multiple taxonomies and post types to get the related posts. Include or exclude terms. Change the look and feel with your own html templates in your (child) theme.  

Plugin features:

* Widget and shortcode.
* Display post thumbnails, links, excerpts or full posts.
* Exclude or include terms.
* Exclude posts.
* Limit related posts by date.
* Display related posts on single post pages only.
* Automatically display related posts after the post content.
* Use your own templates for display of the related posts.
* Use plugin functions to get related posts in your theme templates.
* Filters to change the default behavior and display of the plugin.

For more information about the plugin see the [documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/).

Follow this plugin on [GitHub](https://github.com/keesiemeijer/related-posts-by-taxonomy).

To have the related posts automatically display after the post content without using the shortcode or widget see the [FAQ](http://wordpress.org/extend/plugins/related-posts-by-taxonomy/faq/). 

Default usage for the shortcode is:
<pre><code>[related_posts_by_tax]</code></pre>

Attributes for the shortcode are:

* post_id
* taxonomies
* post_types
* posts_per_page
* order
* orderby
* title
* before_title
* after_title
* exclude_terms
* include_terms
* exclude_posts
* format
* image_size
* columns
* caption
* limit_posts
* limit_year
* limit_month
* related

Example to show 10 related posts instead of the default 5.
<pre><code>[related_posts_by_tax posts_per_page="10"]</code></pre>

See the [documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/) for more information about the attributes and their defaults.
= Translations =
Dutch
French (by [Annie Stasse](http://www.artisanathai.fr))

== Installation ==
* Unzip the <code>related-posts-by-taxonomy.zip</code> folder.
* Upload the <code>related-posts-by-taxonomy</code> folder to your <code>/wp-content/plugins</code> directory.
* Activate *Related Posts by Taxonomy*.
* That's it, now you are ready to use the widget and shortcode

== Frequently Asked Questions ==

For more information on how to use the plugin see this [documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/).

= Can I include or exclude posts or terms with the widget or shortcode =
Yes. For the widget see [this filter](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/filters/#related_posts_by_taxonomy_widget_args) and for the shortcode see the [shortcode parameters](href="http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#shortcode-attributes).

= The widget only lets you choose "all taxonomies" or a single taxonomy. Can I make it use only the taxonomies I want? =

Yes. See [this filter](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/filters/#related_posts_by_taxonomy_widget_args) on how to do that.

= How can I automatically add related posts after the post content? =

Read the "[Adding Related Posts After the Post Content](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#after-content).
" section in the documentation.

== Screenshots ==

1. The Widget
2. Twenty Twelve screenshot. Post thumbnails (after post content) and the widget
3. Twenty Thirteen screenshot. Post thumbnails (after post content) and the widget

== Changelog ==
= 0.4.1 =
Updated 'include_terms' logic to search for related posts with different taxonomy terms if 'related' is set.
Updated unit tests to be more reliable.
= 0.4 =
* Enhancement
	* Added a new parameter 'related' to get posts with include_terms even if the current post doesn't have the included terms (unrelated). 
	* Used attribute aria-describedby in wp_get_attachment_image for accessibility (similar changes as in the WordPress 4.1 gallery).
	* Added new filters related_posts_by_taxonomy_post_thumbnail_link and related_posts_by_taxonomy_rss_post_thumbnail_link.
	* Removed extract() from all files (WordPress core best practices).
	* Added Phpunit tests (in the github repository).

* Bug fixes
	* Fixed a filter recursion bug for the shortcode if it's used in the WordPress the_content filter and the format is excerpts or posts.

* Notice
	* Deprecated filters related_posts_by_taxonomy_post_thumbnail and related_posts_by_taxonomy_rss_post_thumbnail.

= 0.3.1 =
* New filters for the related posts query.
* Fixed minor bug when using spaces in post_types string in the short code.
* Added plugin icons introduced with WordPress 4.0

= 0.3 =
* Enhancement
	* New parameters for the shortcode and widget: include_terms and caption.
	* New support for html5 tags (set by themes) in the thumbnail gallery (same as in WordPress 3.9 gallery shortcode).
	* Allow image columns to be set to zero (same as in WordPress gallery shortcode).
	* New options for gallery captions. Use the post title (default), excerpt, attachment caption or attachment alt text for the caption. 
	* Reformatted code and new inline filter documentation is added to adhere to new WordPress coding standards.
	* New filters for overriding images in the thumbnail gallery.

* Bug fixes
	* Fixed bug where duplicate related posts where found for order="RAND". Props: [Mock](https://profiles.wordpress.org/mock).
	* Fixed php notices coming from the Related_Posts_By_Taxonomy_Defaults class.

* Notice
	* Removed backward compatibility for WordPress 3.4.
	* The template used for display of the post thumbnails is updated. Review the changes made in the updated related-posts-thumbnails.php file if you're using it to display the thumbnails in your own theme.

= 0.2.1 =
* Added image sizes and columns for the widget and shortcode. 
* Cleaned up of code base and speed improvements.
* Added a Dutch translation.
* Added a filter for the caption of post thumbnails.
* Added two filters for a future settings page for this plugin. (this will be a separate plugin)

= 0.2 =
* Added new feature to display related post thumbnails.
* Removed the docs that came with the plugin.
* some minor bug fixing for the shortcode.

== Upgrade Notice ==
= 0.4.1 =
Tested with WordPress 4.1. A minor update with a more stable logic to retrieve related posts with the 'include_terms' parameter. 
