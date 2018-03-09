/**
 * External dependencies
 */
import { isUndefined, pickBy, flatten } from 'lodash';

/**
 * WordPress dependencies
 */
const { withSelect, select } = wp.data;

/**
 * Internal dependencies
 */
import { getPluginData } from './data';

export const postEditorTaxonomies = withSelect( () => {
	const taxonomyTerms = {};
	const taxonomyNames = [];
	const taxonomies = getPluginData( 'taxonomies' );

	for ( var key in taxonomies ) {
		if ( ! taxonomies.hasOwnProperty( key ) ) {
			continue;
		}

		// Get the correct tax name for post attribute 'categories' and 'tags'. 
		const taxName = getTaxName( key );
		const query = select( 'core/editor' ).getEditedPostAttribute( taxName );

		if( ! isUndefined( query ) ) {
			taxonomyTerms[ key ] = query;
			taxonomyNames.push( key );
		}
	}

	return {
		editorTaxonomyTerms: taxonomyTerms,
		editorTaxonomyNames: taxonomyNames,
		editorTermIDs: getTermIDs( taxonomyTerms ),
	};
} );

export function getTermIDs( taxonomies ) {
	const ids = pickBy( taxonomies, value => value.length );
	let terms = Object.keys( ids ).map( ( tax ) => ids[ tax ]);

	return flatten( terms );
}

export function getTaxName( taxonomy ) {
	if ( ( 'category' === taxonomy ) || ( 'post_tag' === taxonomy ) ) {
		taxonomy = ('category' === taxonomy) ? 'categories' : 'tags';
	}
	return taxonomy;
}
