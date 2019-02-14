// assets/js/app.js

require('../css/shuri.scss');

var shuri = {
  // Variables
  $body : null,
  // Functions
  loading : function() {
    this.$body.addClass('is-loading');
  },
  unload : function() {
    this.$body.removeClass('is-loading');
  },
  // Rocket launcher !
  launch : function() {
    // set nodes
    this.$body = $('body');

    this.loading();

    //
    // Doc ready
    //

    (function() {
      console.log('Radis ! ~');
      shuri.unload();
    })();
  }
};

// Launching shuri
shuri.launch();
