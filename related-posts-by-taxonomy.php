<?php
/*
Plugin Name: Related Posts By Taxonomy
Version: 2.3.0
Plugin URI: http://keesiemeijer.wordpress.com/related-posts-by-taxonomy/
Description: Display related posts as thumbnails, links, excerpts or as full posts with a widget or shortcode. Posts with the most terms in common will display at the top.
Author: keesiemijer
Author URI:
License: GPL v2
Text Domain: related-posts-by-taxonomy
Domain Path: /lang

Related Posts By Taxonomy
Copyright 2013  Kees Meijer  (email : keesie.meijer@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version. You may NOT assume that you can use any other version of the GPL.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin Folder Path.
if ( ! defined( 'RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR' ) ) {
	define( 'RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/* loads plugin files, adds the shortcode and sets the text domain */
if ( ! function_exists( 'related_posts_by_taxonomy_init' ) ) {

	function related_posts_by_taxonomy_init() {

		load_plugin_textdomain( 'related-posts-by-taxonomy', '', dirname( plugin_basename( __FILE__ ) ) . '/lang' );

		// Defaults used by this plugin.
		require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/defaults.php';

		// Deprecated functions.
		require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/deprecated.php';

		// The widget.
		require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/widget.php';

		// Functions to retrieve related posts from the database.
		require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/functions.php';

		// Functions for display of the related post thumbnail gallery.
		require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/functions-thumbnail.php';

		// loads the different templates used for the widget and shortcode.
		require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/template-loader.php';

		// displays the related posts from the shortcode.
		require_once RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'includes/shortcode.php';

		// Instantiate the defaults class.
		Related_Posts_By_Taxonomy_Defaults::init();
	}


	/* initialize plugin */
	related_posts_by_taxonomy_init();

} // !function_exists
