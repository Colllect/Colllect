const fs = require('fs')
const path = require('path')
const needle = require('needle')
const CodeGen = require('swagger-typescript-codegen').CodeGen

const generatedFolderPath = 'generated'
const generatedApiFilePath = `${generatedFolderPath}/api.ts`
const templatesPath = 'typescriptSwaggerTemplates'

const swaggerUrl = 'https://127.0.0.1/api/doc.json'
const swaggerUrlOptions = {
  headers: {
    host: 'colllect.localhost',
  },
}

console.log('Fetching swagger description file from', swaggerUrl)
needle('get', swaggerUrl, swaggerUrlOptions)
  .then((response) => {
    swaggerCodeGen(response.body)
  })
  .catch((err) => {
    console.error('Unable to fetch swagger description file. Did you run the `docker-compose up` command?')
    console.error(err)
    process.exit(1)
  })

function getTemplate(templateName) {
  return fs.readFileSync(path.join(__dirname, templatesPath, `${templateName}.mustache`), 'utf-8')
}

function swaggerCodeGen (swagger) {
  console.log('Generating TypeScript code...')
  const tsSourceCode = CodeGen.getTypescriptCode({
    className: 'Api',
    swagger,
    template: {
      class: getTemplate('class'),
      method: getTemplate('method'),
      type: getTemplate('type'),
    },
    beautify: true,
    beautifyOptions: {
      indent_size: 2,
    },
  }).replace(/\r\n/g, '\n')
    .replace(/"/g, '\'')
    .replace(/\s\?\s:/g, '?:')
    .replace(/Promise < request\.Response >/g, 'Promise<request.Response>')
    .replace(/(\/\*\*\n)(\s+)(\*[^\n]+)([^*]+)\*\n\s+\*/g, (match, commentStart, indentSpaces, firstLine, otherLines) => {
      return [
        commentStart.replace('\n', ''),
        indentSpaces + ' ' + firstLine,
        ...otherLines.split('\n').map((line) => indentSpaces + ' * ' + line.replace(indentSpaces, '')),
        indentSpaces + ' *',
      ].join('\n')
    })
    .replace(/ {2,4}\*/g, '   *')
    .replace(/[ ]+$/gm, '')
    .replace(/( {3}\*\n){2,}/g, '   *\n') + '\n'

  if (!fs.existsSync(generatedFolderPath)) {
    fs.mkdirSync(generatedFolderPath)
  }
  fs.writeFileSync(generatedApiFilePath, tsSourceCode)

  console.log(generatedApiFilePath, 'was successfully updated!')
}
