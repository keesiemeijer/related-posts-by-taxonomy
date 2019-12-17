/**
 * External dependencies
 */
import { isEmpty } from 'lodash';

/**
 * WordPress dependencies
 */
import {  __  } from '@wordpress/i18n';
import {  registerBlockType  } from '@wordpress/blocks';
/**
 * Internal dependencies
 */
import edit from './edit';
import { _pluginData } from './data/data';

if (!isEmpty(_pluginData)) {
	registerRelatedPostsBlock();
}

function registerRelatedPostsBlock() {

	registerBlockType('related-posts-by-taxonomy/related-posts-block', {
		title: __('Related Posts by Taxonomy'),
		icon: 'megaphone',
		category: 'widgets',
		description: __('This block displays related posts by taxonomy.'),
		supports: {
			html: false,
			customClassName: false,
		},
		edit: edit,
		save() {
			// Rendering in PHP
			return null;
		}
	});
}