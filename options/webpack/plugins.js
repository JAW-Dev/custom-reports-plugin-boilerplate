// Import Packages.
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FileManagerPlugin = require('filemanager-plus-webpack-plugin');
const ImageminPlugin = require('imagemin-webpack');
const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer');

module.exports = (options, settings) => {
	// https://github.com/jantimon/html-webpack-plugin
	if (options.plugins.CleanWebpackPlugin) {
		settings.plugins.push(new CleanWebpackPlugin(options.CleanWebpackPlugin));
	}

	// https://github.com/webpack-contrib/mini-css-extract-plugin
	if (options.plugins.MiniCssExtractPlugin) {
		settings.plugins.push(new MiniCssExtractPlugin(options.MiniCssExtractPlugin));
	}

	// https://github.com/jantimon/html-webpack-plugin
	if (options.plugins.HtmlWebpackPlugin) {
		settings.plugins.push(new HtmlWebpackPlugin(options.HtmlWebpackPlugin));
	}

	// https://github.com/gregnb/filemanager-webpack-plugin
	if (options.plugins.FileManagerPlugin) {
		if (options.mode === 'development') {
			settings.plugins.push(new FileManagerPlugin(options.FileManagerPluginDev, { silent: true }));
		} else {
			settings.plugins.push(new FileManagerPlugin(options.FileManagerPluginProduction, { silent: true }));
		}
	}

	// https://github.com/itgalaxy/imagemin-webpack
	if (options.plugins.ImageminPlugin) {
		settings.plugins.push(new ImageminPlugin(options.ImageminPlugin));
	}

	// https://github.com/webpack-contrib/webpack-bundle-analyzer
	if (options.plugins.BundleAnalyzerPlugin) {
		settings.plugins.push(new BundleAnalyzerPlugin({ analyzerMode: 'static' }));
	}
};
