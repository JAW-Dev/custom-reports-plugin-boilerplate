module.exports = (options, settings) => {
	if (options.process.images) {
		settings.module.rules.push({
			test: /\.(gif|png|jpe?g|webp|svg)$/i,
			use: [
				{
					loader: 'file-loader',
					options: {
						name: '[path][name].[ext]',
						publicPath: options.images.publicPath
					}
				}
			]
		});
	}
};
