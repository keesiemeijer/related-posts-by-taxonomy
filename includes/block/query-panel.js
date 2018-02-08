/**
 * External dependencies
 */
import { noop } from 'lodash';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;

const { InspectorControls } = wp.blocks;
const { SelectControl } = InspectorControls;
const tax_options = get_taxonomy_options();

export default function QueryPanel( {
	taxonomies,
	onTaxonomiesChange,
} ) {
	return [
		onTaxonomiesChange && (
			<SelectControl
				key="query-panel-select"
				label={ __( 'Taxonomies' ) }
				value={ `${ taxonomies }` }
				options={  tax_options }
				onChange={ ( value ) => { onTaxonomiesChange( value ); } }
			/> ),
	];
}

function get_taxonomy_options() {
	let options = [
		{
			label: __( 'all taxonomies' ),
			value: km_rpbt_plugin_data.all_tax,
		},
	];

	const taxonomies = km_rpbt_plugin_data.taxonomies;

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
