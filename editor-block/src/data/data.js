/**
 * External dependencies
 */
import { isUndefined, isObject, isBoolean, isString, get } from 'lodash';

/**
 * Don't use _pluginData directly, use getPluginData()
 */
export const _pluginData = window.km_rpbt_plugin_data || {};

const _defaults = {
	post_types: { type: 'object' },
	taxonomies: { type: 'object' },
	formats: { type: 'object' },
	order: { type: 'object' },
	image_sizes: { type: 'object' },
	default_tax: { type: 'string' },
	default_category: { type: 'string' },
	html5_gallery: {
		type: 'bool',
		default: false,
	},
	show_date: {
		type: 'bool',
		default: false,
	}
}

/**
 * Check if a property key exists and has a value.
 * 
 * @param  {string} key Property to check.
 * @return {[type]}     Returns true if it exists
 */
export function hasData( object, key ) {
	if( isObject( object ) && object.hasOwnProperty( key ) ) {
		return ! isUndefined( object[ key ]);
	}
	return false;
}

/**
 * Check if a value exists in a plugin data property.
 * 
 * @param  {string} key  Plugin data key.
 * @param  {string} value Value to test.
 * @return {bool}   True if value exists.
 */
export function inPluginData( key, value ) {
	return hasData( getPluginData( key ), value );
}

/**
 * Get data provided by this plugin.
 *
 * Only returns data if it's the correct type.
 * Else returns empty value of the correct type.
 * 
 * @param  {string} key Property key in the plugin data.
 * @return {[type]}     Plugin data.
 */
export function getPluginData( key ) {
	const defaultValue = getDefault( key );

	if( ! hasData( _pluginData, key ) || isUndefined( defaultValue ) ) {
		return defaultValue;
	}

	const data = _pluginData[ key ];
	const dataType = get( _defaults, key + '.type' );

	return isType( dataType , data ) ? data : defaultValue;
}

/**
 * Get the default value for a setting.
 *
 * Booleans should always provide a default value.
 * If no default is provided an empty value with 
 * the correct type is returned.
 * 
 * @param  {string} key Plugin data property key.
 * @return {object|string|bool} Default value.
 */
function getDefault( key ) {
	// Types to check. Booleans should have a default.
	const types = {
		object: {},
		string: '', 
	}

	const keyValue = get( _defaults, key + '.default' );
	const keyDefault = get( types, get( _defaults, key + '.type' ) );

	return ! isUndefined( keyValue ) ? keyValue : keyDefault;
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
	switch ( type ) {
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