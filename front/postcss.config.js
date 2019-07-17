module.exports = (context) => ({
  plugins: {
    'postcss-nested': {},
    autoprefixer: true,
    cssnano: context.env === 'production' ? {} : false
  }
})
