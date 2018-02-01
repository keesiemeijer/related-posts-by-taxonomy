/**
 * External dependencies
 */
import { stringify } from 'querystringify';
import { isUndefined, pickBy } from 'lodash';

const { InspectorControls, BlockDescription } = wp.blocks;
const { withAPIData } = wp.components;
const { Component } = wp.element;


class RelatedPostsBlock extends Component {
	constructor() {
		super( ...arguments );
	}

	render(){
		if ( ! this.props.related_posts.data ) {
			return "loading !";
		}
		if ( this.props.related_posts.data.length === 0 ) {
			return "No posts";
		}

		const relatedPosts = this.props.related_posts.data;

		return (<div dangerouslySetInnerHTML={{__html:relatedPosts.rendered}}></div>) 
	}
}

export default withAPIData( ( props ) => {
	const { taxonomies } = props.attributes;
	const queryString = stringify( pickBy( {
		taxonomies,
		_fields: [ 'date_gmt', 'link', 'title' ],
	}, value => ! isUndefined( value ) ) );
	return {
		related_posts: '/related-posts-by-taxonomy/v1/posts/' + _wpGutenbergPost.id,
	};
} )( RelatedPostsBlock );
