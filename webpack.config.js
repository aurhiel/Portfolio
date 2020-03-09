var Encore              = require('@symfony/webpack-encore');
var CopyWebpackPlugin   = require("copy-webpack-plugin");
var GoogleFontsPlugin   = require("@beyonk/google-fonts-webpack-plugin");
var FaviconsPlugin      = require("favicons-webpack-plugin");
var FaviconsTwigPlugin  = require("create-favicons-partial-webpack");


Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    // the public path you will use in Symfony's asset() function - e.g. asset('build/some_file.js')
    .setManifestKeyPrefix('build/')

    // Vendors
    .createSharedEntry('entries', './shared_entries.js') // new way to add vendor on Encore^0.24.0
    // .createSharedEntry('vendors', ['jquery', 'bootstrap']) // old way (Encore^0.20.0)

    // Main theme (Bootstrap + Shuri)
    .addEntry('shuri', './assets/js/shuri.js')

    // // Page : "Under works" page JS & CSS (with SASS)
    .addEntry('under-works', './assets/js/under-works.js')

    // // Page : Home
    .addStyleEntry('home', './assets/css/home.scss')

    // // Page : Golden book "Livre d'Or"
    .addStyleEntry('golden-book', './assets/css/golden-book.scss')

    // // Page : Curriculum vitae
    .addStyleEntry('curry-q', './assets/css/curry-q.scss')

    // Dashboard theme
    .addEntry('dashboard', './assets/js/dashboard.js')

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    // Cleaning things
    .cleanupOutputBeforeBuild()

    // HTML templates builder
    // .addPlugin(new HTMLPlugin({
    //   title: 'Yay !',
    //   inject: false,
    //   minify: false
    // }))

    // Google Fonts
    .addPlugin(new GoogleFontsPlugin({
      fonts: [
        // { family: "Roboto Mono", variants: [ "400", "700" ] },
        // { family: "PT Mono", variants: [ "400", "700" ] },
        { family: "Space Mono", variants: [ "400", "700" ] },
        { family: "Raleway", variants: [
          "300", "400", "500", "600", "700",
          // "400italic", "500italic", "600italic", "700italic"
        ] },
      ],
      "path": "fonts/google/",
      "filename": "google-fonts.css"
    }))

    // Favicons
    .addPlugin(new FaviconsPlugin({
      logo : './assets/images/favicon.png',
      // favicon background color
      background: '#2d2733',
      // favicon app title
      title: 'Litti Aurélien',
      // The prefix for all image files (might be a folder or a name)
      prefix: 'favicons/',
      // inject the html into the html-webpack-plugin
      inject: false,
      // which icons should be generated
      icons: {
        android: true,
        appleIcon: true,
        appleStartup: true,
        coast: false,
        favicons: true,
        firefox: true,
        opengraph: false,
        twitter: false,
        yandex: false,
        windows: false
      }
      /*
      // Emit all stats of the generated icons
      emitStats: false,
      // The name of the json containing all favicon information
      statsFilename: 'iconstats-[hash].json',
      // Generate a cache file with control hashes and
      // don't rebuild the favicons until those hashes change
      persistentCache: true,
      */
    }))
    // Build twig file for favicons HTML <link>
    .addPlugin(new FaviconsTwigPlugin({
      path: './templates/components/',
      fileName: 'head-favicons.html.twig',
      inputFilePath: './public/build/favicons/.cache',
    }))

    // Copy static files like fonts, images, ... (= if lazy or messy script/css)
    .addPlugin(new CopyWebpackPlugin([
      { from: './assets/static' }
    ]))

    // Source maps for production environment
    .enableSourceMaps(!Encore.isProduction())

    // the following line enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use Sass/SCSS files
    .enableSassLoader(function(sassOptions) {}, {
      resolveUrlLoader: false // without this option = VERY bad performance
    })

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()

    // Post CSS to autoprefix CSS properties for example
    .enablePostCssLoader()
;

// Get webpack config
const config = Encore.getWebpackConfig();

// Use polling instead of inotify
config.watchOptions = {
    poll: true,
};

module.exports = config;
