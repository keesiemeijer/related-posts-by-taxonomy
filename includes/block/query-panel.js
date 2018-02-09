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

export default function QueryPanel( {
	taxonomies,
	onTaxonomiesChange,
	postsPerPage,
	onPostsPerPageChange,
} ) {
	return [
		onPostsPerPageChange && (
			<RangeControl
				key="query-panel-range-control"
				label={ __( 'Number of items', 'related-posts-by-taxonomy' ) }
				value={ postsPerPage }
				onChange={ onPostsPerPageChange }
				min={ 1 }
				max={ 100 }
			/>
		),
		onTaxonomiesChange && (
			<SelectControl
				key="query-panel-select"
				label={ __( 'Taxonomies', 'related-posts-by-taxonomy' ) }
				value={ `${ taxonomies }` }
				options={  tax_options }
				onChange={ ( value ) => { onTaxonomiesChange( value ); } }
			/> ),
	];
}

function get_taxonomy_options() {
	if( isEmpty( plugin_data )  ) {
		return [];
	}

	let options = [
		{
			label: __( 'all taxonomies', 'related-posts-by-taxonomy' ),
			value: plugin_data.all_tax,
		},
	];

	const taxonomies = plugin_data.taxonomies;

	for (var key in taxonomies) {
		if (taxonomies.hasOwnProperty(key)) { 
			options.push({
				label: taxonomies[key],
				value: key,
			});
		}
	}
	
	return options;
}
