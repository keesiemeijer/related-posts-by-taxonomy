/**
 * External dependencies
 */
import { isEmpty } from 'lodash';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

/**
 * Internal dependencies
 */
import RelatedPostsBlock from './includes/block';
import { _pluginData } from './includes/data';

if( ! isEmpty( _pluginData )  ) {
	registerRelatedPostsBlock();
}

function registerRelatedPostsBlock() {

	registerBlockType('related-posts-by-taxonomy/related-posts-block', {
		title: __( 'Related Posts by Taxonomy', 'related-posts-by-taxonomy' ),
		icon: 'megaphone',
		category: 'widgets',
		description: __( 'This block displays related posts by taxonomy.', 'related-posts-by-taxonomy' ),
		supports: {
			html: false,
			customClassName: false,
		},

		edit: RelatedPostsBlock,

		save() {
			// Rendering in PHP
			return null;
		}
	});
}