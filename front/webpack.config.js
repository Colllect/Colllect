const path = require('path')
const webpack = require('webpack')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
const ForkTsCheckerWebpackPlugin = require('fork-ts-checker-webpack-plugin')

const isDev = process.env.NODE_ENV === 'development'

let config = {
  entry: {
    main: [ './assets/scss/main.scss', './src/main.ts' ]
  },
  output: {
    path: path.resolve(__dirname, 'dist'),
    publicPath: '/dist',
    filename: 'main.js'
  },
  resolve: {
    extensions: [ '.js', '.ts' ]
  },
  devServer: {
    noInfo: true,
    overlay: true,
    historyApiFallback: true
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
            { loader: 'css-loader', options: { sourceMap: isDev } },
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
  config.plugins.push(new webpack.optimize.UglifyJsPlugin())
} else {
  config.devtool = 'cheap-module-eval-source-map'
  config.plugins.push(new webpack.NamedModulesPlugin())
}

module.exports = config
