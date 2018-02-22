/**
 * External dependencies
 */
import { isEmpty } from 'lodash';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { SelectControl, RangeControl } = wp.components;

/**
 * Internal dependencies
 */
import { _pluginData, getSelectOptions } from './data';
import PostTypeControl from './post-type-control';
const tax_options = get_taxonomy_options();
const format_options = getSelectOptions('formats');
const img_options = getSelectOptions('image_sizes');

export default function QueryPanel( {
	taxonomies,
	onTaxonomiesChange,
	postsPerPage,
	onPostsPerPageChange,
	format,
	onFormatChange,
	imageSize,
	onImageSizeChange,
	columns,
	onColumnsChange,
	postTypes,
	onPostTypesChange,
} ) {
	return [
		onPostsPerPageChange && (
			<RangeControl
				key="rpbt-range-posts-per-page"
				label={ __( 'Number of items', 'related-posts-by-taxonomy' ) }
				value={ postsPerPage }
				onChange={ onPostsPerPageChange }
				min={ -1 }
				max={ 100 }
			/> ),
		onTaxonomiesChange && (
			<SelectControl
				key="rpbt-select-taxonomies"
				label={ __( 'Taxonomies', 'related-posts-by-taxonomy' ) }
				value={ `${ taxonomies }` }
				options={  tax_options }
				onChange={ ( value ) => { onTaxonomiesChange( value ); } }
			/> ),
		onPostTypesChange && (
			<PostTypeControl
				label={ __( 'Post Types' ) }
				onChange={ onPostTypesChange }
				postTypes={ postTypes }
			/> ),
		onFormatChange && (
			<SelectControl
				key="rpbt-select-format"
				label={ __( 'Format', 'related-posts-by-taxonomy' ) }
				value={ `${ format }` }
				options={  format_options }
				onChange={ ( value ) => { onFormatChange( value ); } }
			/> ),
		onImageSizeChange && (
			<SelectControl
				key="rpbt-select-image-size"
				label={ __( 'Image Size', 'related-posts-by-taxonomy' ) }
				value={ `${ imageSize }` }
				options={  img_options }
				onChange={ ( value ) => { onImageSizeChange( value ); } }
			/> ),
		onColumnsChange && (
			<RangeControl
				key="rpbt-range-columns"
				label={ __( 'Image Columns', 'related-posts-by-taxonomy' ) }
				value={ columns }
				onChange={ onColumnsChange }
				min={ 0 }
				max={ 20 }
			/> ),
	];
}

function get_taxonomy_options() {
	if( ! _pluginData.hasOwnProperty( 'all_tax' ) ) {
		return [];
	}

	const options = [
		{
			label: __( 'all taxonomies', 'related-posts-by-taxonomy' ),
			value: _pluginData.all_tax,
		},
	];

	return getSelectOptions('taxonomies', options);
}