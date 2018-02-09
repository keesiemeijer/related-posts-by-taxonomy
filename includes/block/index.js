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

const plugin_data = window.km_rpbt_plugin_data || {};
if( ! isEmpty( plugin_data )  ) {
	registerRelatedPostsBlock();
}

function registerRelatedPostsBlock() {

	registerBlockType('related-posts-by-taxonomy/related-posts-block', {
		title: __( 'Related Posts by Taxonomy', 'related-posts-by-taxonomy' ),
		icon: 'megaphone',
		category: 'widgets',
		supports: {
			html: false
		},
		attributes: {
				title: {
					type: 'string',
					default: __( 'Related Posts', 'related-posts-by-taxonomy' ),
				},
				taxonomies: {
					type: 'string',
					default: plugin_data.all_tax,
				},
				posts_per_page: {
					type: 'int',
					default: 5,
				},
		},

		edit: RelatedPostsBlock,

		save() {
			// Rendering in PHP
			return null;
		}
	});
}