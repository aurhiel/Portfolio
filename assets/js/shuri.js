// assets/js/shuri.js

// See file ./shuricon.font.js
// require('../../shuricon.font');

require('../css/shuri.scss');

var shuri = {
  // Variables
  //
  $body : null,


  // Functions
  //
  // Page loading
  loading : function() {
    this.$body.addClass('is-loading');
  },
  unload : function() {
    this.$body.removeClass('is-loading');
  },

  /*
    Fixed nav bar feature
      NOTE :
      When scrolling down (direction = DOWN and scroll position is greater than the navbar height)
        > Set navbar to fixed position with CSS class ".app-core--navbar-fixed"
      When scrolling up (direction = UP and scroll position is smaller than the navbar scroll top position)
        > Set navbar to previous CSS position by removing CSS class ".app-core--navbar-fixed"
  */
  fixed_navbar_class  : 'app-core--navbar-fixed',
  oos_navbar_class    : 'app-core--navbar-oos', // oos : out of screen
  toggle_fixed_navbar : function(scroll, offset) {
    offset = (typeof offset == 'undefined') ? 0 : offset;
    var direction = scroll.direction();
    var navbar_height   = this.$header_navbar.outerHeight();
    var top_when_fixed  = navbar_height + offset;

    if ( scroll.position > navbar_height )
      this.$body.addClass(this.oos_navbar_class);

    if ( direction == scroll.SCROLL_DOWN && (scroll.position > top_when_fixed) )
      this.$body.addClass(this.fixed_navbar_class);
    else if ( direction == scroll.SCROLL_UP && (scroll.position) <= this.$header_navbar.scrollTop() )
      this.$body.removeClass(this.fixed_navbar_class + ' ' + this.oos_navbar_class);
  },


  // Rocket launcher !
  //
  launch : function() {
    var self = this;

    // Set nodes
    self.$body = $('.app-core');
    self.$window = $(window);
    // // Set header nodes
    self.$header        = self.$body.find('.app-header');
    self.$header_navbar = self.$header.find('.navbar');

    // Set loading
    self.loading();


    //
    // Events
    //

    // Scroll object
    var scroll = {
      SCROLL_DOWN  : 1,
      SCROLL_UP    : -1,
      previousPos : null,
      position    : 0,
      direction   : function() { return (this.position > this.previousPos) ? this.SCROLL_DOWN : this.SCROLL_UP; }
    };
    // // Scrolling event
    self.$window.on('scroll', function(e) {
      // Set scroll position
      scroll.position   = self.$window.scrollTop();

      // On home page change scroll offset to display menu AFTER the main jumbotron
      var offset = self.$header_navbar.outerHeight(); // Minimal offset of 5px in order to see CSS animation
      if( self.$body.hasClass('app-core--home') )
        offset = self.$body.find('.jumbotron').first().outerHeight() - self.$header_navbar.outerHeight();

      // Fix navbar ?
      self.toggle_fixed_navbar(scroll, offset);

      // Set previous scroll position
      scroll.previousPos = scroll.position;
    }).trigger('scroll');



    //
    // Doc ready
    //

    (function() {
      console.log('Radis ! ~');

      // Remove loading
      self.unload();
    })();
  }
};

// Launching shuri
shuri.launch();
