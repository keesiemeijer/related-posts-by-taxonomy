/**
 * External dependencies
 */
import { isEmpty, filter, flatten, includes, pickBy } from 'lodash';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { withSelect } = wp.data;
const { compose } = wp.compose;
/**
 * Internal dependencies
 */
import RelatedPostsBlock from './block.js';
import { _pluginData } from './data/data';

if (!isEmpty(_pluginData)) {
	registerRelatedPostsBlock();
}

function registerRelatedPostsBlock() {

	registerBlockType('related-posts-by-taxonomy/related-posts-block', {
		title: __('Related Posts by Taxonomy'),
		icon: 'megaphone',
		category: 'widgets',
		description: __('This block displays related posts by taxonomy.'),
		supports: {
			html: false,
			customClassName: false,
		},

		edit: compose(
			withSelect((select, props) => {
				return {
					postID: select('core/editor').getCurrentPostId(),
					postType: select('core/editor').getCurrentPostType(),
					registeredTaxonomies: select('core').getTaxonomies(),
				};
			}),

			withSelect((select, props) => {
				if (!props.registeredTaxonomies || !props.postType || !props.postID) {
					return null;
				}

				const taxonomyTerms = {};
				const taxonomyNames = [];

				const taxonomies = props.registeredTaxonomies;
				const postTaxonomies = filter(taxonomies, (taxonomy) => includes(taxonomy.types, props.postType));
				postTaxonomies.map((taxonomy) => {
					taxonomyTerms[taxonomy.slug] = select('core/editor').getEditedPostAttribute(taxonomy.rest_base);
					taxonomyNames.push(taxonomy.slug);
				});

				const ids = pickBy(taxonomyTerms, value => value.length);
				let terms = Object.keys(ids).map((tax) => ids[tax]);

				return {
					editorData: {
						taxonomyTerms: taxonomyTerms,
						taxonomyNames: taxonomyNames,
						termIDs: flatten(terms),
					}
				};
			})
		)(RelatedPostsBlock),

		save() {
			// Rendering in PHP
			return null;
		}
	});
}