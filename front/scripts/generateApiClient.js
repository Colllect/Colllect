/* eslint-env node, browser:false */

const path = require('path')

const OpenAPI = require('openapi-typescript-codegen')
const needle = require('needle')

const generatedFolderPath = path.normalize('src/generated/api')

const generate = (input) => {
	OpenAPI.generate({
		input,
		output: generatedFolderPath,
	}).then(() => {
		console.info(generatedFolderPath, 'was successfully updated!')
		process.exit(0)
	}).catch((err) => {
		console.error('Unable to generate client. Is the definition file valid?')
		console.error(err)
		process.exit(1)
	})
}

if (process.env.CI) {
	generate(generatedFolderPath + '.json')
	return
}

const openApiUrl = 'https://dev.colllect.io/api/doc.json'

console.info('Fetching swagger definition file from', openApiUrl)
needle('get', openApiUrl)
	.then((response) => {
		generate(response.body)
	})
	.catch((err) => {
		console.error('Unable to fetch OpenAPI definition file. Did you run the `make up` command?')
		console.error(err)
		process.exit(1)
	})
