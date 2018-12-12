module.exports = {
	entry: './assets/js/admin/gutenberg/group-block.js',
	output: {
		path: __dirname,
		filename: 'assets/js/admin/gutenberg/group-block.min.js',
	},
	module: {
		loaders: [
			{
				test: /.js$/,
				loader: 'babel-loader',
				exclude: /node_modules/,
			},
		],
	},
};
