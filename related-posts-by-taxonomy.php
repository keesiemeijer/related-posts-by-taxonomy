<?php
/*
Plugin Name: Related Posts By Taxonomy
Version: 2.7.4
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

// Plugin Folder URL
if ( ! defined( 'RELATED_POSTS_BY_TAXONOMY_PLUGIN_URL' ) ) {
	define( 'RELATED_POSTS_BY_TAXONOMY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/* loads plugin files, adds the shortcode and sets the text domain */
if ( ! function_exists( 'related_posts_by_taxonomy_init' ) ) {

	/**
	 * Initialize plugin.
	 *
	 * Includes all plugin files and initializes plugin.
	 *
	 * @since  0.1
	 */
	function related_posts_by_taxonomy_init() {

		require_once __DIR__ . '/autoloader/autoload.php';

		load_plugin_textdomain( 'related-posts-by-taxonomy', '', dirname( plugin_basename( __FILE__ ) ) . '/lang' );

		// Instantiate the plugin class.
		$related_posts = new Related_Posts_By_Taxonomy_Plugin();
		$related_posts->init();
	}

	/* initialize plugin */
	related_posts_by_taxonomy_init();

} // !function_exists
