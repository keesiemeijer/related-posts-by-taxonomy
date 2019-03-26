/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;
const { SelectControl, RangeControl } = wp.components;

/**
 * Internal dependencies
 */
import { getPluginData } from '../data/data';
import { getOptions } from '../data/options';

// Select input options
const imageOptions = getOptions('image_sizes');

export default function ImagePanel( {
	imageSize,
	onImageSizeChange,
	columns,
	onColumnsChange
} ) {

	return [
		onImageSizeChange && (
			<SelectControl
				key="rpbt-select-image-size"
				label={ __( 'Image Size' ) }
				value={ `${ imageSize }` }
				options={  imageOptions }
				onChange={ ( value ) => { onImageSizeChange( value ); } }
			/> ),
		onColumnsChange && (
			<RangeControl
				key="rpbt-range-columns"
				label={ __( 'Image Columns' ) }
				value={ columns }
				onChange={ onColumnsChange }
				min={ 0 }
				max={ 20 }
			/> ),
	];
}
