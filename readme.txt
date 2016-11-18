=== Related Posts by Taxonomy ===
Contributors: keesiemeijer
Tags: posts,related,related posts,related thumbnails,similar,similar posts,widget,shortcode,taxonomy,taxonomies,post type,post types,category,categories,tag,tags,post thumbnail,post thumbnails,thumbnails,featured,featured image,image,images
Requires at least: 4.0
Tested up to: 4.7
Stable tag: 2.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This lightweight plugin lets you easily display related posts in a sidebar or after posts as thumbnails, links, excerpts or full posts.

== Description ==

Quickly increase your readers' engagement by adding related posts in the sidebar or after post content with a widget or shortcode.

Posts with the **most terms in common** will display at the top! 

This plugin is capable of finding related posts in multiple **taxonomies** and **post types**. Include or exclude terms from the search for related posts. Change the look and feel by using your own templates in your (child) theme.

[plugin documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/)

Plugin features:

* Widget and Shortcode.
* Display related posts as **post thumbnails**, links, excerpts or full posts.
* **Small Footprint**. Doesn't slow down your site!
* Object **and** persistent cache to get the related posts.
* Automatic display of related posts after the post content.
* Search for related posts in single or multiple **taxonomies** and **post types**.
* **Exclude** or **include** terms and posts.
* **Limit the search** of related posts by date or number.
* Use your own **HTML templates** for display of the related posts.
* Use **plugin functions** in your theme templates to display related posts yourself.
* Use Filters to **change the default behavior** of the plugin. 
* Extensive **[plugin documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/)**.
* Highly Adjustable!
* Follows WordPress coding standards and plugin best practices.

Follow this plugin on [GitHub](https://github.com/keesiemeijer/related-posts-by-taxonomy).

See the [FAQ](http://wordpress.org/extend/plugins/related-posts-by-taxonomy/faq/) to have related posts automatically display after the post content without using the shortcode or widget. 

Default usage for the shortcode is:
<pre><code>[related_posts_by_tax]</code></pre>

Attributes for the shortcode are:

* `post_id`
* `taxonomies`
* `post_types`
* `posts_per_page`
* `order`
* `orderby`
* `before_shortcode`
* `after_shortcode`
* `title`
* `before_title`
* `after_title`
* `exclude_terms`
* `include_terms`
* `exclude_posts`
* `format`
* `image_size`
* `columns`
* `caption`
* `link_caption`
* `limit_posts`
* `limit_year`
* `limit_month`
* `related`

Example to show 10 related posts instead of the default 5.
<pre><code>[related_posts_by_tax posts_per_page="10"]</code></pre>

See the [documentation](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#shortcode) for more information about these attributes.
= Translations =
* Dutch  
* French (by [Annie Stasse](http://www.artisanathai.fr))  
* Spanish (by [msoravilla](http://www.ludobooks.com)) 
* Catalan (by [msoravilla](http://www.ludobooks.com))  
* Polish (by [koda0601](http://rekolekcje.net.pl))  

== Installation ==
* Unzip the <code>related-posts-by-taxonomy.zip</code> folder.
* Upload the <code>related-posts-by-taxonomy</code> folder to your <code>/wp-content/plugins</code> directory.
* Activate *Related Posts by Taxonomy*.
* That's it, now you are ready to use the widget and shortcode

== Frequently Asked Questions ==

For more information about the plugin see the [plugin documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/).  
To customize this plugin see the [plugin recipes page](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/recipes/).

= Can I change the layout for the related posts? =
Yes. Review [this section](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/recipes/#styling) of the documentation to style the related posts yourself.

= How can I automatically add related posts after the post content? =
Review [Adding Related Posts After the Post Content](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#after-content).

= Can I include or exclude posts or terms with the widget or shortcode =
Yes. For the widget see [this filter](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/filters/#related_posts_by_taxonomy_widget_args) and for the shortcode see the attributes [exclude-terms](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#exclude-terms), [include_terms](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#include-terms) and [exclude_posts](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#exclude-posts).

= Can I set my own defaults for the shortcode? =
Yes. Review [Setting your own defaults for the shortcode](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/recipes/#shortcode_defaults) 

= The widget only lets you choose "all taxonomies" or a single taxonomy. Can I make it only use the taxonomies I want? =

Yes. See [this filter](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/filters/#related_posts_by_taxonomy_widget_args) on how to do that.

== Screenshots ==

1. The Widget
2. Twenty Fifteen screenshot. Related posts in the sidebar and after post content
3. Twenty Twelve screenshot. Post thumbnails (after post content) and the widget
4. Twenty Thirteen screenshot. Post thumbnails (after post content) and the widget

== Changelog ==
= 2.2.2 =

* Enhancement
	* New option to link thumbnail captions (post titles) to posts in the widget and shortcode
	* Make the widget UI more intuitive
* Bug fixes
	* Fix wrong results for cached custom post type related posts 

= 2.2.1 =

* Enhancement
	* Add selective refresh to the widget for the customizer
* Bug fixes
	* Fix deprecated notice (by removing PHP4 style constructor) when installing this plugin with PHP7. Props: @dima-stefantsov

= 2.2.0 =
* Enhancement
	* Refactor of the persistant cache for consistent results when using the plugin filters
	* Better data sanitation for taxonomies and comma separated values
	* New PHPunit tests (GitHub only)

= 2.1.1 =
* Enhancement
	* Add Spanish, Catalan and Polish translation files
* Bug fixes
	* Fix minor regression bug with the hide empty filter.
	* Fix minor bug where the wrong default post type was being used by the shortcode.

= 2.1.0 =
* Enhancement
	* Optimised related posts query.
	* All round better validation of function arguments.
	* Persistent cache out of beta. Now fully functioning.
	* Cached data will be deleted after deleting the plugin from the wp-admin
	* New opt-in cache log in the toolbar.

= 2.0.1 =
* Enhancement
	* New option for the widget to randomise the related posts.
	* Refactored the query to get the related posts.
	* Organized files in the new 'includes' directory.
	* Added a new class for debugging (only loaded if needed, with a filter).
	* New PHPUnit tests for the widget(GitHub only).
	* Updated the widget constructor as it is deprecated with WP 4.3
	* Added a new (beta) front end cache layer.

= 1.1 =
* Bug fixes
	* Fixed a minor compatibility bug with the widget customizer. Settings were not saved properly when adding a new widget.

= 1.0 =
* Enhancement
	* Using WordPress semantic versioning.
	* New shortcode attributes before_shortcode and after_shortcode.
	* Default h3 heading for the shortcode 'Related Posts' title
	* Shortcode is wrapped in a div container
	* New filter to add classes to gallery items.
	* New action after displaying related posts.
	* Better logic for the 'related' parameter.
* Bug fixes
	* Applying 'the_title' filters for the shortcode caption.
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
= 2.2.2 =
New option to link thumbnail captions (post titles) to posts in the widget and shortcode. 