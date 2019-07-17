const path = require('path')
const webpack = require('webpack')
const CopyWebpackPlugin = require('copy-webpack-plugin')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const ForkTsCheckerWebpackPlugin = require('fork-ts-checker-webpack-plugin')
const TerserJSPlugin = require('terser-webpack-plugin')

const isDev = process.env.NODE_ENV === 'development'

let config = {
  mode: process.env.NODE_ENV,
  watchOptions: {
    // Makes watch works everywhere
    poll: true,
    // Avoid crazy CPU usage in some cases
    ignored: /node_modules/
  },
  entry: {
    main: ['./assets/scss/main.scss', './src/main.ts']
  },
  output: {
    path: path.resolve(__dirname, 'dist'),
    publicPath: '/',
    filename: 'main.js'
  },
  resolve: {
    extensions: ['.js', '.ts']
  },
  devServer: {
    noInfo: true,
    overlay: true,
    contentBase: path.resolve(__dirname, 'dist'),
    http2: true,
    allowedHosts: [
      'colllect.localhost',
      'localhost'
    ],
    host: '0.0.0.0',
    historyApiFallback: true,
    proxy: [{
      context: ['/api', '/proxy', '/oauth2', '/login', '/logout', '/register', '/_profiler', '/_wdt'],
      target: 'https://127.0.0.1/',
      bypass: function (req) {
        req.headers.host = 'colllect.localhost'
      },
      secure: false
    }]
  },
  plugins: [
    new CopyWebpackPlugin(['./index.html']),
    new MiniCssExtractPlugin({ filename: 'main.css' }),
    new ForkTsCheckerWebpackPlugin(),
    new webpack.DefinePlugin({
      'process.env': {
        'NODE_ENV': JSON.stringify(process.env.NODE_ENV)
      }
    })
  ],
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
        use: [
          isDev
            ? { loader: 'style-loader', options: { sourceMap: isDev } }
            : { loader: MiniCssExtractPlugin.loader },
          { loader: 'css-loader', options: { sourceMap: isDev, importLoaders: 1 } },
          { loader: 'postcss-loader', options: { sourceMap: isDev } },
          { loader: 'sass-loader', options: { sourceMap: isDev, includePaths: [path.resolve(__dirname, 'src')] } }
        ]
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
  optimization: {}
}

if (process.env.NODE_ENV === 'production') {
  config.optimization.push(new TerserJSPlugin({
    uglifyOptions: {
      compress: {
        unused: false
      }
    }
  }))
} else {
  config.devtool = 'cheap-module-eval-source-map'
}

module.exports = config
