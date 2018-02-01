/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;

/**
 * Internal dependencies
 */
import RelatedPostsBlock from './block';
const { registerBlockType } = wp.blocks;

registerBlockType('related-posts-by-taxonomy/related-posts-block', {
	title: 'Related Posts by Taxonomy',
	icon: 'megaphone',
	category: 'widgets',
	supports: {
		html: false
	},

	edit: RelatedPostsBlock,

	save() {
		// Rendering in PHP
		return null;
	}
});