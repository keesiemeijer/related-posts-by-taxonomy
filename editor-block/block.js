/**
 * External dependencies
 */
import { isUndefined, debounce } from 'lodash';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
const { InspectorControls } = wp.blocks;
const { BaseControl, PanelBody, ToggleControl } = wp.components;
const { Component, Fragment, RawHTML, compose } = wp.element;
const { __, sprintf } = wp.i18n;

/**
 * Internal dependencies
 */
import './editor.scss'
import { getPluginData } from './data/data';
import { relatedPosts } from './data/posts';
import PostsPanel from './components/posts-panel';
import ImagePanel from './components/image-panel';
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

		this.toggleLinkCaption = this.toggleLinkCaption.bind( this );

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

	toggleLinkCaption(){
		const { link_caption } = this.props.attributes;
		const { setAttributes } = this.props;

		setAttributes( { link_caption: ! link_caption } );
	}

	render(){
		const relatedPosts = this.props.relatedPosts.data;
		const { attributes, focus, isSelected, setAttributes, editorData } = this.props;
		const { title, taxonomies, post_types, posts_per_page, format, image_size, columns, link_caption } = attributes;
		const titleID = 'inspector-text-control-' + this.instanceId;
		const className = classnames( this.props.className, { 'rpbt-html5-gallery': ( 'thumbnails' === format ) && this.html5Gallery } );

		let checkedPostTypes = post_types;
		if( isUndefined( post_types ) || ! post_types ) {
			// Use the post type from the current post if not set.
			checkedPostTypes = editorData.postType;
		}

		const inspectorControls = (
			<InspectorControls>
				<PanelBody title={ __( 'Related Posts Settings' ) }>
					<div className={this.props.className + '-inspector-controls'}>
						<div>
							<p>
							{ __( 'Note: The preview style is not the actual style used in the front end of your site.' ) }
							</p>
						</div>
						<BaseControl label={ __( 'Title'  ) } id={titleID}>
							<input className="components-text-control__input"
								type="text"
								onChange={this.onTitleChange}
								defaultValue={title}
								id={titleID}
							/>
						</BaseControl>
						<PostsPanel
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
							postTypes={ checkedPostTypes }
							onPostTypesChange={ this.updatePostTypes }
						/>
					</div>
				</PanelBody>
				<PanelBody title={ __( 'Image Settings' ) }>
					<ImagePanel
						imageSize={image_size}
						onImageSizeChange={ ( value ) => setAttributes( { image_size: value } ) }
						columns={columns}
						onColumnsChange={ ( value ) => setAttributes( { columns: Number( value ) } ) }
					/>
					<ToggleControl
						label={ __( ' Link image captions to posts' ) }
						checked={ link_caption }
						onChange={ this.toggleLinkCaption }
					/>
				</PanelBody>
			</InspectorControls>
			);


		let showPosts = this.previewExpanded;
		if( ! showPosts ) {
			// Show posts when block is selected
			showPosts = isSelected;
		}

		let html       = '';
		let postsFound = 0;

		const queryFinished = ! isUndefined( relatedPosts );
		if( queryFinished ) {
			if( relatedPosts.hasOwnProperty('posts') ) {
				postsFound = relatedPosts.posts.length ? relatedPosts.posts.length : 0;
			}
			if( relatedPosts.hasOwnProperty('rendered') ) {
				html = relatedPosts.rendered.length ? relatedPosts.rendered : '';
			}
		}

		if ( ! showPosts || ! html.length || ! postsFound  ) {
			return (
				<Fragment>
					{ inspectorControls }
					<LoadingPlaceholder
						className={className}
						queryFinished={queryFinished}
						postsFound={postsFound}
						showPosts={showPosts}
						html={html.length}
						editorTerms={editorData.termIDs}
						editorTaxonomies={editorData.taxonomyNames}
						icon="megaphone"
						label={ __( 'Related Posts by Taxonomy' ) }
					/>
				</Fragment>
			);
		}

		// Add target blank to all links
		// Todo: find a better way to do this
		html = relatedPosts.rendered.replace(/\<a href\=/g, '<a target="_blank" href=');

		return (
				<Fragment>
					{ inspectorControls }
					<div className={className}>
						<RawHTML>{html}</RawHTML>
					</div>
				</Fragment>
		);
	}
}

export default compose( [
	relatedPosts,
] )( RelatedPostsBlock );
