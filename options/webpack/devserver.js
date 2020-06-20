// Import Modules.
const path = require('path')

module.exports = {
	contentBase: path.join(__dirname, 'src'),
	publicPath: 'http://localhost',
	watchContentBase: true,
	port: 8080,
	https: false,
	inline: true,
	hot: true,
	historyApiFallback: true,
	overlay: true,
}