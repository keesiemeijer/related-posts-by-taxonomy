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

	if( queryFinished ) {
		if(!postsFound) {
			loading = (<div>{__( 'No related posts found', 'related-posts-by-taxonomy' )}</div>);
		}
	} else {
		loading = __( 'Loading related posts', 'related-posts-by-taxonomy');
	}

	if ( ! editorTerms.length ) {
		message = __( 'There are no taxonomy terms assigned to this post', 'related-posts-by-taxonomy' );
		if( ! editorTaxonomies.length ) {
			message = __( "This post type doesn't support any taxonomies", 'related-posts-by-taxonomy' );
			loading = '';
		}
	} else {
		message = (
			<ul>
				<li>{__( 'Check if the settings for this block are correct', 'related-posts-by-taxonomy' )}</li>
				<li>{__( 'Check if there are other posts with the same taxonomy terms', 'related-posts-by-taxonomy' )}</li>
			</ul> );
	}

	const preview = (<div><a href="#">{ __( 'preview related posts', 'related-posts-by-taxonomy' ) }</a></div>);
	const instructions = (<div class="instructions">{message}</div>);

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
