const path = require('path')
const webpack = require('webpack')
const CopyWebpackPlugin = require('copy-webpack-plugin')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
const ForkTsCheckerWebpackPlugin = require('fork-ts-checker-webpack-plugin')
const UglifyJsPlugin = require('uglifyjs-webpack-plugin')

const isDev = process.env.NODE_ENV === 'development'

require('tls').DEFAULT_ECDH_CURVE = 'auto'

let config = {
  mode: process.env.NODE_ENV,
  // watchOptions: {
  //   poll: true
  // },
  entry: {
    main: [ './assets/scss/main.scss', './src/main.ts' ]
  },
  output: {
    path: path.resolve(__dirname, 'dist'),
    publicPath: '/',
    filename: 'main.js'
  },
  resolve: {
    extensions: [ '.js', '.ts' ]
  },
  devServer: {
    noInfo: true,
    overlay: true,
    open: true,
    contentBase: path.resolve(__dirname, 'dist'),
    https: true,
    allowedHosts: [
      'colllect.localhost',
      'localhost'
    ],
    historyApiFallback: true,
    proxy: [{
      context: ['/api', '/proxy', '/oauth2'],
      target: 'https://127.0.0.1/app_dev.php',
      bypass: function (req) {
        req.headers.host = 'colllect.localhost'
      },
      secure: false
    }]
  },
  module: {
    rules: [
      {
        test: /\.ts$/,
        enforce: 'pre',
        use: 'tslint-loader'
      },
      {
        test: /\.ts$/,
        use: 'ts-loader'
      },
      {
        test: /\.scss/,
        use: ExtractTextPlugin.extract({
          fallback: { loader: 'style-loader', options: { sourceMap: isDev } },
          use: [
            { loader: 'css-loader', options: { sourceMap: isDev, importLoaders: 1 } },
            { loader: 'postcss-loader', options: { sourceMap: isDev } },
            { loader: 'sass-loader', options: { sourceMap: isDev, includePaths: [ path.resolve(__dirname, 'src') ] } }
          ]
        })
      },
      {
        test: /\.html$/,
        use: 'vue-template-loader'
      },
      {
        test: /\.(png|jpe?g|gif|woff2?|eot|ttf|otf|wav)(\?.*)?$/,
        use: 'url-loader'
      }
    ]
  },
  plugins: [
    new CopyWebpackPlugin(['./index.html']),
    new ExtractTextPlugin({ filename: 'main.css', disable: isDev }),
    new ForkTsCheckerWebpackPlugin(),
    new webpack.DefinePlugin({
      'process.env': {
        'NODE_ENV': JSON.stringify(process.env.NODE_ENV)
      }
    })
  ]
}

if (process.env.NODE_ENV === 'production') {
  config.plugins.push(new UglifyJsPlugin({
    uglifyOptions: {
      compress: {
        unused: false
      }
    }
  }))
} else {
  config.devtool = 'cheap-module-eval-source-map'
  config.plugins.push(new webpack.NamedModulesPlugin())
}

module.exports = config
