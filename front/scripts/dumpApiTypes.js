/* eslint-env node, browser:false */

const fs = require('fs')
const path = require('path')

const openapiTS = require('openapi-typescript').default

const openApiUrl = 'https://dev.colllect.io/api/doc.json'
const openApiJsonPath = path.join(__dirname, '..', 'src', 'generated', 'api.json')
const apiTypesOutputPath = path.join(__dirname, '..', 'src', 'generated', 'apiTypes.ts')

const openApiInputPath = fs.existsSync(openApiJsonPath) ? openApiJsonPath : openApiUrl

console.info('Fetching OpenAPI definition file from', openApiUrl)
openapiTS(openApiInputPath)
	.then((apiTypes) => {
		return fs.writeFileSync(apiTypesOutputPath, apiTypes)
	})
	.catch((err) => {
		console.error('Unable to fetch OpenAPI definition file. Did you run the `make up` command?')
		console.error(err)
		process.exit(1)
	})
