=== Related Posts by Taxonomy ===
Contributors: keesiemeijer
Tags: posts,related,related posts,related thumbnails,similar,similar posts,widget,shortcode,taxonomy,taxonomies,post type,post types,category,categories,tag,tags,post thumbnail,post thumbnails,thumbnails,featured,featured image,image,images
Requires at least: 4.1
Tested up to: 4.9
Stable tag: 2.5.1
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
* Automatic display of related posts after the post content.
* Search for related posts in single or multiple **taxonomies** and **post types**.
* **Exclude** or **include** terms and posts.
* **Limit the search** of related posts by date or number.
* Use your own **HTML templates** for display of the related posts.
* Use the **WordPress REST API** to get related posts. (opt-in feature)
* Use a persistent cache layer for the related posts. (opt-in feature)
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
* `show_date`
* `before_title`
* `after_title`
* `terms`
* `include_terms`
* `exclude_terms`
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
Yes. For the widget see [this filter](http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/filters/#related_posts_by_taxonomy_widget_args) and for the shortcode see the attributes [terms](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#terms), [include_terms](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#include-terms), [exclude-terms](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#exclude-terms) and [exclude_posts](https://keesiemeijer.wordpress.com/related-posts-by-taxonomy/#exclude-posts).

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

= 2.4.1 =
* Enhancement
	* Add post classes with filters or as a shortcode or widget argument
	* Move included current post to the top of the stack by default 

= 2.3.2 =
* Enhancement
	* Add public_only parameter to not display private related posts from the current author.
	* Add include_self parameter to include the current post in the related posts.
	* Add filter pre_related_posts to override the related posts query.
* Bug fixes
	* Fix duplicate gallery IDs breaking the column layout of WordPress galleries. Props: @sonamel

= 2.3.1 =

* Enhancement
	* Add support to disable the shortcode or widget for this plugin
	* Add support to update all plugin settings with one filter.


For older changelog versions see the changelog.txt file

== Upgrade Notice ==
= 2.5.1 =
With this update you can add the post date after post titles. (see changelog for more changes) 