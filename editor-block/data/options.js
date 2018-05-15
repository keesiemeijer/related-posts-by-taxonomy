/**
 * Internal dependencies
 */
import { getPluginData } from './data';

export function getOptions(type, options = []) {
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