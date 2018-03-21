/**
 * External dependencies
 */
import { pickBy, flatten, filter, includes } from 'lodash';

/**
 * WordPress dependencies
 */
const { withSelect} = wp.data;
const { withAPIData } = wp.components;
const { compose } = wp.element;

const postFields = {};

function getTermIDs( taxonomies ) {
	const ids = pickBy( taxonomies, value => value.length );
	let terms = Object.keys( ids ).map( ( tax ) => ids[ tax ]);

	return flatten( terms );
}

const taxonomies = withAPIData( () => ( {
	registeredTaxonomies: '/wp/v2/taxonomies?context=edit',
} ) );

const postEditorAttributes = withSelect( ( select, props ) => {
	const taxonomyTerms = {};
	const taxonomyNames = [];

	if( ! postFields.length ) {
		postFields['id'] = select( 'core/editor' ).getEditedPostAttribute('id');
		postFields['type'] = select( 'core/editor' ).getEditedPostAttribute('type');
	}

	const taxonomies = props.registeredTaxonomies.data;
	const postTaxonomies = filter( taxonomies, ( taxonomy ) => includes( taxonomy.types, postFields['type'] ) );

	postTaxonomies.map( ( taxonomy ) => { 
		taxonomyTerms[ taxonomy.slug ] = select( 'core/editor' ).getEditedPostAttribute( taxonomy.rest_base );
		taxonomyNames.push( taxonomy.slug );
	} );

	return {
		editorData: {
			taxonomyTerms: taxonomyTerms,
			taxonomyNames: taxonomyNames,
			termIDs: getTermIDs( taxonomyTerms ),
			postType: postFields['type'],
			postID: postFields['id'],
		}
	};
} );

export const editorData = compose(
	taxonomies,
	postEditorAttributes,
);