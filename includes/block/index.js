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
import RelatedPostsBlock from './block';
import { pluginData } from './includes/data';

if( ! isEmpty( pluginData )  ) {
	registerRelatedPostsBlock();
}

function registerRelatedPostsBlock() {

	registerBlockType('related-posts-by-taxonomy/related-posts-block', {
		title: __( 'Related Posts by Taxonomy', 'related-posts-by-taxonomy' ),
		icon: 'megaphone',
		category: 'widgets',
		description: __( 'This block Displays related posts by taxonomy. The preview of the related posts might not be the same as the display in the front end of your site.', 'related-posts-by-taxonomy' ),
		supports: {
			html: false
		},

		edit: RelatedPostsBlock,

		save() {
			// Rendering in PHP
			return null;
		}
	});
}