/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { SelectControl, RangeControl } = wp.components;

/**
 * Internal dependencies
 */
import { getPluginData } from '../data/plugin';
import PostTypeControl from '../components/post-type-control';

// Select input options
const taxonomyOptions = getTaxonomyOptions();
const formatOptions = getOptions('formats');
const imageOptions = getOptions('image_sizes');

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
				options={ taxonomyOptions }
				onChange={ ( value ) => { onTaxonomiesChange( value ); } }
			/> ),
		onPostTypesChange && (
			<PostTypeControl
				label={ __( 'Post Types', 'related-posts-by-taxonomy' ) }
				onChange={ onPostTypesChange }
				postTypes={ postTypes }
			/> ),
		onFormatChange && (
			<SelectControl
				key="rpbt-select-format"
				label={ __( 'Format', 'related-posts-by-taxonomy' ) }
				value={ `${ format }` }
				options={  formatOptions }
				onChange={ ( value ) => { onFormatChange( value ); } }
			/> ),
		onImageSizeChange && ('thumbnails' === format) && (
			<SelectControl
				key="rpbt-select-image-size"
				label={ __( 'Image Size', 'related-posts-by-taxonomy' ) }
				value={ `${ imageSize }` }
				options={  imageOptions }
				onChange={ ( value ) => { onImageSizeChange( value ); } }
			/> ),
		onColumnsChange && ('thumbnails' === format) && (
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

function getTaxonomyOptions() {
	if( ! getPluginData( 'all_tax' ) ) {
		return [];
	}

	const options = [
		{
			label: __( 'all taxonomies', 'related-posts-by-taxonomy' ),
			value: getPluginData( 'all_tax' ),
		},
	];

	return getOptions( 'taxonomies', options );
}

function getOptions(type, options = []) {
	const typeOptions = getPluginData( type );
	for ( var key in typeOptions ) {
		if ( typeOptions.hasOwnProperty( key ) ) {
			options.push({
				label: typeOptions[ key ],
				value: key,
			});
		}
	}

	return options;
}