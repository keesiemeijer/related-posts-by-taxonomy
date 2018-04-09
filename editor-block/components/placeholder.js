/**
 * WordPress dependencies
 */
const { Spinner, Placeholder } = wp.components;
const { __ } = wp.i18n;

function LoadingPlaceholder( {
	icon,
	label,
	postsFound,
	queryFinished,
	editorTerms,
	editorTaxonomies,
	className,
	} ) {

	let message = '';
	let loading = ! queryFinished ? __( 'Loading related posts' ) : '';

	if( queryFinished && ! postsFound ) {
		loading = (<div>{__( 'No related posts found' )}</div>);
	}

	if ( ! editorTerms.length ) {
		message = __( 'There are no taxonomy terms assigned to this post' );
		if( ! editorTaxonomies.length ) {
			message = __( "This post type doesn't support any taxonomies" );
			loading = '';
		}
	} else {
		message = (
			<ul>
				<li>{__( 'Check if the settings for this block are correct' )}</li>
				<li>{__( 'Check if there are other posts with the same taxonomy terms' )}</li>
			</ul> );
	}

	const preview = (<div><a href="#">{ __( 'preview related posts' ) }</a></div>);
	const instructions = (<div class="instructions">{message}</div>);

	return (
		<Placeholder
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
