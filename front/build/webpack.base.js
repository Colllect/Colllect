'use strict'
const path = require('path')
const config = require('./config')

const postcss = [
  require('autoprefixer')({
    browsers: [ 'last 2 versions', 'ie > 8' ]
  }),
  require('css-mqpacker')()
]

let webpackBase = {
  entry: config.entry,
  output: {
    path: config.assets_path,
    filename: '[name].js',
    publicPath: config.assets_url
  },
  resolve: {
    extensions: [ '', '.js', '.vue', '.css', '.json' ],
    alias: {
      root: path.join(__dirname, '../js'),
      components: path.join(__dirname, '../js/components'),
      vue: 'vue/dist/vue.js'
    }
  },
  module: {
    preLoaders: [
      {
        test: /\.vue$/,
        loader: 'eslint',
        exclude: [ /node_modules/ ]
      },
      {
        test: /\.js$/,
        loader: 'eslint',
        exclude: [ /node_modules/, /libs/ ]
      }
    ],
    loaders: [
      {
        test: /\.vue$/,
        loaders: [ 'vue' ]
      },
      {
        test: /\.js$/,
        loader: 'babel',
        exclude: [ /node_modules/, /libs/ ]
      },
      {
        test: /\.scss$/,
        vue: 'scss',
        loaders: [ 'css', 'postcss', 'sass' ]
      },
      {
        test: /\.css$/,
        loaders: [ 'css', 'postcss' ]
      }, {
        test: /\.(png|jpe?g|gif|svg|woff2?|eot|ttf|otf|wav)(\?.*)?$/,
        loader: 'url',
        query: {
          limit: 10,
          name: '[name].[hash:7].[ext]'
        }
      }
    ]
  },
  babel: {
    babelrc: false,
    presets: [
      'es2015',
      'stage-2'
    ],
    plugins: [ 'transform-runtime' ]
  },
  postcss,
  vue: {
    loaders: {},
    postcss
  },
  plugins: [],
  devServer: {
    headers: { 'Access-Control-Allow-Origin': '*' }
  }
}

if (config.html) {
  const HtmlWebpackPlugin = require('html-webpack-plugin')
  webpackBase.plugins.push(
    new HtmlWebpackPlugin({
      filename: 'index.html',
      template: 'index.html',
      inject: true
    })
  )
}

module.exports = webpackBase
