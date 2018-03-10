/**
 * External dependencies
 */
import { isUndefined, pickBy, flatten, filter, includes } from 'lodash';
import { stringify } from 'querystringify';

/**
 * WordPress dependencies
 */
const { withSelect} = wp.data;
const { withAPIData } = wp.components;

/**
 * Internal dependencies
 */
import { getPluginData } from './data';

export const postEditorTaxonomies = withAPIData( () => ( {
	editorTaxonomies: '/wp/v2/taxonomies?context=edit',
} ) );

export const postEditorAttributes = withSelect( ( select, props ) => {
	const taxonomyTerms = {};
	const taxonomyNames = [];

	const postType = select( 'core/editor' ).getEditedPostAttribute( 'type' );
	const availableTaxonomies = filter( props.editorTaxonomies.data, ( taxonomy ) => includes( taxonomy.types, postType ) );

	availableTaxonomies.map( ( taxonomy ) => { 
		taxonomyTerms[ taxonomy.slug ] = select( 'core/editor' ).getEditedPostAttribute( taxonomy.rest_base );
		taxonomyNames.push( taxonomy.slug );
	} );

	return {
		editorTaxonomyTerms: taxonomyTerms,
		editorTaxonomyNames: taxonomyNames,
		editorTermIDs: getTermIDs( taxonomyTerms ),
		editorPostType: select( 'core/editor' ).getEditedPostAttribute( 'type' ),
		editorPostID: select( 'core/editor' ).getEditedPostAttribute( 'id' ),
	};
} );

export const relatedPosts = withAPIData( ( props ) => {
	const { editorTermIDs, editorTaxonomyNames, editorPostID, editorPostType } = props
	const { post_types, title, posts_per_page, format, image_size, columns } = props.attributes;
	const type = 'editor_block';
	let { taxonomies} = props.attributes
	
	// Get the terms set in the editor.
	let terms = editorTermIDs.join(',');
	if( ! terms.length && ( -1 !== editorTaxonomyNames.indexOf('category') ) ) {
		// Use default category if this post supports the 'category' taxonomy.
		terms = getPluginData('default_category');
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
	};

	if( attributes['post_types'] && ( attributes['post_types'] === editorPostType ) ) {
		// The post type isn't needed in the query (if not set).
		// It defaults to the post type of the current post.
		delete attributes['post_types'];
	}

	const query = stringify( pickBy( attributes, value => ! isUndefined( value ) ), true );

	return {
		relatedPosts: `/related-posts-by-taxonomy/v1/posts/${editorPostID}` + `${query}`,
	};
} );

export function getTermIDs( taxonomies ) {
	const ids = pickBy( taxonomies, value => value.length );
	let terms = Object.keys( ids ).map( ( tax ) => ids[ tax ]);

	return flatten( terms );
}