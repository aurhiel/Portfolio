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
    var direction       = scroll.direction();
    var navbar_height   = this.$header_navbar.outerHeight();
    var top_when_fixed  = navbar_height + offset;

    if ( scroll.position > navbar_height )
      this.$body.addClass(this.oos_navbar_class);

    if ( direction == scroll.SCROLL_DOWN && (scroll.position > top_when_fixed) )
      this.$body.addClass(this.fixed_navbar_class);
    else if ( direction == scroll.SCROLL_UP && scroll.position <= 0 )
      this.$body.removeClass(this.fixed_navbar_class + ' ' + this.oos_navbar_class);
  },

  /*
    Scroll animation feature
    NOTE : TODO docs
  */
  $scroll_elems : {},
  scroll_hide_class : 'sm--hide',
  scroll_anim__hide_elems : function () {
    this.$scroll_elems.addClass(this.scroll_hide_class);
  },
  scroll_anim__display_elems : function(scroll) {
    var self = this;

    self.$scroll_elems.each(function() {
      var $elem = $(this);
      var position_show_elem = scroll.position + (self.$window.height() * .9); // 0.9 = 10% of window height

      if( position_show_elem > $elem.offset().top )
        $elem.removeClass(self.scroll_hide_class);
      else
        $elem.addClass(self.scroll_hide_class);
    });
  },


  /*
    Contact form managnment
    NOTE : TODO docs
  */
  form_contact__toggle_is_quote : function (is_quote) {
    if (is_quote) {
      this.$form_contact.addClass('is-quote');
      this.$contact_quote_inputs.attr('required', 'required');
    } else {
      this.$form_contact.removeClass('is-quote');
      this.$contact_quote_inputs.removeAttr('required');
    }
  },
  form_contact__submit : function () {
    var self = this;

    // Add CSS class to form in order to disable any manipulation (ugly for security but simple solution)
    self.$form_contact.addClass('disabled');

    // Submit form with ajax
    $.ajax({
      method  : 'POST',
      url     : this.$form_contact.attr('action'),
      data    : this.$form_contact.serialize(),
      error   : function(jqXHR, status, error) {
        // Abort raven (remove body class, ...)
        raven.abort();
        // Print problem if not an abort()
        if(error != 'abort')
          alert(error);
      },
      success : function(response) {
        // Filling Toast with Message
        self.$contact_toast.find('.toast-body').html(response.message);
        self.$contact_toast.removeClass('toast-success toast-error').toast('show');

        if (response.status == 1) {
          self.$contact_toast.addClass('toast-success');

          // Reset form
          self.form_contact__reset();

          // Hide form
          self.$body.removeClass('app-core--display-form-contact');
        } else {
          self.$contact_toast.addClass('toast-error');
        }
      }
    });
  },
  // Not used, to delete ?
  form_contact__reset : function () {
    // Re-activate form
    this.$form_contact.removeClass('disabled');

    // Reset classic inputs
    this.$form_contact.find('input, textarea').not('[type="hidden"]').val('');

    // Reset <select>
    this.$form_contact.find('select').each(function() {
      var $select     = $(this);
      var $options    = $select.find('option');
      var select_val  = $options.first().val();

      if($options.filter('[selected]').length > 0)
        select_val = $options.filter('[selected]').val();

      $select.val(select_val);
    });
  },


  // Rocket launcher ! > code executed immediately (before document ready)
  //
  launch : function() {
    //
    // Variables (private & public)
    //

    // Le viss
    var self = this;
    // Scroll object
    var scroll = {
      SCROLL_DOWN : 1,
      SCROLL_UP   : -1,
      previousPos : null,
      position    : 0,
      direction   : function() { return (this.position > this.previousPos) ? this.SCROLL_DOWN : this.SCROLL_UP; }
    };

    // Set nodes
    self.$body      = $('.app-core');
    self.$html_body = $('html, body')
    self.$window    = $(window);
    // // Set header nodes
    self.$header        = self.$body.find('.app-header');
    self.$header_navbar = self.$header.find('.navbar');
    // // Set scroll elems node
    self.$scroll_elems  = self.$body.find('.scrolling-machine');
    // // Toaster (main toast container)
    self.$toaster       = self.$body.find('.app-toaster');
    // // Contact form
    self.$form_contact            = self.$body.find('.app-section--contact .form--contact');
    self.$contact_toast           = self.$toaster.find('.toast-form-contact');
    self.$contact_quote_checkbox  = self.$form_contact.find('#contact_is_quote');
    self.$contact_quote_inputs    = self.$form_contact.find('.contact-inputs-quote').find('select, input');



    //
    // Init functions
    //

    // Hide elements to animate
    self.scroll_anim__hide_elems();

    // Set loading
    self.loading();

    // Init toast
    self.$toaster.find('.toast').toast();



    //
    // Events
    //

    // // Scrolling event
    self.$window.on('scroll', function(e) {
      // Set scroll position
      scroll.position = self.$window.scrollTop();

      // On home page change scroll offset to display menu AFTER the main jumbotron
      // var offset = self.$header_navbar.outerHeight(); // Minimal offset of 5px in order to see CSS animation
      // if( self.$body.hasClass('app-core--home') )
      //   offset = self.$body.find('.jumbotron').first().outerHeight() - self.$header_navbar.outerHeight();
      var offset = 100; // used to see navbar animation (100px approx. one scroll mouse)

      // Fix navbar ?
      self.toggle_fixed_navbar(scroll, offset);

      // Display elements ?
      self.scroll_anim__display_elems(scroll);

      // Set previous scroll position
      scroll.previousPos = scroll.position;
    });

    // // Scroll to event
    self.$body.on('click', '.scroll-to', function(e) {
      // get element ID where to scroll from link #href
      var anchor_id = $(this).attr('href');
      var $elem     = self.$body.find(anchor_id);

      if($elem.length > 0) {
        var offset = self.$header_navbar.outerHeight() + parseInt($elem.css('marginTop'));
        self.$html_body.animate({
          scrollTop: $elem.offset().top - offset
        }, 300);
      }

      // disable anchor in url
      e.preventDefault();
      return false;
    });

    // // Toggle popin with form contact event
    self.$body.on('click', '.btn-toggle-form-contact', function(e) {
      var $button = $(this);

      // Auto check is-quote & display inputs
      var is_quote = (typeof $button.data('check-is-quote') != 'undefined') ? $button.data('check-is-quote') : false;
      self.$contact_quote_checkbox.prop('checked', is_quote);
      self.form_contact__toggle_is_quote(is_quote);

      // Display form
      self.$body.toggleClass('app-core--display-form-contact');

      // disable click
      e.preventDefault();
      return false;
    });

    // // Contact form > Quote checkbox change event
    self.$contact_quote_checkbox.on('change', function() {
      self.form_contact__toggle_is_quote($(this).is(':checked'));
    });
    // // // Toggle quote inputs on first load, if needed
    self.form_contact__toggle_is_quote(self.$contact_quote_checkbox.is(':checked'));
    // // // Form submit interception
    self.$form_contact.on('submit', function(e) {
      // Launch submit function
      self.form_contact__submit();

      // disable form submit
      e.preventDefault();
      return false;
    });



    //
    // Doc ready
    //

    (function() {
      console.log('🌱 Radis ! ~');

      // Remove loading (not used yet...)
      self.unload();

      // Trigger scroll event after ready to display elements already on screen
      self.$window.trigger('scroll');
    })();
  }
};

// Launching shuri
shuri.launch();
