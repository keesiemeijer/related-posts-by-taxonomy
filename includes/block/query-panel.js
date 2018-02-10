/**
 * External dependencies
 */
import { isEmpty } from 'lodash';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { InspectorControls } = wp.blocks;
const { SelectControl, RangeControl } = InspectorControls;

/**
 * Internal dependencies
 */
const plugin_data = window.km_rpbt_plugin_data || {};
const tax_options = get_taxonomy_options();
const format_options = get_options('formats');
const img_options = get_options('image_sizes');

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
} ) {
	return [
		onPostsPerPageChange && (
			<RangeControl
				key="rpbt-range-posts-per-page"
				label={ __( 'Number of items', 'related-posts-by-taxonomy' ) }
				value={ postsPerPage }
				onChange={ onPostsPerPageChange }
				min={ 1 }
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
				min={ 1 }
				max={ 20 }
			/> ),
	];
}

function get_taxonomy_options() {
	if( ! plugin_data.hasOwnProperty( 'all_tax' ) ) {
		return [];
	}

	const options = [
		{
			label: __( 'all taxonomies', 'related-posts-by-taxonomy' ),
			value: plugin_data.all_tax,
		},
	];

	return get_options('taxonomies', options);
}

function get_options(type, options = []) {
	if( ! plugin_data.hasOwnProperty( type ) ) {
		return [];
	}

	const type_options = plugin_data[ type ];
	for (var key in type_options) {
		if (type_options.hasOwnProperty(key)) {
			options.push({
				label: type_options[key],
				value: key,
			});
		}
	}

	return options;
}
