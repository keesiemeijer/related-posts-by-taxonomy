/**
 * External dependencies
 */
import { stringify } from 'querystringify';
import { isUndefined, pickBy, debounce } from 'lodash';

/**
 * WordPress dependencies
 */
const { InspectorControls, BlockDescription } = wp.blocks;
const { BaseControl } = InspectorControls;
const { withAPIData, Spinner, Placeholder } = wp.components;
const { Component } = wp.element;
const { __, sprintf } = wp.i18n;

/**
 * Internal dependencies
 */
import { postTypes, getPostField } from './includes/data';
import QueryPanel from './includes/query-panel/';
import PostTypeControl from './includes/post-type-control/';

let instances = 0;

var placeholderStyle = {
	minHeight: '100px',
};

class RelatedPostsBlock extends Component {
	constructor() {
		super( ...arguments );

		this.updatePostTypes = this.updatePostTypes.bind(this);
		this.handleChange = this.handleChange.bind(this);
		this.emitChangeDebounced = debounce( this.emitChange, 1000);
		this.instanceId = instances++;
	}

	componentWillUnmount() {
		this.emitChangeDebounced.cancel();
	}

	handleChange(e) {
		// React pools events, so we read the value before debounce.
		// Alternately we could call `event.persist()` and pass the entire event.
		// For more info see reactjs.org/docs/events.html#event-pooling
		this.emitChangeDebounced(e.target.value);
	}

	emitChange(value) {
		const { setAttributes } = this.props;
		setAttributes( { title: value } );
	}

	updatePostTypes( post_types, e ) {
		const { setAttributes } = this.props;
		e.target.checked = true;
		setAttributes( { post_types: post_types } );
	}

	render(){
		const textID = 'rpbt-inspector-text-control-' + this.instanceId;
		const relatedPosts = this.props.relatedPostsByTax.data;
		const { attributes, focus, setAttributes } = this.props;
		const { title, taxonomies, post_types, posts_per_page, format, image_size, columns } = attributes;

		let checked_post_types = post_types;
		if( isUndefined( post_types ) || ! post_types ) {
			// Use post type from the current post if not set.
			checked_post_types = getPostField('type');
		}

		const inspectorControls = focus && (
			<InspectorControls key="inspector">
				<h3>{ __( 'Related Posts Settings' ) }</h3>
				<BaseControl label={ 'Title' } id={ textID }>
					<input className="blocks-text-control__input"
						type="text"
						onChange={this.handleChange}
						defaultValue={title}
						id={textID}
					/>
				</BaseControl>
				<QueryPanel
					postsPerPage={posts_per_page}
					onPostsPerPageChange={ ( value ) => setAttributes( { posts_per_page: Number( value ) } )}
					taxonomies={ taxonomies }
					onTaxonomiesChange={ ( value ) => setAttributes( { taxonomies: value } ) }
					format={ format }
					onFormatChange={ ( value ) => setAttributes( { format: value } ) }
					imageSize={image_size}
					onImageSizeChange={ ( value ) => setAttributes( { image_size: value } ) }
					columns={columns}
					onColumnsChange={ ( value ) => setAttributes( { columns: Number( value ) } ) }
				/>
				<PostTypeControl
					label={ __( 'Post Types' ) }
					onChange={ this.updatePostTypes }
					postTypes={ checked_post_types }
				/>
			</InspectorControls>
			);

		let loading = '';
		let postsFound = 0;
		if( isUndefined( relatedPosts ) ) {
			loading = __( 'Loading posts', 'related-posts-by-taxonomy');
		} else {
			if( relatedPosts.hasOwnProperty('posts') ) {
				postsFound = relatedPosts.posts.length ? relatedPosts.posts.length : 0;
				loading = postsFound ? '' : __( 'No posts found.', 'related-posts-by-taxonomy' );
			}
		}

		if ( loading || ! focus ) {
			let postsFoundText = __('preview related posts');

			return [
				inspectorControls,
				(! focus || ! postsFound) && (<Placeholder
					style={placeholderStyle}
					key="placeholder"
					icon="megaphone"
					label={ __( 'Related Posts by Taxonomy', 'related-posts-by-taxonomy' ) }
				>
					{ isUndefined( relatedPosts ) ? <Spinner /> : '' }
					{ loading }
					{ postsFound ? <a href="#">{ postsFoundText }</a> : '' }
				</Placeholder> ),
			];
		}

		return [
			inspectorControls,
			(<div dangerouslySetInnerHTML={{__html:relatedPosts.rendered}}></div>)
		];
	}
}

const applyWithAPIData = withAPIData( ( props ) => {
	const { taxonomies, post_types, title, posts_per_page, format, image_size, columns } = props.attributes;
	const attributes = {
		taxonomies,
		post_types,
		title,
		posts_per_page,
		format,
		image_size,
		columns
	};

	const postID = getPostField('id');
	const postType = getPostField('type');

	if( attributes['post_types'] && ( attributes['post_types'] === postType ) ) {
		// Not needed in the query.
		// The post type defaults to the post type of the current post
		delete attributes['post_types'];
	}

	const query = stringify( pickBy( attributes, value => ! isUndefined( value ) ), true );
	return {
		relatedPostsByTax: `/related-posts-by-taxonomy/v1/posts/${_wpGutenbergPost.id}` + `${query}`
	};
} );

export default applyWithAPIData( RelatedPostsBlock );
