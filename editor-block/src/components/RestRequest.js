/**
 * External dependencies.
 */
import { isEqual, debounce } from 'lodash';

/**
 * WordPress dependencies.
 */
import { Component, RawHTML } from '@wordpress/element';
import { Placeholder, Spinner } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

export function rendererPath(postID, attributes = null, urlQueryArgs = {}) {
	let queryArgs = (null !== attributes) ? attributes : {};

	// Defaults
	queryArgs.is_editor = true;
	queryArgs.related = true;
	queryArgs.gallery_format = 'editor_block';

	return addQueryArgs(`/related-posts-by-taxonomy/v1/posts/${ postID }`, {
		...queryArgs,
		...urlQueryArgs,
	});
}

export class RestRequest extends Component {
	constructor(props) {
		super(props);
		this.state = {
			response: null,
		};
	}

	componentDidMount() {
		this.isStillMounted = true;
		this.fetch(this.props);
		// Only debounce once the initial fetch occurs to ensure that the first
		// renders show data as soon as possible.
		this.fetch = debounce(this.fetch, 500);
	}

	componentWillUnmount() {
		this.isStillMounted = false;
	}

	componentDidUpdate(prevProps) {
		if (!isEqual(prevProps.attributes, this.props.attributes)) {
			this.fetch(this.props);
		}
	}

	fetch(props) {
		if (!this.isStillMounted) {
			return;
		}
		if (null !== this.state.response) {
			this.setState({ response: null });
		}

		const { postID, attributes = null, urlQueryArgs = {} } = props;

		if (!attributes['terms']) {
			// No need to fetch related posts
			this.setState({ response: '' });
			return this.currentFetchRequest;
		}

		const path = rendererPath(postID, attributes, urlQueryArgs);

		// Store the latest fetch request so that when we process it, we can
		// check if it is the current request, to avoid race conditions on slow networks.
		const fetchRequest = this.currentFetchRequest = apiFetch({ path })
			.then((response) => {
				if (this.isStillMounted && fetchRequest === this.currentFetchRequest && response) {
					this.setState({ response: response.rendered });
				}
			})
			.catch((error) => {
				if (this.isStillMounted && fetchRequest === this.currentFetchRequest) {
					this.setState({
						response: {
							error: true,
							errorMsg: error.message,
						}
					});
				}
			});
		return fetchRequest;
	}

	render() {
		const response = this.state.response;
		const { className, EmptyResponsePlaceholder, ErrorResponsePlaceholder, LoadingResponsePlaceholder } = this.props;

		if (response === '') {
			return (
				<EmptyResponsePlaceholder response={ response } { ...this.props } />
			);
		} else if (!response) {
			return (
				<LoadingResponsePlaceholder response={ response } { ...this.props } />
			);
		} else if (response.error) {
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
	EmptyResponsePlaceholder: ({ notice, label, hideEmpty, message }) => {
		let displayMessage = __('This block will not be displayed.', 'related-posts-by-taxonomy');
		let noticeMessage = ' ' + __('There are no related posts found with the current block settings.', 'related-posts-by-taxonomy');

		if (!hideEmpty && message.length) {
			// No posts found message
			displayMessage = sprintf(__('This block will be displayed with the message: "%s".', 'related-posts-by-taxonomy'), message);
			noticeMessage = ' ' + __('Try using different block settings.', 'related-posts-by-taxonomy');
		}

		if (notice.length) {
			// No terms or taxonomies
			noticeMessage = ' ' + notice;
		}
		return (
			<Placeholder label={label}>
				{displayMessage}
				{noticeMessage}
			</Placeholder>
		);
	},
	ErrorResponsePlaceholder: ({ response, label }) => {
		// translators: %s: error message describing the problem
		const errorMessage = sprintf(__('Error loading block: %s', 'related-posts-by-taxonomy'), response.errorMsg);
		return (
			<Placeholder label={label}>
				{ errorMessage }
			</Placeholder>
		);
	},
	LoadingResponsePlaceholder: ({ label }) => {
		return (
			<Placeholder label={label}>
				<Spinner />
			</Placeholder>
		);
	},
};

export default RestRequest;