import { isUndefined } from 'lodash';

export const pluginData = window.km_rpbt_plugin_data || {};

export const _postTypes = getPostTypes();

export function getPostTypes() {
	if( ! pluginData.hasOwnProperty( 'post_types' ) ) {
		return [];
	}

	return pluginData[ 'post_types' ];
}

export function validatePostType( postType ){
	const postTypes = Object.keys( getPostTypes() );
	return ! ( postTypes.indexOf( postType ) === -1 );
}

export function getPostField(field) {
	// Todo: Check if there is a native function to return current post fields.
	if (isUndefined( _wpGutenbergPost ) ) {
		return '';
	}

	if (! _wpGutenbergPost.hasOwnProperty(field) ) {
		return '';
	}

	return _wpGutenbergPost[field];
}

export function getSelectOptions(type, options = []) {
	if( ! pluginData.hasOwnProperty( type ) ) {
		return [];
	}

	const type_options = pluginData[ type ];
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