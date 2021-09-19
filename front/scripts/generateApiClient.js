/* eslint-env node, browser:false */

const path = require('path')

const OpenAPI = require('openapi-typescript-codegen')
const needle = require('needle')

const generatedFolderPath = path.normalize('src/generated/api')

const swaggerUrl = 'https://dev.colllect.io/api/doc.json'

console.info('Fetching swagger description file from', swaggerUrl)
needle('get', swaggerUrl)
	.then(async (response) => {
		await OpenAPI.generate({
			input: response.body,
			output: generatedFolderPath,
		})
		console.info(generatedFolderPath, 'was successfully updated!')
	})
	.catch((err) => {
		console.error('Unable to fetch swagger description file. Did you run the `make up` command?')
		console.error(err)
		process.exit(1)
	})
