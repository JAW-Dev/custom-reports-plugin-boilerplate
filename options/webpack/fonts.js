module.exports = (options, settings) => {
	if (options.process.fonts) {
		settings.module.rules.push({
			test: /\.(woff2?|ttf|otf|eot)$/,
			use: [
				{
					loader: 'file-loader',
					options: {
						name: '[path][name].[ext]',
						publicPath: options.fonts.publicPath
					}
				}
			]
		});
	}
};
