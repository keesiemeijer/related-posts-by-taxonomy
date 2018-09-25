/**
 * External dependencies
 */
import { isUndefined, debounce } from 'lodash';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
const { InspectorControls } = wp.editor;
const { BaseControl, PanelBody, ToggleControl, ServerSideRender, Disabled } = wp.components;
const { Component, Fragment } = wp.element;
const { __ } = wp.i18n;

/**
 * Internal dependencies
 */
import './editor.scss'
import { getPluginData } from './data/data';
import PostsPanel from './components/posts-panel';
import ImagePanel from './components/image-panel';

let instances = 0;

export class RelatedPostsBlock extends Component {
	constructor() {
		super(...arguments);

		// Data provided by this plugin.
		this.previewExpanded = getPluginData('preview');
		this.html5Gallery = getPluginData('html5_gallery');
		this.defaultCategory = getPluginData('default_category');

		this.updatePostTypes = this.updatePostTypes.bind(this);

		// The title is updated 1 second after a change.
		// This allows the user more time to type.
		this.onTitleChange = this.onTitleChange.bind(this);
		this.titleDebounced = debounce(this.updateTitle, 1000);

		this.toggleLinkCaption = this.createToggleAttribute('link_caption');
		this.toggleShowDate = this.createToggleAttribute('show_date');

		this.instanceId = instances++;
	}

	createToggleAttribute(propName) {
		return () => {
			const value = this.props.attributes[propName];
			const { setAttributes } = this.props;

			setAttributes({
				[propName]: !value });
		};
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
		setAttributes({ title: value });
	}

	updatePostTypes(postTypes) {
		const { setAttributes } = this.props;
		setAttributes({ post_types: postTypes });
	}

	render() {
		const { attributes, setAttributes, editorData, postType, postID } = this.props;
		const { title, taxonomies, post_types, posts_per_page, format, image_size, columns, link_caption, show_date, order } = attributes;
		const titleID = 'inspector-text-control-' + this.instanceId;
		const className = classnames(this.props.className, { 'rpbt-html5-gallery': ('thumbnails' === format) && this.html5Gallery });

		if (isUndefined(editorData)) {
			return null;
		}

		let shortcodeAttr = Object.assign({}, attributes);
		shortcodeAttr['post_id'] = postID;
		shortcodeAttr['terms'] = editorData.termIDs.join(',');

		if (!shortcodeAttr['terms'].length && (-1 !== editorData.taxonomyNames.indexOf('category'))) {
			// Use default category if this post supports the 'category' taxonomy and no terms are selected.
			shortcodeAttr['terms'] = this.defaultCategory;
		}

		let checkedPostTypes = post_types;
		if (isUndefined(post_types) || !post_types) {
			// Use the post type from the current post if not set.
			checkedPostTypes = postType;
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
							order={ order }
							onOrderChange={ ( value ) => setAttributes( { order: value } ) }
							showDate={show_date}
							onShowDateChange={ this.toggleShowDate }
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

		return (
			<Fragment>
				{inspectorControls}
				<Disabled>
					<div className={className}>
					<ServerSideRender
						block="related-posts-by-taxonomy/related-posts-block"
						attributes={ shortcodeAttr }
					/>
					</div>
				</Disabled>
			</Fragment>
		);
	}
}

export default RelatedPostsBlock;