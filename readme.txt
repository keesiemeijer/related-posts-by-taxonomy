=== Related Posts by Taxonomy ===
Contributors: keesiemeijer
Tags: related,related posts,widget,shortcode,taxonomy,taxonomies,post type,post types,term,terms,category,categories,tag,tags,post_tag,posts,similar,similar posts
Requires at least: 3.4
Tested up to: 3.6
Stable tag: 0.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This lightweight plugin provides a widget and shortcode to display related posts by taxonomies as links, full posts or excerpts. 

== Description ==

Choose from single or multiple taxonomies and search for related posts in multiple post types. Posts that have the most terms in common will display at the top (also ordered by date). It's easy to override the look of the widget or shortcode with a filter or by including your own templates in your theme. It's also possible to get the related posts by using a function in your theme template files.

For more information on how to use the plugin see [these instructions](http://keesiemeijer.wordpress.com/2013/05/11/related-posts-by-taxonomy-plugin/) or read the readme.html file that comes with the plugin (inside the 'docs' directory).

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
* limit_posts
* limit_year
* limit_month

Example to show 10 related posts instead of the default 5.
<pre><code>[related_posts_by_tax posts_per_page="10"]</code></pre>

Read the documentation for more information on these attributes and what they do. 

== Installation ==
* Unzip the <code>related-posts-by-taxonomy.zip</code> folder.
* Upload the <code>related-posts-by-taxonomy</code> folder to your <code>/wp-content/plugins</code> directory.
* Activate *Related Posts by Taxonomy*.
* That's it, now you are ready to use the widget and shortcode

== Frequently Asked Questions ==

For more information on how to use the plugin see [these instructions](http://keesiemeijer.wordpress.com/2013/05/11/related-posts-by-taxonomy-plugin/) or read the readme.html file that comes with the plugin (inside the 'docs' directory).

= Can I exclude posts and/or terms with the widget or shortcode =
Yes. Read the "Filters" or "Shortcode" section in the documentation that comes with the plugin.

= The widget only lets you choose "all taxonomies" or a single taxonomy. Can I make it use only the taxonomies I want? =

Yes. Read the "Filters" section in the documentation that comes with the plugin.

= How can I automatically add related posts after the post content? =

Read the "Adding Related Posts After the Post Content" section in the documentation that comes with the plugin.


== Screenshots ==

1. The Widget
