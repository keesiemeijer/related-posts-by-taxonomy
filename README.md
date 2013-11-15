related-posts-by-taxonomy
=========================

A WordPress plugin that provides a widget and shortcode to display related posts by taxonomies as links, full posts, excerpts or post thumbnails. 

Choose from single or multiple taxonomies and search for related posts in multiple post types. Posts that have the most terms in common will display at the top (also ordered by date). It's easy to override the look of the widget or shortcode with a filter or by including your own templates in your theme. It's also possible to get the related posts by using a function in your theme template files.

For more information on how to use the plugin see [these instructions](http://keesiemeijer.wordpress.com/2013/05/11/related-posts-by-taxonomy-plugin/).

Default usage of the shortcode is:

    [related_posts_by_tax]

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

    [related_posts_by_tax posts_per_page="10"]

Read the documentation for more information on these attributes and what they do.
