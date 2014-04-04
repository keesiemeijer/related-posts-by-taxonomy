=== Related Posts by Taxonomy ===
Contributors: keesiemeijer
Tags: related,related posts,widget,shortcode,taxonomy,taxonomies,post type,post types,category,categories,tags,similar,posts,thumbnails,post thumbnail,featured image
Requires at least: 3.4
Tested up to: 3.9-beta3
Stable tag: 0.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This lightweight WordPress plugin provides a widget and shortcode to display related posts as links, post thumbnails, full posts or excerpts. 

== Description ==

Display related posts as links, post thumbnails, full posts or excerpts. Posts with the **most terms** in common will display at the top. Choose from single or multiple taxonomies and search for related posts in single or multiple post types. The plugin provides an easy way to change the look of the widget or shortcode with your own template and html markup. Or use plugin functions in your (child) theme template files to get the related posts.

Plugin features:

* Widget and shortcode.
* Display related posts as links, post thumbnails, full posts or excerpts.
* Exclude or include terms.
* Exclude posts.
* Limit related posts by year(s) or by month(s).
* Only display related posts on single posts.
* Display related posts after the post content.
* Use your own templates for display of the related posts.
* Lots of filters to change default behaviour or display.

For more information on how to use the plugin see the [documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/).

If you want to have related posts after the post content on single post pages without using the shortcode or widget see the [FAQ](http://wordpress.org/extend/plugins/related-posts-by-taxonomy/faq/). 

Default usage of the shortcode is:
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
* limit_posts
* limit_year
* limit_month

Example to show 10 related posts instead of the default 5.
<pre><code>[related_posts_by_tax posts_per_page="10"]</code></pre>

Read the [documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/) for more information about the attributes and their defaults.
= Translations =
Dutch

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
= 0.2.2 =
* New parameters: include_terms and caption.
* New filter output options for post thumbnail gallery captions.
* New support for html5 tags (set by themes) in the post thumbnail gallery (same as in WordPress 3.9 gallery shortcode).
* Notice: The template used for the post thumbnails is updated.
* New default post type for the shortcode. Now the shortcode defaults to the post type from the post to get the related posts for.
* Reformatted code to adhere to new WordPress coding standarts.
* New inline filter documentation.
* New filters for even more control of the plugin.
* Fixed bug where duplicate related posts where found for order="RAND". Props: Mock.

= 0.2.1 =
* Added image sizes and columns to the widget and shortcode. 
* Cleaned up the codebase and made it faster.
* Added Dutch translation.
* Added a filter for the caption of post thumbnails.
* Added two filters for a future settings page for this plugin. (this will be a separate plugin)

= 0.2 =
* Added new feature to display related post thumbnails.
* Removed the docs that came with the plugin.
* some minor bug fixing for the shortcode.

== Upgrade Notice ==
= 0.2.2 =
This version is tested up to WordPress 3.9-beta and adds a new parameters include_terms and caption.
The post thumbnail gallery (function )is updated for html5 tags support (same as in WordPress 3.9 gallery shortcode). Notice - The template used for post thumbnail display is also updated (backwards compatible).  
The shortcode post type now defaults to the post type of the current post. 
Code is reformatted to adhere to new WordPress coding standarts. 
Fixed a bug where duplicate related posts where found for order="RAND". Props: Mock.

= 0.2.1 =
Added image sizes and image columns to the widget and shortcode. New filter to change the post thumbnail caption text. Cleaned up the codebase and made it faster. Also added the Dutch language translation and tested it up to WordPress 3.7.1.