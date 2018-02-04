/**
 * External dependencies
 */
import { stringify } from 'querystringify';
import { isUndefined, pickBy } from 'lodash';


import QueryPanel from './query-panel';
const { InspectorControls, BlockDescription } = wp.blocks;
const { withAPIData } = wp.components;
const { Component } = wp.element;
const { __ } = wp.i18n;

class RelatedPostsBlock extends Component {
	constructor() {
		super( ...arguments );
	}

	render(){
		if ( ! this.props.relatedPostsByTax.data ) {
			return "loading !";
		}
		if ( this.props.relatedPostsByTax.data.length === 0 ) {
			return "No posts";
		}

		const { attributes, focus, setAttributes } = this.props;
		const relatedPosts = this.props.relatedPostsByTax.data;
		
		const inspectorControls = focus && (
			<InspectorControls key="inspector">
				<h3>{ __( 'Latest Posts Settings' ) }</h3>
				<QueryPanel
					taxonomies={ attributes.taxonomies }
					onTaxonomiesChange={ ( value ) => setAttributes( { taxonomies: value } ) }
				/>
			</InspectorControls>
			);

		return [
				inspectorControls,
				(<div dangerouslySetInnerHTML={{__html:relatedPosts.rendered}}></div>)
			];
	}
}

export default withAPIData( ( props ) => {
	const { taxonomies } = props.attributes;
	const query = stringify( pickBy( {
		taxonomies
	}, value => ! isUndefined( value ) ), true );

	return {
		relatedPostsByTax: `/related-posts-by-taxonomy/v1/posts/${_wpGutenbergPost.id}` + `${query}`
	};
} )( RelatedPostsBlock );
