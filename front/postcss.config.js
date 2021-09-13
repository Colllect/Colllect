module.exports = () => {
  const plugins = [
    require('postcss-easing-gradients'),
    require('autoprefixer'),
  ]

  return {
    plugins,
  }
}
