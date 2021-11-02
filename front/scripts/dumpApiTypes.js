/* eslint-env node, browser:false */

const fs = require('fs')
const path = require('path')

const openapiTS = require('openapi-typescript').default

const openApiUrl = 'https://dev.colllect.io/api/doc.json'
const openApiDefinitionPath = path.join(__dirname, '..', 'src', 'generated', 'apiTypes.ts')

console.info('Fetching OpenAPI definition file from', openApiUrl)
openapiTS(openApiUrl)
	.then((apiTypes) => {
		return fs.writeFileSync(openApiDefinitionPath, apiTypes)
	})
	.catch((err) => {
		console.error('Unable to fetch OpenAPI definition file. Did you run the `make up` command?')
		console.error(err)
		process.exit(1)
	})
