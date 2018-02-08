/**
 * External dependencies
 */
import { stringify } from 'querystringify';
import { isUndefined, pickBy, debounce } from 'lodash';


import QueryPanel from './query-panel';
const { InspectorControls, BlockDescription } = wp.blocks;
const { BaseControl } = InspectorControls;
const { withAPIData } = wp.components;
const { Component } = wp.element;
const { __ } = wp.i18n;
let instances = 0;

class RelatedPostsBlock extends Component {
	constructor() {
		super( ...arguments );

		this.handleChange = this.handleChange.bind(this);
		this.emitChangeDebounced = debounce( this.emitChange, 1000);
		this.instanceId = instances++;
	}

	componentWillUnmount() {
		this.emitChangeDebounced.cancel();
	}

	handleChange(e) {
		// React pools events, so we read the value before debounce.
		// Alternately we could call `event.persist()` and pass the entire event.
		// For more info see reactjs.org/docs/events.html#event-pooling
		this.emitChangeDebounced(e.target.value);
	}

	emitChange(value) {
		const { setAttributes } = this.props;
		setAttributes( { title: value } );
	}

	render(){
		if ( ! this.props.relatedPostsByTax.data ) {
			return "loading !";
		}
		if ( this.props.relatedPostsByTax.data.length === 0 ) {
			return "No posts";
		}

		const { attributes, focus, setAttributes } = this.props;
		const { title, taxonomies } = attributes;
		const relatedPosts = this.props.relatedPostsByTax.data;
		const textID = 'rpbt-inspector-text-control-' + this.instanceId;
		
		const inspectorControls = focus && (
			<InspectorControls key="inspector">
				<h3>{ __( 'Related Posts Settings' ) }</h3>
				<BaseControl label={ 'Title' } id={ textID }>
					<input className="blocks-text-control__input"
						type="text"
						onChange={this.handleChange}
						defaultValue={title}
						id={textID}
					/>
				</BaseControl>
				<QueryPanel
					taxonomies={ taxonomies }
					onTaxonomiesChange={ ( value ) => setAttributes( { taxonomies: value } ) }
				/>
			</InspectorControls>
			);

		return [
				inspectorControls,
				(<div dangerouslySetInnerHTML={{__html:relatedPosts.rendered}}></div>)
			];
	}
}

export default withAPIData( ( props ) => {
	const { taxonomies, title } = props.attributes;
	const query = stringify( pickBy( {
		taxonomies,
		title,
	}, value => ! isUndefined( value ) ), true );

	return {
		relatedPostsByTax: `/related-posts-by-taxonomy/v1/posts/${_wpGutenbergPost.id}` + `${query}`
	};
} )( RelatedPostsBlock );
