/* eslint-env node, browser:false */

module.exports = {
	root: true,
	extends: [
		'@alex-d/eslint-config',
	],
	parserOptions: {
		project: './tsconfig.json',
	},
	ignorePatterns: [
		'src/generated/**',
	],
}
