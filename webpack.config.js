/**
 * Webpack Configuration
 *
 * Block: `02-basic-esnext` — Webpack config file.
 *
 * Working of a Webpack can be very simple or complex. This is an intenally simple
 * build configuration.
 *
 * Webpack basics — If you are new the Webpack here's all you need to know:
 *     1. Webpack is a module bundler. It bundles different JS modules together.
 *     2. It needs and entry point and an ouput to process file(s) and bundle them.
 *     3. By default it only understands common JavaScript but you can make it
 *        understand other formats by way of adding a Webpack loader.
 *     4. In the file below you will find an entry point, an ouput, and a babel-loader
 *        that tests all .js files excluding the ones in node_modules to process the
 *        ESNext and make it compatible with older browsers i.e. it converts the
 *        ESNext (new standards of JavaScript) into old JavaScript through a loader
 *        by Babel.
 *
 * Instructions: How to build or develop with this Webpack config:
 *     1. In the command line browse the folder `02-basic-esnext` where
 *        this `webpack.config.js` file is present.
 *     2. Run the `npm run dev` or `npm run build` for development or
 *        production respectively.
 *     3. To read what these NPM Scripts do, read the `package.json` file.
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

console.log(process.env.NODE_ENV);

module.exports = {
	entry: {
		'./includes/assets/js/editor-block': './block/index.js',
		'./includes/assets/js/editor-block.min': './block/index.js',
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
			include: /\.min\.js$/,
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
