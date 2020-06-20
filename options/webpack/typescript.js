module.exports = (options, settings) => {
	if (options.process.typescript) {
		settings.module.rules.push({
			test: /\.(tsx?)$/,
			exclude: /node_modules/,
			use: {
				loader: 'ts-loader'
			}
		});
	}
};
