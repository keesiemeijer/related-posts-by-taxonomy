/**
 * External dependencies
 */
import { isUndefined, debounce } from 'lodash';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
const { InspectorControls } = wp.blocks;
const { BaseControl} = wp.components;
const { Component, RawHTML, compose } = wp.element;
const { __, sprintf } = wp.i18n;

/**
 * Internal dependencies
 */
import './editor.scss'
import { getPluginData } from './data/plugin';
import { relatedPosts } from './data/related-posts';
import QueryPanel from './components/query-panel';
import LoadingPlaceholder from './components/placeholder';

let instances = 0;

class RelatedPostsBlock extends Component {
	constructor() {
		super( ...arguments );

		// Data provided by this plugin.
		this.previewExpanded = getPluginData( 'preview' );
		this.html5Gallery = getPluginData( 'html5_gallery' );

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

	updatePostTypes( postTypes ) {
		const { setAttributes } = this.props;
		setAttributes( { post_types: postTypes } );
	}

	render(){
		const relatedPosts = this.props.relatedPosts.data;
		const { attributes, focus, setAttributes, editorData } = this.props;
		const { title, taxonomies, post_types, posts_per_page, format, image_size, columns } = attributes;
		const titleID = 'rpbt-inspector-text-control-' + this.instanceId;
		const className = classnames( this.props.className, { 'html5-gallery': this.html5Gallery } );

		let checkedPostTypes = post_types;
		if( isUndefined( post_types ) || ! post_types ) {
			// Use the post type from the current post if not set.
			checkedPostTypes = editorData.postType;
		}

		const inspectorControls = focus && (
			<InspectorControls key="inspector">
				<div>
					<p>
					<RawHTML>
					{__( '<strong>Note</strong>: The preview of this block is not the actual display as used in the front end of your site.', 'related-posts-by-taxonomy')}
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
					className={className}
					queryFinished={queryFinished}
					postsFound={postsFound}
					editorTerms={editorData.termIDs}
					editorTaxonomies={editorData.taxonomyNames}
					icon="megaphone"
					label={ __( 'Related Posts by Taxonomy', 'related-posts-by-taxonomy' ) }
				/>
			];
		}

		let html = ! isUndefined( relatedPosts.rendered ) ? relatedPosts.rendered : '';

		// Add target blank to all links
		// Todo: find a better way to do this
		html = relatedPosts.rendered.replace(/\<a href\=/g, '<a target="_blank" href=');

		return [
			inspectorControls,
			html && (<div className={className}><RawHTML>{html}</RawHTML></div>)
		];
	}
}

export default compose( [
	relatedPosts,
] )( RelatedPostsBlock );
