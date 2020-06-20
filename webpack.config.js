global.__basedir = __dirname; // eslint-disable-line

// Import Options.
const options = require('./options/webpack-options');
const stats = require('./options/webpack/stats');
const devServer = require('./options/webpack/devserver');

const devMode = process.env.NODE_ENV !== 'production';

const settings = {
	mode: options.mode,
	target: options.target,
	context: options.context,
	devtool: devMode && (options.sourcemaps.js || options.sourcemaps.css) ? 'source-map' : '',
	entry: options.entry,
	output: options.output,
	externals: options.externals,
	watch: options.watch,
	watchOptions: options.watchOptions,
	optimization: { minimizer: [] },
	module: { rules: [] },
	plugins: [],
	stats,
	devServer
};

// Javascript
const javascript = require('./options/webpack/javascript');

javascript(options, settings);

// CSS
const css = require('./options/webpack/css');

css(options, settings, devMode);

// Images
const images = require('./options/webpack/images');

images(options, settings);

// Fonts
const fonts = require('./options/webpack/fonts');

fonts(options, settings);

// Plugins
const plugins = require('./options/webpack/plugins');

plugins(options, settings);

module.exports = settings;
