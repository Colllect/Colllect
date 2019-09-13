module.exports = (context) => {
  const plugins = [
    require('postcss-easing-gradients'),
    require('autoprefixer'),
  ]

  if (context.env === 'production') {
    plugins.push(require('cssnano'))
  }

  return {
    plugins,
  }
}
