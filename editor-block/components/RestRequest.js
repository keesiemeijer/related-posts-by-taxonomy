/**
 * External dependencies.
 */
import { isEqual, debounce } from 'lodash';

/**
 * WordPress dependencies.
 */
const { Component, RawHTML } = wp.element;
const { Placeholder, Spinner } = wp.components
const { __, sprintf } = wp.i18n;
const apiFetch = wp.apiFetch;
const { addQueryArgs } = wp.url;

export function rendererPath( postID, attributes = null, urlQueryArgs = {} ) {
	let queryArgs = ( null !== attributes ) ? attributes : {};
	queryArgs.editor_block = true;

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
		if ( ! isEqual( prevProps, this.props ) ) {
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
				if ( this.isStillMounted && fetchRequest === this.currentFetchRequest && response && response.rendered ) {
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
		if ( ! response ) {
			return (
				<Placeholder><Spinner /></Placeholder>
			);
		} else if ( response.error ) {
			// translators: %s: error message describing the problem
			const errorMessage = sprintf( __( 'Error loading post: %s' ), response.errorMsg );
			return (
				<Placeholder>{ errorMessage }</Placeholder>
			);
		} else if ( ! response.length ) {
			return (
				<Placeholder>{ __( 'No results found.' ) }</Placeholder>
			);
		}

		return (
			<RawHTML key="html">{ response }</RawHTML>
		);
	}
}

export default RestRequest;
