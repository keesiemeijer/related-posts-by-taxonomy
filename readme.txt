=== Related Posts by Taxonomy ===
Contributors: keesiemeijer
Tags: posts,related,related posts,thumbnails,taxonomy,widget,shortcode,taxonomies,post type,post types,category,categories,tag,tags,post thumbnail,post thumbnails,thumbnails,featured,featured image,image,images
Requires at least: 4.5
Tested up to: 5.5
Stable tag: 2.7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display a list of related posts on your site based on the most terms in common. Supports thumbnails, shortcodes, a widget and more.

== Description ==

Quickly increase your readers' engagement by adding related posts in the sidebar or after post content with a widget or shortcode.

Posts with the **most terms in common** will display at the top! 

This plugin is capable of finding related posts in multiple **taxonomies** and **post types**. Include or exclude terms from the search for related posts. Change the look and feel by using your own templates in a (child) theme.

[plugin documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/)

Plugin features:

* Widget and Shortcode.
* Display related posts as **post thumbnails**, links, excerpts or full posts.
* **Small Footprint**. Doesn't slow down your site!
* Automatic display of related posts after the post content.
* **Exclude** or **include** terms and posts.
* Search for related posts in single or multiple **taxonomies** and **post types**.
* Limit the search for related posts by date, number or post meta.
* Use your own **HTML templates** for display of the related posts.
* Extensive [plugin documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/).
* Follows WordPress coding standards and plugin best practices.
* Highly Adjustable!

