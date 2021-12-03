/* eslint-env node, browser:false */

const fs = require('fs')
const path = require('path')

const openapiTS = require('openapi-typescript').default

const openApiUrl = 'https://dev.colllect.io/api/doc.json'
const destinationDirectory = path.join(__dirname, '..', 'src', 'generated')
const openApiJsonPath = path.join(destinationDirectory, 'api.json')
const apiTypesOutputPath = path.join(destinationDirectory, 'apiTypes.ts')

const openApiInputPath = fs.existsSync(openApiJsonPath) ? openApiJsonPath : openApiUrl

console.info('Fetching OpenAPI definition file from', openApiUrl)
openapiTS(openApiInputPath)
	.then((apiTypes) => {
		fs.mkdirSync(destinationDirectory, { recursive: true })
		return fs.writeFileSync(apiTypesOutputPath, apiTypes)
	})
	.catch((err) => {
		console.error('Unable to fetch OpenAPI definition file. Did you run the `make up` command?')
		console.error(err)
		process.exit(1)
	})
