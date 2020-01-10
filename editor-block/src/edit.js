/**
 * External dependencies
 */
import { isUndefined, debounce, filter, includes, isArray } from 'lodash';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import { BaseControl, PanelBody, ToggleControl, ServerSideRender, Disabled } from '@wordpress/components';
import { Component, Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { withSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';


/**
 * Internal dependencies
 */
import { getPluginData } from './data/data';
import PostsPanel from './components/posts-panel';
import ImagePanel from './components/image-panel';
import RestRequest from './components/RestRequest';

let instances = 0;

export class RelatedPostsBlock extends Component {
	constructor() {
		super(...arguments);

		// Data provided by this plugin.
		this.html5Gallery = getPluginData('html5_gallery');
		this.defaultCategory = getPluginData('default_category');

		this.updatePostTypes = this.updatePostTypes.bind(this);

		// The title is updated 1 second after a change.
		// This allows the user more time to type.
		this.onTitleChange = this.onTitleChange.bind(this);
		this.titleDebounced = debounce(this.updateTitle, 1000);

		this.toggleLinkCaption = this.createToggleAttribute('link_caption');
		this.toggleShowDate = this.createToggleAttribute('show_date');
		this.toggleImageCrop = this.createToggleAttribute('image_crop');

		this.instanceId = instances++;
	}

	createToggleAttribute(propName) {
		return () => {
			const value = this.props.attributes[propName];
			const { setAttributes } = this.props;

			setAttributes({
				[propName]: !value
			});
		};
	}

	getImageCropHelp(checked) {
		if(checked) {
			return __('Thumbnails are cropped to align.', 'related-posts-by-taxonomy');
		}
		return __('Thumbnails are not cropped.', 'related-posts-by-taxonomy');
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
		const { attributes, setAttributes, postType, postID, termIDs, taxonomyNames } = this.props;
		const { title, taxonomies, post_types, posts_per_page, format, image_size, columns, link_caption, show_date, order, fields, image_crop } = attributes;
		const titleID = 'inspector-text-control-' + this.instanceId;
		const className = classnames(this.props.className, { 'rpbt-html5-gallery': ('thumbnails' === format) && this.html5Gallery });

		if (isUndefined(termIDs) || isUndefined(taxonomyNames)) {
			return null;
		}

		let restAttributes = Object.assign({}, attributes);
		restAttributes['post_id'] = postID;
		restAttributes['include_terms'] = termIDs.join(',');

		if (!restAttributes['include_terms'].length && (-1 !== taxonomyNames.indexOf('category'))) {
			// Use default category if this post supports the 'category' taxonomy and no terms are selected.
			restAttributes['include_terms'] = this.defaultCategory;
		}

		let checkedPostTypes = post_types;
		if (isUndefined(post_types) || !post_types) {
			// Use the post type from the current post if not set.
			checkedPostTypes = postType;
		}

		const inspectorControls = (
			<InspectorControls>
				<PanelBody title={ __( 'Related Posts Settings' , 'related-posts-by-taxonomy') }>
					<div className={this.props.className + '-inspector-controls'}>
						<div>
							<p>
							{ __( 'Note: The preview style is not the actual style used in the front end of your site.' , 'related-posts-by-taxonomy') }
							</p>
						</div>
						<BaseControl label={ __( 'Title'  , 'related-posts-by-taxonomy') } id={titleID}>
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
				<PanelBody title={ __( 'Image Settings' , 'related-posts-by-taxonomy') }>
					<ImagePanel
						imageSize={image_size}
						onImageSizeChange={ ( value ) => setAttributes( { image_size: value } ) }
						columns={columns}
						onColumnsChange={ ( value ) => setAttributes( { columns: Number( value ) } ) }
					/>
					<ToggleControl
							label={ __( 'Crop Images' , 'related-posts-by-taxonomy') }
							checked={ image_crop }
							onChange={ this.toggleImageCrop }
							help={ this.getImageCropHelp }
					/>
					<ToggleControl
						label={ __( ' Link image captions to posts' , 'related-posts-by-taxonomy') }
						checked={ link_caption }
						onChange={ this.toggleLinkCaption }
					/>
				</PanelBody>
			</InspectorControls>
		);

		return (
			<Fragment>
				{inspectorControls}
					<div className={className}>
					<RestRequest
						block="related-posts-by-taxonomy/related-posts-block"
						postID={postID}
						attributes={ restAttributes }
					/>
					</div>
			</Fragment>
		);
	}
}

export default compose(
	withSelect((select, props) => {
		return {
			postID: select('core/editor').getCurrentPostId(),
			postType: select('core/editor').getCurrentPostType(),
			registeredTaxonomies: select('core').getTaxonomies(),
		};
	}),

	withSelect((select, props) => {
		if (!props.registeredTaxonomies || !props.postType || !props.postID) {
			return null;
		}

		const termIDs = [];
		const taxonomyNames = [];

		const taxonomies = props.registeredTaxonomies;
		const postTaxonomies = filter(taxonomies, (taxonomy) => includes(taxonomy.types, props.postType));

		postTaxonomies.map((taxonomy) => {
			taxonomyNames.push(taxonomy.slug);

			const terms = select('core/editor').getEditedPostAttribute(taxonomy.rest_base);
			if (isArray(terms)) {
				termIDs.push(...terms);
			}
		});

		return {
			taxonomyNames: taxonomyNames,
			termIDs: termIDs,
		};
	})
)(RelatedPostsBlock)