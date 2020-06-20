/* global __basedir */

// Import Modules.
const path = require('path');

const devMode = process.env.NODE_ENV !== 'production';
const sourcePath = `${__basedir}/assets`;
const destinationPath = `${__basedir}/src`;

module.exports = {
	mode: devMode ? 'development' : 'production',
	target: 'web',
	context: path.resolve(__dirname, '../assets'),
	entry: {
		index: ['./../assets/js/index.js', './../assets/css/index.scss'],
		admin: ['./../assets/js/admin.js', './../assets/css/admin.scss']
	},
	output: {
		path: path.join(__dirname, './../src'),
		filename: '[name].js',
		sourceMapFilename: '[file].map'
	},
	sourcePath,
	destinationPath,
	externals: {
		jquery: 'jQuery'
	},
	watch: !!devMode,
	watchOptions: {
		ignored: /node_modules/
	},
	process: {
		js: true,
		css: true,
		images: true,
		fonts: true,
		typescript: false
	},
	sourcemaps: {
		js: true,
		css: true
	},
	minimize: {
		js: !devMode,
		css: !devMode
	},
	images: {
		publicPath: 'src'
	},
	fonts: {
		publicPath: 'src'
	},
	plugins: {
		CleanWebpackPlugin: true,
		MiniCssExtractPlugin: true,
		FileManagerPlugin: true,
		ImageminPlugin: true,
		BundleAnalyzerPlugin: false
	},
	CleanWebpackPlugin: {},
	MiniCssExtractPlugin: {
		filename: '[name].css'
	},
	ImageminPlugin: {
		bail: false,
		cache: true,
		name: '[path][name].[ext]',
		imageminOptions: {
			plugins: [
				['mozjpeg', { progressive: true, quality: 75 }],
				['optipng', { optimizationLevel: 3 }],
				['gifsicle', { interlaced: false }],
				[
					'svgo',
					{
						plugins: [
							{ cleanupAttrs: true },
							{ cleanupEnableBackground: true },
							{ cleanupIDs: true },
							{ cleanupNumericValues: { floatPrecision: 3 } },
							{ collapseGroups: true },
							{ convertColors: true },
							{ convertPathData: true },
							{ convertShapeToPath: true },
							{ convertStyleToAttrs: true },
							{ convertTransform: true },
							{ inlineStyles: true },
							{ mergePaths: true },
							{ minifyStyles: false },
							{ moveElemsAttrsToGroup: true },
							{ moveGroupAttrsToElems: true },
							{ removeComments: true },
							{ removeAttrs: true },
							{ removeDesc: true },
							{ removeDoctype: true },
							{ removeEditorsNSData: true },
							{ removeElementsByAttr: true },
							{ removeEmptyAttrs: true },
							{ removeEmptyContainers: true },
							{ removeEmptyText: true },
							{ removeHiddenElems: true },
							{ removeMetadata: true },
							{ removeNonInheritableGroupAttrs: true },
							{ removeTitle: true },
							{ removeUnknownsAndDefaults: true },
							{ removeUnusedNS: true },
							{ removeUselessDefs: true },
							{ removeUselessStrokeAndFill: true },
							{ removeXMLProcInst: true },
							{ transformsWithOnePath: true },
							{ addAttributesToSVGElement: false },
							{ addClassesToSVGElement: false },
							{ cleanupListOfValues: false },
							{ removeDimensions: true },
							{ removeStyleElement: false },
							{ removeViewBox: false },
							{ removeXMLNS: true },
							{ sortAttrs: false }
						]
					}
				]
			]
		}
	},
	FileManagerPluginDev: {
		silent: true,
		onEnd: {
			mkdir: [`${destinationPath}/styles`, `${destinationPath}/scripts`],
			move: [
				{ source: `${destinationPath}/index.css`, destination: `${destinationPath}/styles/index.css` },
				{ source: `${destinationPath}/index.js`, destination: `${destinationPath}/scripts/index.js` },
				{ source: `${destinationPath}/admin.css`, destination: `${destinationPath}/styles/admin.css` },
				{ source: `${destinationPath}/admin.js`, destination: `${destinationPath}/scripts/admin.js` },
				{ source: `${destinationPath}/index.css.map`, destination: `${destinationPath}/styles/index.css.map` },
				{ source: `${destinationPath}/index.js.map`, destination: `${destinationPath}/scripts/index.js.map` },
				{ source: `${destinationPath}/admin.css.map`, destination: `${destinationPath}/styles/admin.css.map` },
				{ source: `${destinationPath}/admin.js.map`, destination: `${destinationPath}/scripts/admin.js.map` }
			]
		}
	},
	FileManagerPluginProduction: {
		onEnd: {
			mkdir: [`${destinationPath}/styles`, `${destinationPath}/scripts`],
			move: [
				{ source: `${destinationPath}/index.css`, destination: `${destinationPath}/styles/index.css` },
				{ source: `${destinationPath}/index.js`, destination: `${destinationPath}/scripts/index.js` },
				{ source: `${destinationPath}/admin.css`, destination: `${destinationPath}/styles/admin.css` },
				{ source: `${destinationPath}/admin.js`, destination: `${destinationPath}/scripts/admin.js` }
			]
		}
	}
};
