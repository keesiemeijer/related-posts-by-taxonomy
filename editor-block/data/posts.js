/**
 * External dependencies
 */
import { isUndefined, pickBy } from 'lodash';
import { stringify } from 'querystringify';

/**
 * WordPress dependencies
 */
const { withAPIData } = wp.components;
const { compose } = wp.element;

/**
 * Internal dependencies
 */
import { getPluginData } from './data';
import { editorData } from './editor';

const previewExpanded = getPluginData('preview');
const defaultCategory = getPluginData('default_category');

export const relatedPosts = compose(
	editorData,
	withAPIData( ( props ) => {
		const { editorData, isSelected } = props;
		const { post_types, title, posts_per_page, format, image_size, columns } = props.attributes;
		const type = 'editor_block';
		let { taxonomies} = props.attributes;
		let fields = '';

		if ( ! isSelected && ! previewExpanded ) {
			// Us a less expensive query if preview is not expanded by default
			fields = 'ids';
		}
		
		// Get the terms set in the editor.
		let terms = editorData.termIDs.join(',');
		if( ! terms.length && ( -1 !== editorData.taxonomyNames.indexOf('category') ) ) {
			// Use default category if this post supports the 'category' taxonomy.
			terms = defaultCategory;
		}

		// If no terms are selected return no related posts.
		taxonomies = terms.length ? taxonomies : '';

		const attributes = {
			taxonomies,
			post_types,
			terms,
			title,
			posts_per_page,
			format,
			image_size,
			columns,
			type,
			fields,
		};

		if( attributes['post_types'] && ( attributes['post_types'] === editorData.postType ) ) {
			// The post type isn't needed in the query (if not set).
			// It defaults to the post type of the current post.
			delete attributes['post_types'];
		}

		const query = stringify( pickBy( attributes, value => ! isUndefined( value ) ), true );

		return {
			relatedPosts: `/related-posts-by-taxonomy/v1/posts/${editorData.postID}` + `${query}`,
		}
	} ),
);