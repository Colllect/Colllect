const path = require('path')
const webpack = require('webpack')
const ForkTsCheckerWebpackPlugin = require('fork-ts-checker-webpack-plugin')

let config = {
  entry: './src/main.ts',
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
        use: 'ts-loader'
      },
      {
        test: /\.html$/,
        use: 'vue-template-loader'
      }
    ]
  },
  plugins: [
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
