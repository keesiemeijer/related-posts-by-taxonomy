/**
 * External dependencies
 */
import { stringify } from 'querystringify';
import { isUndefined, pickBy, debounce } from 'lodash';

/**
 * WordPress dependencies
 */
const { InspectorControls } = wp.blocks;
const { withAPIData, BaseControl} = wp.components;
const { Component, RawHTML, compose } = wp.element;
const { __, sprintf } = wp.i18n;
const { withSelect, select } = wp.data;

/**
 * Internal dependencies
 */
import './editor.scss'
import { getPluginData, getPostField, getTermIDs, getTaxName } from './data';
import QueryPanel from './query-panel';
import LoadingPlaceholder from './placeholder';

let instances = 0;

class RelatedPostsBlock extends Component {
	constructor() {
		super( ...arguments );

		this.previewExpanded = getPluginData( 'preview' );
		this.updatePostTypes = this.updatePostTypes.bind(this);

		// The title is updated 1 second after a change.
		// This allows the user more time to type.
		this.onTitleChange = this.onTitleChange.bind(this);
		this.titleDebounced = debounce( this.updateTitle, 1000);

		this.instanceId = instances++;
	}

	componentWillUnmount() {
		this.titleDebounced.cancel();
	}

	onTitleChange(e) {
		// React pools events, so we read the value before debounce.
		// Alternately we could call `event.persist()` and pass the entire event.
		// For more info see reactjs.org/docs/events.html#event-pooling
		this.titleDebounced(e.target.value);
	}

	updateTitle(value) {
		const { setAttributes } = this.props;
		setAttributes( { title: value } );
	}

	updatePostTypes( post_types ) {
		const { setAttributes } = this.props;
		setAttributes( { post_types: post_types } );
	}

	render(){
		const relatedPosts = this.props.relatedPostsByTax.data;
		const { attributes, focus, setAttributes, editorTerms, editorTaxonomies } = this.props;
		const { title, taxonomies, post_types, posts_per_page, format, image_size, columns } = attributes;
		const titleID = 'rpbt-inspector-text-control-' + this.instanceId;

		let checkedPostTypes = post_types;
		if( isUndefined( post_types ) || ! post_types ) {
			// Use the post type from the current post if not set.
			checkedPostTypes = getPostField('type');
		}

		const inspectorControls = focus && (
			<InspectorControls key="inspector">
				<div>
					<p>
					<RawHTML>
					{__( '<strong>Note</strong>: The preview of this block can be different from the display in the front end of your site.', 'related-posts-by-taxonomy')}
					</RawHTML>
					</p>
				</div>
					
				<BaseControl label={ __( 'Title' , 'related-posts-by-taxonomy') } id={titleID}>
					<input className="blocks-text-control__input"
						type="text"
						onChange={this.onTitleChange}
						defaultValue={title}
						id={titleID}
					/>
				</BaseControl>
				<QueryPanel
					postsPerPage={posts_per_page}
					onPostsPerPageChange={ ( value ) => {
							// Don't allow 0 as a value.
							const newValue = ( 0 === Number( value ) ) ? 1 : value;
							setAttributes( { posts_per_page: Number( newValue ) } );
						}
					}
					taxonomies={ taxonomies }
					onTaxonomiesChange={ ( value ) => setAttributes( { taxonomies: value } ) }
					format={ format }
					onFormatChange={ ( value ) => setAttributes( { format: value } ) }
					imageSize={image_size}
					onImageSizeChange={ ( value ) => setAttributes( { image_size: value } ) }
					columns={columns}
					onColumnsChange={ ( value ) => setAttributes( { columns: Number( value ) } ) }
					postTypes={ checkedPostTypes }
					onPostTypesChange={ this.updatePostTypes }
				/>
			</InspectorControls>
			);

		let postsFound = 0;
		const queryFinished = ! isUndefined( relatedPosts );

		if( queryFinished ) {
			if( relatedPosts.hasOwnProperty('posts') ) {
				postsFound = relatedPosts.posts.length ? relatedPosts.posts.length : 0;
			}
		}

		if ( ( ! focus && ! this.previewExpanded ) || ! postsFound  ) {
			return [
				inspectorControls,
				<LoadingPlaceholder
					className={this.props.className}
					queryFinished={queryFinished}
					postsFound={postsFound}
					editorTerms={editorTerms}
					editorTaxonomies={editorTaxonomies}
					icon="megaphone"
					label={ __( 'Related Posts by Taxonomy', 'related-posts-by-taxonomy' ) }
				/>
			];
		}

		return [
			inspectorControls,
			<div className={this.props.className}><RawHTML>{relatedPosts.rendered}</RawHTML></div>
		];
	}
}

const applyWithAPIData = withAPIData( ( props ) => {
	const { editorTerms, editorTaxonomies } = props
	const { post_types, title, posts_per_page, format, image_size, columns } = props.attributes;
	const is_editor_block = true;
	let { taxonomies} = props.attributes

	// Get the terms set in the editor.
	let terms = getTermIDs( editorTerms ).join(',');
	if( ! terms.length && ( -1 !== editorTaxonomies.indexOf('category') ) ) {
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
		is_editor_block,
	};

	const postID = getPostField('id');
	const postType = getPostField('type');

	if( attributes['post_types'] && ( attributes['post_types'] === postType ) ) {
		// The post type isn't needed in the query (if not set).
		// It defaults to the post type of the current post.
		delete attributes['post_types'];
	}

	const query = stringify( pickBy( attributes, value => ! isUndefined( value ) ), true );

	return {
		relatedPostsByTax: `/related-posts-by-taxonomy/v1/posts/${postID}` + `${query}`
	};
} );

const applyWithQuery = withSelect( () => {
		const editorTerms = {};
		const editorTaxonomies = [];
		const taxonomies = getPluginData( 'taxonomies' );

		for ( var key in taxonomies ) {
			if ( ! taxonomies.hasOwnProperty( key ) ) {
				continue;
			}

			// Get the correct tax name for post attribute 'categories' and 'tags'. 
			const taxName = getTaxName( key );
			const query = select( 'core/editor' ).getEditedPostAttribute( taxName );

			if( ! isUndefined( query ) ) {
				editorTerms[ key ] = query;
				editorTaxonomies.push( key );
			}
		}

		return {
			editorTerms: editorTerms,
			editorTaxonomies: editorTaxonomies,
		};
	} );

export default compose( [
	applyWithQuery,
	applyWithAPIData,
] )( RelatedPostsBlock );
