'use strict'
const webpack = require('webpack')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
const ProgressBarPlugin = require('progress-bar-webpack-plugin')
const AssetsPlugin = require('assets-webpack-plugin')
const webpack_base = require('./webpack.base')
const config = require('./config')

webpack_base.devtool = false
webpack_base.output.filename = '[name].[chunkhash:8].js'
webpack_base.plugins.push(
  new ProgressBarPlugin(),
  new ExtractTextPlugin('[name].[contenthash:8].css'),
  new webpack.DefinePlugin({
    'process.env.NODE_ENV': JSON.stringify('production')
  }),
  new webpack.optimize.UglifyJsPlugin({
    compress: {
      warnings: false
    },
    comments: false
  }),
  new AssetsPlugin({filename: config.assets_path + 'assets.json'})
)

// On extrait le CSS
webpack_base.module.loaders.forEach(function (loader) {
  if (loader.vue) {
    webpack_base.vue.loaders[loader.vue] = ExtractTextPlugin.extract(loader.loaders)
  }
  if (loader.loaders && loader.loaders.includes('css')) {
    loader.loader = ExtractTextPlugin.extract(loader.loaders)
    delete loader['loaders']
  }
})

module.exports = webpack_base
