=== Related Posts by Taxonomy ===
Contributors: keesiemeijer
Tags: related,related posts,widget,shortcode,taxonomy,taxonomies,post type,post types,category,categories,tags,similar,posts,thumbnails,post thumbnail,featured image
Requires at least: 3.4
Tested up to: 3.7
Stable tag: 0.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This lightweight WordPress plugin provides a widget and shortcode to display related posts as links, full posts, excerpts or post thumbnails. 

== Description ==

Display related posts as links, full posts, excerpts or post thumbnails with a widget or shortcode. Posts with the **most terms** in common will display at the top. Choose from single or multiple taxonomies and search for related posts in single or multiple post types. It’s easy to override the look of the widget or shortcode. It’s also possible to get the related posts by using plugin functions in your (child) theme template files.

Plugin features:

* Widget and shortcode
* Display related posts as links, full posts, excerpts or post thumbnails
* exclude terms from taxonomies
* exclude posts from the related posts
* limit related posts by year(s) or by month(s)
* only display related posts on single posts 
* display related posts after the post content
* use your own templates for display of the related posts


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
* exclude_posts
* format
* image_size
* columns
* limit_posts
* limit_year
* limit_month

Example to show 10 related posts instead of the default 5.
<pre><code>[related_posts_by_tax posts_per_page="10"]</code></pre>

Read the documentation for more information about the attributes.
= Translations =
Dutch

== Installation ==
* Unzip the <code>related-posts-by-taxonomy.zip</code> folder.
* Upload the <code>related-posts-by-taxonomy</code> folder to your <code>/wp-content/plugins</code> directory.
* Activate *Related Posts by Taxonomy*.
* That's it, now you are ready to use the widget and shortcode

== Frequently Asked Questions ==

For more information on how to use the plugin see this [documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/).

= Can I exclude posts and/or terms with the widget or shortcode =
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

= 0.2.1 =
* Added image sizes and columns to the widget and shortcode. 
* Cleaned up the codebase and made it faster.
* Added Dutch translation.
* Added a filter for the caption of post thumbnails.
* Added two filters for a future settings page for this plugin. (this will be a seperate plugin)

= 0.2 =
* Added new feature to display related post thumbnails.
* Removed the docs that came with the plugin.
* some minor bug fixing for the shortcode.

== Upgrade Notice ==

= 0.2.1 =
Added image sizes and image columns to the widget and shortcode. New filter to change the post thumbnail caption text. Cleaned up the codebase and made it faster. Also added the Dutch language translation and tested it up to WordPress 3.7.1.