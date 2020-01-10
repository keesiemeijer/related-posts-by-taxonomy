/**
 * WordPress dependencies
 */
import {  __  } from '@wordpress/i18n';
import {  SelectControl, RangeControl, ToggleControl  } from '@wordpress/components';


/**
 * Internal dependencies
 */
import { getPluginData } from '../data/data';
import { getOptions } from '../data/options';
import PostTypeControl from '../components/post-type-control';

// Select input options
const taxonomyOptions = getTaxonomyOptions();
const formatOptions = getOptions('formats');
const orderOptions = getOptions('order');

export default function PostsPanel({
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
	order,
	onOrderChange,
}) {

	return [
		onPostsPerPageChange && (
			<RangeControl
				key="rpbt-range-posts-per-page"
				label={ __( 'Number of items' , 'related-posts-by-taxonomy') }
				value={ postsPerPage }
				onChange={ onPostsPerPageChange }
				min={ -1 }
				max={ 100 }
			/>),
		onTaxonomiesChange && (
			<SelectControl
				key="rpbt-select-taxonomies"
				label={ __( 'Taxonomies' , 'related-posts-by-taxonomy') }
				value={ `${ taxonomies }` }
				options={ taxonomyOptions }
				onChange={ ( value ) => { onTaxonomiesChange( value ); } }
			/>),
		onPostTypesChange && (
			<PostTypeControl
				label={ __( 'Post Types' , 'related-posts-by-taxonomy') }
				onChange={ onPostTypesChange }
				postTypes={ postTypes }
			/>),
		onOrderChange && (
			<SelectControl
				key="rpbt-select-order"
				label={ __( 'Order posts' , 'related-posts-by-taxonomy') }
				value={ `${ order }` }
				options={  orderOptions }
				onChange={ ( value ) => { onOrderChange( value ); } }
			/>),
		onFormatChange && (
			<SelectControl
				key="rpbt-select-format"
				label={ __( 'Format' , 'related-posts-by-taxonomy') }
				value={ `${ format }` }
				options={  formatOptions }
				onChange={ ( value ) => { onFormatChange( value ); } }
			/>),
		onShowDateChange && (
			<ToggleControl
				label={ __( 'Display post date' , 'related-posts-by-taxonomy') }
				checked={ showDate }
				onChange={ onShowDateChange }
			/>
		),
	];
}

function getTaxonomyOptions() {
	const options = [{
		label: __('all taxonomies', 'related-posts-by-taxonomy'),
		value: 'km_rpbt_all_tax',
	}, ];

	return getOptions('taxonomies', options);
}