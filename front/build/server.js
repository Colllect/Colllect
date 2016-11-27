'use strict'
const WebpackDevServer = require('webpack-dev-server')
const webpack = require('webpack')
const webpack_dev = require('./webpack.dev')
const config = require('./config')
const compiler = webpack(webpack_dev)
const hotMiddleware = require('webpack-hot-middleware')(compiler)
const chokidar = require('chokidar')

// Force le rafraichissement du navigateur
let refresh = function (path) {
    console.log('* ' + path + ' changed')
    hotMiddleware.publish({action: 'reload'})
}

let server = new WebpackDevServer(compiler, {
  hot: true,
  historyApiFallback: false,
  quiet: false,
  noInfo: false,
  publicPath: webpack_dev.output.publicPath,
  stats: {
    colors: true,
    chunks: false
  }
})
server.use(hotMiddleware)
server.listen(3003, function (err) {
  if (err) {
    console.log(err)
    return
  }
  chokidar.watch(config.refresh).on('change', refresh)
  console.log('==> Listening on http://localhost:3003')
})
