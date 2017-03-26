<?php

// Uninstall script.

if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit ();
}

global $wpdb;

if ( is_multisite() ) {
	global $wpdb;
	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
	if ( $blogs ) {
		foreach ( (array) $blogs as $blog ) {
			switch_to_blog( $blog['blog_id'] );
			$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_rpbt_related_posts%'" );
			delete_transient( 'rpbt_related_posts_flush_cache' );
		}
		restore_current_blog();
	}
} else {
	$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE '_rpbt_related_posts%'" );
	delete_transient( 'rpbt_related_posts_flush_cache' );
}
