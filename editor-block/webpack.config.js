const defaultConfig = require("./node_modules/@wordpress/scripts/config/webpack.config");
const path = require('path');

module.exports = {
	...defaultConfig,
	entry: {
		'editor-block/build/index': path.resolve(process.cwd(), 'src', 'index.js'),
		'includes/assets/js/editor-block': path.resolve(process.cwd(), 'src', 'index.js'),
	},
	output: {
		filename: '[name].js',
		path: path.dirname(process.cwd()),
	},
};