var Encore = require('@symfony/webpack-encore');
var CopyWebpackPlugin = require("copy-webpack-plugin");
var GoogleFontsPlugin = require("google-fonts-webpack-plugin");
// var ExtractTextPlugin = require("extract-text-webpack-plugin");
/*
// Try to copy SVG icons with webfonts-generator, results : crappy/ugly icons ...
const webfontsGenerator = require('webfonts-generator');

webfontsGenerator({
  fontName: 'shuricon',
  templateOptions : {
    classPrefix: 'shuricon-',
    baseSelector: '.shuricon'
  },
  dest: './public/build/fonts/shuricon/',
  cssDest: './public/build/shuricon.css',
  cssFontsUrl: './fonts/shuricon/',
  files: [
    './assets/fonts/linearicons-svg/phone.svg',
    './assets/fonts/linearicons-svg/envelope.svg',
    './assets/fonts/linearicons-svg/map-marker.svg',
    './assets/fonts/linearicons-svg/construction.svg'
  ],
});
*/

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    // the public path you will use in Symfony's asset() function - e.g. asset('build/some_file.js')
    .setManifestKeyPrefix('build/')

    // will create public/build/main.js and public/build/main.css
    .addEntry('shuri', './assets/js/shuri.js')

    // "Under works" page JS & CSS (with SASS)
    .addEntry('under-works', './assets/js/under-works.js')

    //
    .addStyleEntry('home', './assets/css/home.scss')

    // Themes
    // .addStyleEntry('kakeibo-dark', './assets/css/kakeibo-dark.scss')

    // Vendors
    // .createSharedEntry('vendor', './entry.js')
    // Vendors
    .createSharedEntry('vendors', ['jquery', 'bootstrap'])

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    // .enableSingleRuntimeChunk()

    // Cleaning things
    .cleanupOutputBeforeBuild()

    // Google Fonts
    .addPlugin(new GoogleFontsPlugin({
        fonts: [
            // { family: "Quicksand", variants: [ "400", "500", "700" ] },
            { family: "Raleway", variants: [
              "300", "400", "500", "600", "700",
              // "400italic", "500italic", "600italic", "700italic"
            ] },
        ],
        "path": "fonts/google/",
        "filename": "google-fonts.css"
    }))

    // Copy static files like fonts, images, ...
    .addPlugin(new CopyWebpackPlugin([
      { from: './assets/static' }
    ]))

    // .addPlugin(new MiniCssExtractPlugin({
    //   // Options similar to the same options in webpackOptions.output
    //   // both options are optional
    //   filename: "[name].css",
    //   chunkFilename: "[id].css"
    // }))

    // Fonts icons loader
    /*.addLoader({
      test: /\.font\.js/,
      loader: ExtractTextPlugin.extract({
        // fallback: 'style-loader',
        use: [
          'css-loader',
          'webfonts-loader'
        ],
      }),
    })*/

    .enableSourceMaps(!Encore.isProduction())

    // the following line enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use Sass/SCSS files
    .enableSassLoader(function(sassOptions) {}, {
      // resolveUrlLoader: false
    })

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
;

// Get webpack config
const config = Encore.getWebpackConfig();

// Use polling instead of inotify
// config.watchOptions = {
//     poll: true,
// };

module.exports = config;