For the following features you need to be somewhat familiar with WordPress [hooks](https://developer.wordpress.org/plugins/hooks/). The [plugin documentation](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/) has many examples to help you along.

Extended features:

* Use a persistent cache for the related posts query. (opt-in feature)
* Use the **WordPress REST API** to get related posts. (opt-in feature)
* Load related posts after the whole page has loaded (with Ajax). (opt-in feature)
* Use **plugin functions** in your theme templates to display related posts yourself.
* Use Filters to **change the default behavior** of the plugin. 

Follow this plugin on [GitHub](https://github.com/keesiemeijer/related-posts-by-taxonomy).  
Search the [code reference](https://keesiemeijer.github.io/related-posts-by-taxonomy)  

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
* `show_date`
* `before_title`
* `after_title`
* `include_terms`
* `include_parents`
* `include_children`
* `exclude_terms`
* `exclude_posts`
* `format`
* `gallery_format`
* `image_size`
* `columns`
* `caption`
* `link_caption`
* `limit_posts`
* `limit_month`
* `meta_key`
* `meta_value`
* `meta_compare`
* `meta_type`
* `public_only`
* `include_self`
* `post_class`

Example to show 10 related posts instead of the default 5.
<pre><code>[related_posts_by_tax posts_per_page="10"]</code></pre>

See the [documentation](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#shortcode) for more information about these attributes.
= Translations =
* Dutch  
* French (by [Annie Stasse](http://www.artisanathai.fr))  
* Spanish (by [Ludobooks – Cuentos personalizados](http://www.ludobooks.com))  
* Catalan (by [Ludobooks – Cuentos personalizados](http://www.ludobooks.com))  
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
Yes. For the widget see [this filter](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/filters/#related_posts_by_taxonomy_widget_args) and for the shortcode see the attributes [include_terms](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#include-terms), [exclude-terms](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#exclude-terms) and [exclude_posts](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#exclude-posts).

= Can I set my own defaults for the shortcode? =
Yes. Review [Setting your own defaults for the shortcode](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/recipes/#shortcode_defaults) 

= The widget only lets you choose "all taxonomies" or a single taxonomy. Can I make it use multiple specific taxonomies? =

Yes. See [this filter](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/filters/#related_posts_by_taxonomy_widget_args) on how to do that.

= Is this plugin GDPR compliant? =
* This plugin doesn’t process, share, send or retain any user data.
* This plugin doesn't track (user) data for analytics (or for any other reason).
* This plugin doesn't save or read cookies.
* This plugin doesn't use 3rd party libraries.

Read [this article](https://developer.wordpress.org/plugins/wordpress-org/compliance-disclaimers/) why plugin authors cannot guarantee 100% compliance.

Please contact a GDPR consultant or law firm with this information to assess if this plugin is compliant.

== Screenshots ==

1. The Widget
2. Twenty Fifteen screenshot. Related posts in the sidebar and after post content
3. Twenty Twelve screenshot. Post thumbnails (after post content) and the widget
4. Twenty Thirteen screenshot. Post thumbnails (after post content) and the widget

== Changelog ==
= 2.7.4 =
* Enhancement
	* Update the block gallery with the HTML changes made in WordPress 5.4 (again!).
	* Use post title for aria-label only
	* Display cache log in footer (because admin_bar_menu hook changed to wp_body_open)
	* Getting ready for WP 5.5 (updating deprecated functions)
= 2.7.3 =
* Enhancement
	* Make the no posts found message filterable.
	* Update gallery with the HTML changes made in the WordPress 5.3 gallery.
	* Add accessibility to the gallery item element.
* Deprecated
	* The 'terms' and 'related' arguments are deprecated in favor of the 'include_terms' argument.
		* The 'include_terms' argument now uses the included terms without any restrictions.
		* The default value for the 'related' argument changed from boolean true to null.
		* Set the 'related' argument to boolean true to have the old restrictions back.

= 2.7.2 =
* Bug Fixes
	* Fix for gallery image (fallback) filter not being reached.
	* Sanitize Rest API rendered HTML with wp_kses_post().

= 2.7.1 =
* Enhancement
	* New format for related galleries similar to the Gutenberg gallery block.
	* Include child or parent terms for the related posts query.
	* Filters for Widget defaults and instance.
* Bug Fixes
	* (minor) Don't default to all public taxonomies if no valid taxonomies are used.

= 2.6.0 =
* Enhancement
	* Meta query
		* Allows you to query related posts with post meta
		* Use meta arguments in the shortcode
		* Use a filter for the widget or for complex meta queries
	* ID query
		* Allow queries for post IDs only. (for the related posts in the widget and shortcode templates)
		* Speeds up the related posts query. (not activated by default because of back compatibility)
		* Please read the documentation about query optimization before using this feature
    * Lazy loading (opt in feature).
		* Speeds up perceived page load time for very large sites
		* Does the query for related posts (with Ajax) after the page is loaded
		* Recommended for related posts below the fold.
	* Detect post type Page taxonomies
	* Preparing the plugin for the (Gutenberg) editor block feature

= 2.5.1 =
* Enhancement
    * Add ability to show the post date after the post title
    * Allow getting post fields from the cache
    * Add new filter to filter all related post permalinks
    * Add new tests for the post type feature (GitHub)
* Bug fixes
	* (minor) Add post classes after retrieving posts from the cache
	* (minor) Return an error if invalid taxonomies or post types was requested with the WP Rest API

= 2.5.0 =
* Enhancement
	* Prepare plugin for gutenberg blocks
	* Add 'terms' parameter for shortcode and widget
	* Add GDPR information to readme.txt
	* deprecate functions (with back compatibility)
		* km_rpbt_related_posts_by_taxonomy()
		* km_rpbt_get_default_args()
		* km_rpbt_related_posts_by_taxonomy_validate_ids()
		* km_rpbt_related_posts_by_taxonomy_template()
		* km_rpbt_post_title_link()
		* km_rpbt_get_related_post_title_link()
		* km_rpbt_related_posts_by_taxonomy_widget()
* Bug fixes
	* (minor) Add missing filter pre_related_posts before cache queries
	* (minor) Add missing properties to related posts returned by the cache 

For older changelog versions see the changelog.txt file

== Upgrade Notice ==
= 2.7.4 =
No new features. This upgrade does minor changes for WordPress 5.5
