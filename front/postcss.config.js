/* eslint-env node, browser:false */

module.exports = () => {
	const plugins = [
		require('postcss-easing-gradients'),
		require('autoprefixer'),
	]

	return {
		plugins,
	}
}
