/**
 * External dependencies
 */
import { isEmpty } from 'lodash';

/**
 * WordPress dependencies
 */
const { withInstanceId, BaseControl } = wp.components;
const { Component } = wp.element;

/**
 * Internal dependencies
 */
import { getPluginData, inPluginData } from '../data/plugin';

function getPostTypeObjects( checkedPostTypes = [] ) {
	let postTypeOjects = [];

	const postTypes = getPluginData( 'post_types' );
	for (var key in postTypes) {
		if ( ! postTypes.hasOwnProperty( key ) ) {
			continue;
		}

		postTypeOjects.push({
			post_type: key,
			label: postTypes[key],
			checked: ( -1 !== checkedPostTypes.indexOf( key ) ),
		});
	}

	return postTypeOjects;
}

class PostTypeControl extends Component {
	constructor() {
		super( ...arguments );
		const { postTypes } = this.props;

		// Set the state with post type objects.
		this.state = {
			items: getPostTypeObjects( postTypes.split(",") ),
		}
	}

	onChange( index ) {
		// Update the state.
		let newItems = this.state.items.slice();
		newItems[index].checked = !newItems[index].checked
		this.setState({
			items: newItems
		});

		const checked = this.state.items.filter( item => item.checked );
		const postTypes = checked.map( (obj) => obj.post_type );

		if ( this.props.onChange ) {
			this.props.onChange( postTypes.join(',') );
		}
	}

	render() {
		const { label, help, instanceId, postTypes  } = this.props;
		const id = 'inspector-multi-checkbox-control-' + instanceId;

		let describedBy;
		if ( help ) {
			describedBy = id + '__help';
		}

		let checked = postTypes.split(",");
		checked = checked.filter( item => inPluginData( 'post_types', item ) );

		return ! isEmpty( this.state.items ) && (
			<BaseControl label={ label } id={ id } help={ help } className="blocks-checkbox-control">
				{ this.state.items.map( ( option, index ) =>
					<div
						key={ ( id + '-' + index ) }
						className="blocks-checkbox-control__option"
					>
						<input
							id={ ( id + '-' + index ) }
							className="blocks-checkbox-control__input"
							type="checkbox"
							name={ id + '-' + index}
							value={ option.post_type }
							onChange={this.onChange.bind(this, index)}
							checked={ ! ( checked.indexOf( option.post_type ) === -1 ) }
							aria-describedby={ !! help ? id + '__help' : undefined }
						/>
						<label key={ option.post_type } htmlFor={ ( id + '-' + index ) }>
							{ option.label }
						</label>
					</div>
				) }
			</BaseControl>
		);
	}
}

export default withInstanceId( PostTypeControl );
