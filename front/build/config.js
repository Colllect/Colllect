module.exports = {
  entry: {
    app: ['./src/assets/scss/app.scss', './src/app.js']
  },
  port: 3003,
  html: true,
  assets_url: '/',  // Urls dans le fichier final
  assets_path: './dist/', // ou build ?
  refresh: ['./index.html'] // Permet de forcer le rafraichissement du navigateur lors de la modification de ces fichiers
}
