var Encore = require('@symfony/webpack-encore');
var CopyWebpackPlugin = require("copy-webpack-plugin");
// var GoogleFontsPlugin = require("google-fonts-webpack-plugin");

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    // the public path you will use in Symfony's asset() function - e.g. asset('build/some_file.js')
    .setManifestKeyPrefix('build/')

    // will create public/build/main.js and public/build/main.css
    .addEntry('shuri', './assets/js/shuri.js')

    // Themes
    // .addStyleEntry('kakeibo-dark', './assets/css/kakeibo-dark.scss')

    // Vendors
    // .createSharedEntry('vendor', './webpack.shared_entry.js')

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    // Cleaning things
    .cleanupOutputBeforeBuild()

    // Copy static files like fonts, images, ...
    .addPlugin(new CopyWebpackPlugin([
      { from: './assets/static' }
    ]))

    .enableSourceMaps(!Encore.isProduction())

    // the following line enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
;

// Use polling instead of inotify
const config = Encore.getWebpackConfig();
config.watchOptions = {
    poll: true,
};

module.exports = config;
