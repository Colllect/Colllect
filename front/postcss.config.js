const autoprefixerConfig = {
  browsers: [ 'last 2 versions', 'ie >= 9' ]
}

module.exports = (context) => ({
  plugins: {
    'postcss-nested': {},
    'autoprefixer': context.env === 'production' ? autoprefixerConfig : false,
    cssnano: context.env === 'production' ? {} : false
  }
})
