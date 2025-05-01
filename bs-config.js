// bs-config.js
module.exports = {
    proxy: "localhost:8000",                  // PHP écoute ici
    files: [
      "public_html/**/*.{php,html,js,css}"
    ],                                        // fichiers à surveiller
    port: 3000,                               // BrowserSync sert sur ce port
    open: true,                               // ouvre automatiquement le navigateur
    notify: false                             // pas de petite notification en bas à droite
  };
  