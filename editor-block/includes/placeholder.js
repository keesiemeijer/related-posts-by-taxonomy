/**
 * WordPress dependencies
 */
const { Spinner, Placeholder } = wp.components;
const { __ } = wp.i18n;

import { getTermIDs } from './data';

function LoadingPlaceholder( {
	icon,
	label,
	postsFound,
	queryFinished,
	editorTerms,
	editorTaxonomies,
	className,
	} ) {

	var placeholderStyle = {
		minHeight: '100px',
	};

	let loading = '';
	let message = '';

	const terms = getTermIDs( editorTerms );
	if ( ! terms.length ) {
		message = __( 'There are no taxonomy terms assigned to this post', 'related-posts-by-taxonomy' );
	} else {
		message = __( 'Check if the settings for this block are correct', 'related-posts-by-taxonomy' );
	}

	const preview = (<div><a href="#">{ __( 'preview related posts', 'related-posts-by-taxonomy' ) }</a></div>);
	const instructions = (<div class="instructions">{message}</div>);

	if( queryFinished ) {
		if(!postsFound) {
			loading = (<div>{__( 'No related posts found!', 'related-posts-by-taxonomy' )}</div>);
		}
	} else {
		loading = __( 'Loading related posts', 'related-posts-by-taxonomy');
	}

	return (
		<Placeholder
			style={placeholderStyle}
			className={className}
			key="placeholder"
			icon={icon}
			label={label}
			>
			{ ! queryFinished ? <Spinner /> : '' }
			{ loading }
			{ postsFound ? preview : '' }
			{ ! postsFound && queryFinished ? instructions : ''}
		</Placeholder>
	);
}

export default LoadingPlaceholder;
