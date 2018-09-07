/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { SelectControl, RangeControl, ToggleControl } = wp.components;


/**
 * Internal dependencies
 */
import { getPluginData } from '../data/data';
import { getOptions } from '../data/options';
import PostTypeControl from '../components/post-type-control';

// Select input options
const taxonomyOptions = getTaxonomyOptions();
const formatOptions = getOptions('formats');

export default function PostsPanel( {
	taxonomies,
	onTaxonomiesChange,
	postsPerPage,
	onPostsPerPageChange,
	format,
	onFormatChange,
	showDate,
	onShowDateChange,
	postTypes,
	onPostTypesChange,
} ) {

	return [
		onPostsPerPageChange && (
			<RangeControl
				key="rpbt-range-posts-per-page"
				label={ __( 'Number of items' ) }
				value={ postsPerPage }
				onChange={ onPostsPerPageChange }
				min={ -1 }
				max={ 100 }
			/> ),
		onTaxonomiesChange && (
			<SelectControl
				key="rpbt-select-taxonomies"
				label={ __( 'Taxonomies' ) }
				value={ `${ taxonomies }` }
				options={ taxonomyOptions }
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
				label={ __( 'Format' ) }
				value={ `${ format }` }
				options={  formatOptions }
				onChange={ ( value ) => { onFormatChange( value ); } }
			/> ),
		onFormatChange && (
			<ToggleControl
						label={ __( 'Display post date' ) }
						checked={ showDate }
						onChange={ onShowDateChange }
			/>
		),
	];
}

function getTaxonomyOptions() {
	if( ! getPluginData( 'all_tax' ) ) {
		return [];
	}

	const options = [
		{
			label: __( 'all taxonomies' ),
			value: getPluginData( 'all_tax' ),
		},
	];

	return getOptions( 'taxonomies', options );
}
