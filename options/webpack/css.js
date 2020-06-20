// Import Packages
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const autoPrefixer = require('autoprefixer');
const cssnano = require('cssnano');

module.exports = (options, settings, devMode) => {
	const postcssPlugins = [autoPrefixer({ grid: true })];
	if (options.process.css) {
		settings.module.rules.push({
			test: /\.(sa|sc|le|c)ss$/,
			use: [
				{
					loader: MiniCssExtractPlugin.loader
				},
				{
					loader: 'css-loader',
					options: { sourceMap: !!(devMode && options.sourcemaps.css) }
				},
				{
					loader: 'postcss-loader',
					options: {
						ident: 'postcss',
						plugins: postcssPlugins,
						sourceMap: !!(devMode && options.sourcemaps.css)
					}
				},
				{
					loader: 'sass-loader',
					options: { sourceMap: !!(devMode && options.sourcemaps.css) }
				}
			]
		});
	}

	if (options.minimize.css) {
		postcssPlugins.push(cssnano);
	}
};
