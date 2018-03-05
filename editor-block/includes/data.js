/**
 * External dependencies
 */
import { isUndefined, isObject, isBoolean, isString, has, pickBy, flatten } from 'lodash';

/**
 * Don't use _pluginData directly, use getPluginData()
 */
export const _pluginData = window.km_rpbt_plugin_data || {};

const _defaults = {
	post_types: { type: 'object' },
	taxonomies: { type: 'object' },
	formats: { type: 'object' },
	image_sizes: { type: 'object' },
	default_tax: { type: 'string' },
	all_tax: { type: 'string' },
	preview: {
		type: 'bool',
		default: true, /* required for booleans */
	},
	default_category: { type: 'string' },
}

export function getPluginData( setting ) {
	const defaultValue = getDefault( setting );

	if( ! ( isObject( _pluginData ) && _pluginData.hasOwnProperty( setting ) ) ) {
		return defaultValue;
	}

	let data = _pluginData[ setting ];

	if( has( _defaults, [setting, 'type']) ) {
		const type = _defaults[ setting ]['type'];
		return isType( type , data ) ? data : defaultValue;
	}

	return data;
}

export function inPluginData( type, value) {
	const data = getPluginData( type );
	return ! ( Object.keys( data ).indexOf( value ) === -1 );
}

export function getPostField(field) {
	// Todo: Check if there is a native function to return current post fields.
	if ( isUndefined( _wpGutenbergPost ) ) {
		return '';
	}

	if ( ! _wpGutenbergPost.hasOwnProperty(field) ) {
		return '';
	}

	return _wpGutenbergPost[field];
}

export function getTermIDs( taxonomies ) {
	const ids = pickBy( taxonomies, value => value.length );
	let terms = Object.keys( ids ).map( ( tax ) => ids[ tax ]);
	
	return flatten( terms );
}

function getTaxonomiesFromObject( obj ){
	const pluginTaxonomies = getPluginData( 'taxonomies' );
	let taxonomies = {};
	for (var key in pluginTaxonomies) {
		let tax = key;
		if ( ( 'category' === key ) || ( 'post_tag' === key ) ) {
			tax = ('category' === key) ? 'categories' : 'tags';
		}
	
		if (obj.hasOwnProperty(tax)) {
			taxonomies[key] = obj[tax];
		}
	}
	return taxonomies;
}

export function getTermCount( taxonomies ) {
	taxonomies = getTaxonomiesFromObject( taxonomies )
	let terms = Object.keys( taxonomies );

	return terms
		.map( ( tax ) => taxonomies[tax].length )
		.reduce(function(a, b) { return a + b; }, 0);
}

export function getSelectOptions(type, options = []) {
	const type_options = getPluginData( type );
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

/**
 * Get the default value for a setting.
 *
 * @param  {string} setting Setting name.
 * @return {object|string|bool} Default value.
 */
function getDefault( setting ) {
	const types = {
		object: {},
		string: '',
		int: 0,
	}

	if( has( _defaults, [setting, 'default']) ) {
		return _defaults[setting]['default'];
	}

	return types[ _defaults[ setting ]['type'] ];
}

/**
 * Check if a value has the correct type.
 *
 * @param  {string}             type  Type of value. Accepts 'bool', 'object' and 'string'.
 * @param  {bool|object|string} value Value.
 * @return {Boolean} True if the value is of the correct type.
 */
function isType( type, value ) {
	let is_type = false;
	switch (type) {
		case 'bool':
			value = getBool( value );
			is_type = isBoolean( value );
		break;
		case 'object':
			is_type = isObject( value );
		break;
		case 'string':
			is_type = isString( value );
		break;
	}

	return is_type;
}

/**
 * Get a boolean value from a string.
 *
 * wp_localize_script converts booleans to a string ('1' or '').
 *
 * @param  {string} value String with boolean value.
 * @return {bool} Boolean value if string is '1' or empty.
 */
function getBool( value ) {
	if( ! isString( value ) ) {
		return value;
	}
	const bool = Number( value.trim() );
	return ( 1 === bool || 0 === bool ) ? ( 1 === bool ) : value;
}