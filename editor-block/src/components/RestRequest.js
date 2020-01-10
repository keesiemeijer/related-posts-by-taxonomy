/**
 * External dependencies.
 */
import { isEqual, debounce } from 'lodash';

/**
 * WordPress dependencies.
 */
import {  Component, RawHTML  } from '@wordpress/element';
import { Placeholder, Spinner } from '@wordpress/components';
import {  __, sprintf  } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import {  addQueryArgs  } from '@wordpress/url';

export function rendererPath( postID, attributes = null, urlQueryArgs = {} ) {
	let queryArgs = ( null !== attributes ) ? attributes : {};

	// Defaults
	queryArgs.gallery_format = 'editor_block';
	queryArgs.is_editor = true;

	return addQueryArgs( `/related-posts-by-taxonomy/v1/posts/${ postID }`, {		
		...queryArgs,
		...urlQueryArgs,
	} );
}

export class RestRequest extends Component {
	constructor( props ) {
		super( props );
		this.state = {
			response: null,
		};
	}

	componentDidMount() {
		this.isStillMounted = true;
		this.fetch( this.props );
		// Only debounce once the initial fetch occurs to ensure that the first
		// renders show data as soon as possible.
		this.fetch = debounce( this.fetch, 500 );
	}

	componentWillUnmount() {
		this.isStillMounted = false;
	}

	componentDidUpdate( prevProps ) {
		if ( ! isEqual( prevProps.attributes, this.props.attributes ) ) {
			this.fetch( this.props );
		}
	}

	fetch( props ) {
		if ( ! this.isStillMounted ) {
			return;
		}
		if ( null !== this.state.response ) {
			this.setState( { response: null } );
		}

		const { postID, attributes = null, urlQueryArgs = {} } = props;

		const path = rendererPath( postID, attributes, urlQueryArgs );

		// Store the latest fetch request so that when we process it, we can
		// check if it is the current request, to avoid race conditions on slow networks.
		const fetchRequest = this.currentFetchRequest = apiFetch( { path } )
			.then( ( response ) => {
				if ( this.isStillMounted && fetchRequest === this.currentFetchRequest && response ) {
					this.setState( { response: response.rendered } );
				}
			} )
			.catch( ( error ) => {
				if ( this.isStillMounted && fetchRequest === this.currentFetchRequest ) {
					this.setState( { response: {
						error: true,
						errorMsg: error.message,
					} } );
				}
			} );
		return fetchRequest;
	}

	render() {
		const response = this.state.response;
		const { className, EmptyResponsePlaceholder, ErrorResponsePlaceholder, LoadingResponsePlaceholder } = this.props;

		if ( response === '' ) {
			return (
				<EmptyResponsePlaceholder response={ response } { ...this.props } />
			);
		} else if ( ! response ) {
			return (
				<LoadingResponsePlaceholder response={ response } { ...this.props } />
			);
		} else if ( response.error ) {
			return (
				<ErrorResponsePlaceholder response={ response } { ...this.props } />
			);
		}

		return (
			<RawHTML
				key="html"
				className={ className }
			>
				{ response }
			</RawHTML>
		);
	}
}

RestRequest.defaultProps = {
	EmptyResponsePlaceholder: ( { className } ) => (
		<Placeholder
			className={ className }
		>
			{ __('No posts found with the current block settings', 'related-posts-by-taxonomy') }
		</Placeholder>
	),
	ErrorResponsePlaceholder: ( { response, className } ) => {
		// translators: %s: error message describing the problem
		const errorMessage = sprintf( __( 'Error loading block: %s', 'related-posts-by-taxonomy' ), response.errorMsg );
		return (
			<Placeholder
				className={ className }
			>
				{ errorMessage }
			</Placeholder>
		);
	},
	LoadingResponsePlaceholder: ( { className } ) => {
		return (
			<Placeholder
				className={ className }
			>
				<Spinner />
			</Placeholder>
		);
	},
};

export default RestRequest;
