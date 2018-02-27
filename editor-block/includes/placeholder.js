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
	className,
	} ) {

	var placeholderStyle = {
		minHeight: '100px',
	};

	let loading = '';
	const preview = (<a href="#">{ __('preview related posts', 'related-posts-by-taxonomy') }</a>)
	const instructions = (
		<div class="instructions">
			{ __( 'Assign (more) terms or change the settings of this block', 'related-posts-by-taxonomy' ) }
		</div>);

	if( queryFinished ) {
		if(!postsFound) {
			loading = __( 'No related posts found!', 'related-posts-by-taxonomy' );
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
