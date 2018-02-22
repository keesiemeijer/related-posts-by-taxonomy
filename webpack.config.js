/**
 * Webpack Configuration
 *
 * Instructions: How to build or develop with this Webpack config:
 *     1. Run the `npm run dev` or `npm run build` for development or
 *        production respectively.
 *
 * @since 2.4.0
 */
const path = require( 'path' );
const webpack = require( 'webpack' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );

const editBlocksCSSPlugin = new ExtractTextPlugin( {
  filename: './includes/assets/css/editor-block.css',
} );

// Configuration for the ExtractTextPlugin.
const extractConfig = {
	use: [{
			loader: 'raw-loader'
		},
		{
			loader: 'postcss-loader',
			options: {
				plugins: [require('autoprefixer')],
			},
		},
		{
			loader: 'sass-loader',
			query: {
				outputStyle: 'production' === process.env.NODE_ENV ? 'compressed' : 'nested',
			},
		},
	],
};

module.exports = {
	entry: {
		'./includes/assets/js/editor-block': './editor-block/index.js',
		'./includes/assets/js/editor-block.min': './editor-block/index.js',
	},
	output: {
		path: path.resolve(__dirname),
		filename: '[name].js',
	},
	module: {
		rules: [
			{
				test: /.js$/,
				loader: 'babel-loader',
				exclude: /node_modules/,
			},
			{
				test: /editor\.s?css$/,
				exclude: /node_modules/,
				use: editBlocksCSSPlugin.extract(extractConfig),
			},
		],
	},
	plugins: [
		 editBlocksCSSPlugin,
		// Minify the code.
		new webpack.optimize.UglifyJsPlugin( {
			include: /editor-block\.min\.js$/,
			compress: {
				warnings: false,
				// Disabled because of an issue with Uglify breaking seemingly valid code:
				// https://github.com/facebookincubator/create-react-app/issues/2376
				// Pending further investigation:
				// https://github.com/mishoo/UglifyJS2/issues/2011
				comparisons: false,
			},
			mangle: {
				safari10: true,
			},
			output: {
				comments: false,
				// Turned on because emoji and regex is not minified properly using default
				// https://github.com/facebookincubator/create-react-app/issues/2488
				ascii_only: true,
			},
			sourceMap: false,
		} ),
	],
};
