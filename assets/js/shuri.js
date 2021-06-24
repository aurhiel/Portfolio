// assets/js/shuri.js

// See file ./shuricon.font.js
// require('../../shuricon.font');

require('../css/shuri.scss');

var shuri = {
  // Variables
  //
  $body : null,

  projects : {},


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
  // Navbar close/open functions
  navbar__open : function() {
    this.$body.addClass('app-core--navbar-show');
    this.$navbar_toggler.attr('aria-expanded', 'true');
  },
  navbar__close : function() {
    this.$body.removeClass('app-core--navbar-show');
    this.$navbar_toggler.attr('aria-expanded', 'false');
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
      // get if must display elements on bottom of the website (who are not displayed due to 10%)
      var position_max = scroll.position + self.$window.height() + 5; // ugly fix for mobile
      var has_reached_end = (position_max >= Math.round(self.$body.height()));

      // console.log($elem.attr('class')+': '+position_max+' >= '+Math.round(self.$body.height()), has_reached_end);

      if( position_show_elem > $elem.offset().top || has_reached_end)
        $elem.removeClass(self.scroll_hide_class);
      else
        $elem.addClass(self.scroll_hide_class);
    });
  },

  /*
    Simple slider (testimonials)
    NOTE : TODO docs
  */
  simple_sliders: {
    class_main: 'simple-slider',
    class_items: 'simple-slider-item',
    class_current: '-current',
    get_current_position: function($slider) {
      return $slider.find('.simple-slider-item.' + this.class_current).index();
    },
    next: function($slider) {
      var new_pos = Math.min(($slider.find('.'+this.class_items).length - 1), (this.get_current_position($slider) + 1));
      this.goto($slider, new_pos);
    },
    previous: function($slider) {
      var new_pos = Math.max(0, (this.get_current_position($slider) - 1));
      this.goto($slider, new_pos);
    },
    goto : function($slider, position) {
      var $slides = $slider.find('.'+this.class_items);
      var $inner  = $slider.find('.simple-slider-inner');

      // Get slides % width & nb slides by page in order to change current & position after
      var slides_width_percent = Math.round(($slides.first().outerWidth() * 100 / $inner.width()) * 100) / 100;
      var nb_slides_by_page = Math.round(100 / slides_width_percent);

      // Update current & position
      $slides.removeClass(this.class_current).slice(position, (position + nb_slides_by_page)).addClass('-current');
      $slides.css('left', (position * -slides_width_percent) + '%');

      $slider.removeClass('-is-first -is-last')
        .addClass(position == 0 ? '-is-first' : '')
        .addClass(position >= ($slides.length - nb_slides_by_page) ? '-is-last': '');

      // Automatically update dots .-current class if slider has them
      if ($slider.hasClass('-has-dots')) {
        var $dots = $slider.find('.-dot');
        $dots.removeClass('-current').eq(position).addClass('-current');
      }
    },
    init: function($sliders) {
      var self = this;
      // EVENTS : Buttons
      $sliders.on('click', '.-btn', function() {
        var $button = $(this);
        var $slider = $button.parents('.'+self.class_main).first();

        if ($button.hasClass('-btn-next')) self.next($slider);
        else self.previous($slider);
      });

      // Dots
      $sliders.on('click', '.-dots .-dot', function(e) {
        var $dot = $(this);
        // Change slider position only if not clicked on current slide
        if ($dot.hasClass('-current') == false)
          self.goto($dot.parents('.'+self.class_main).first(), $dot.index());
      });

      // Resize: reset sliders (.goto) after 200ms
      var timeout_resize = null;
      $(window).on('resize', function() {
        clearTimeout(timeout_resize);
        timeout_resize = setTimeout(function() {
          $sliders.each(function() {
            self.goto($(this), 0);
          });
        }, 200);
      });
    }
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
        // raven.abort();
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
          self.$body.removeClass('app-core--display-form-contact app-core--hide-stage');

          // Force navbar close for mobile (< sm)
          self.navbar__close();
        } else {
          self.$contact_toast.addClass('toast-error');
        }
      }
    });
  },
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

  /*
    Contact form managnment
    NOTE : TODO docs
  */
  project_popup__display: function(project) {
    // console.log(project);
    var $screenshots  = this.$project_popup.find('.-project-screenshots');
    var $slider       = $screenshots.find('.simple-slider');

    // Update HTML
    this.$project_popup.find('.-project-title').html(project.name);
    this.$project_popup.find('.-project-desc').html(project.description);

    // Add link
    if (project.url) {
      var $link = this.$project_popup.find('.-project-link');
      $link.removeClass('d-none').find('.-link').attr('href', project.url);
    }

    // Add screenshots
    if (project.screenshots.length > 0) {
      var $slider_inner = $slider.find('.simple-slider-inner');
      var $slider_dots  = $slider.find('.simple-slider-nav .-dots');

      // Clear dots
      $slider_dots.html('');

      // Append screenshots to the slider
      project.screenshots.forEach((screen, i) => {
        // Add screenshot image
        $slider_inner.append($('<li class="simple-slider-item' + ((i == 0) ? ' -current' : '') + '">' +
            '<img class="img-fluid" src="' + this.project_screens_path + screen.filename + '">' +
          '</li>'
        ));
        //  & add dots
        $slider_dots.append($('<span class="-dot' + ((i == 0) ? ' -current' : '') + '"></span>'));
      });

      // Display $screenshots column
      $screenshots.removeClass('d-none');

      // Remove "-is-last" to enable slider if there is more than 1 screenshot
      if (project.screenshots.length > 1)
        $slider.removeClass('-is-last');
    }

    // Add specs
    if (project.specs.length > 0) {
      var specs_text = '';
      project.specs.forEach((spec, j) => {
        specs_text += spec.name + (((j + 1) < project.specs.length) ? ' / ' : '');
      });

      var $specs = this.$project_popup.find('.-project-specs');
      $specs.removeClass('d-none').find('.value').html(specs_text);
    }

    // Add date
    if (project.date != null) {
      var $date = this.$project_popup.find('.-project-date');

      // Display date field & update value
      $date.removeClass('d-none')
        .find('.value').html(project.date);
    }

    // Mobile ? (only for Kargain project)
    if (project.slug == 'kargain') {
      $slider.addClass('-is-mobile');

      // Add columns size for XXL media breakpoint
      this.$project_popup.find('.-project-textual-content').addClass('col-xl-7');
      $screenshots.addClass('col-xl-5');
    }

    // Display popup
    this.$body.addClass('app-core--display-project-popup');
  },
  project_popup__timeout_close: null,
  project_popup__close: function() {
    var self = this;
    // Hide popup
    this.$body.removeClass('app-core--display-project-popup');

    // Clear previous timeout & wait a few ms before clearing popup
    clearTimeout(this.project_popup__timeout_close);
    this.project_popup__timeout_close = setTimeout(function() {
      // Reset title & description HTML content
      self.$project_popup.find('.-project-title').html('');
      self.$project_popup.find('.-project-desc').html('');

      // Reset link
      self.$project_popup.find('.-project-link').addClass('d-none')
        .find('.-link').attr('href', '#');

      // Clear & reset HTML tags
      var $screenshots  = self.$project_popup.find('.-project-screenshots');
      var $slider       = $screenshots.find('.simple-slider');
      var $slider_inner = $slider.find('.simple-slider-inner');

      // Hide $screenshots column
      $screenshots.addClass('d-none');
      //   + reset slider classes & remove slider items
      $slider.removeClass('-reduced -expanded').addClass('-is-first -is-last');
      $slider_inner.html('');

      // Hide $specs & reset value
      var $specs = self.$project_popup.find('.-project-specs');
      $specs.addClass('d-none').find('.value').html('');

      // Hide $date field & reset value
      var $date = self.$project_popup.find('.-project-date');
      $date.addClass('d-none').find('.value').html('');

      // Disable mobile mode
      $slider.removeClass('-is-mobile');
      // Remove columns size for XXL media breakpoint
      self.$project_popup.find('.-project-textual-content').removeClass('col-xl-7');
      $screenshots.removeClass('col-xl-5');

      // Disable iframe
      $slider.find('.simple-slider-item--iframe').remove();
      $slider.removeClass('-iframed');
    }, 400);
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
    self.$header          = self.$body.find('.app-header');
    self.$header_navbar   = self.$header.find('.navbar');
    self.$navbar_toggler  = self.$header_navbar.find('.navbar-toggler');
    // // Set scroll elems node
    self.$scroll_elems  = self.$body.find('.scrolling-machine');
    // // Toaster (main toast container)
    self.$toaster       = self.$body.find('.app-toaster');
    // // Contact form
    self.$form_contact            = self.$body.find('.app-section--contact .form--contact');
    self.$contact_toast           = self.$toaster.find('.toast-form-contact');
    self.$contact_quote_checkbox  = self.$form_contact.find('#contact_is_quote');
    self.$contact_quote_inputs    = self.$form_contact.find('.contact-inputs-quote').find('select, input');
    // // Simple Sliders
    self.$simple_sliders = self.$body.find('.simple-slider');
    // // Projects
    self.$section_projects      = self.$body.find('.app-section--projects');
    self.$project_popup         = self.$body.find('.app-section--project-popup')
    self.$projects_links        = self.$section_projects.find('.btn-display-project');
    self.project_screens_path   = self.$section_projects.data('project-screens-path');



    //
    // Init functions
    //

    // Hide elements to animate
    self.scroll_anim__hide_elems();

    // Set loading
    self.loading();

    // Init toast
    self.$toaster.find('.toast').toast();

    // Simple slider
    self.simple_sliders.init(self.$simple_sliders);


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
      var anchor_id = $(this).attr('href').match(/#(.*$)/)[1];
      var $elem     = self.$body.find('#'+anchor_id);

      if($elem.length > 0) {
        var offset = self.$header_navbar.outerHeight() + parseInt($elem.css('marginTop'));
        self.$html_body.animate({
          scrollTop: $elem.offset().top - offset
        }, 300);

        // Close navbar for mobile menu
        self.navbar__close();
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
      self.$body.toggleClass('app-core--display-form-contact app-core--hide-stage');

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

    // // Click on project link > open popup
    self.$projects_links.on('click', function(e) {
      var $link           = $(this);
      var $project_item   = $link.parents('.app-project-item').first();
      var project_cached  = self.projects[$project_item.data('project-id')];

      // Get project data from "cache"
      if (typeof project_cached != 'undefined') {
        self.project_popup__display(project_cached);
      } else {
        // Get project data
        $.ajax({
          method: 'GET',
          url: $link.attr('href'),
          error   : function(jqXHR, status, error) {
            // Print problem
            if(error != 'abort')
              alert(error);
          },
          success : function(project) {
            if (project != null && typeof project.name != 'undefined') {
              self.project_popup__display(project);
              self.projects[project.id] = project;
            }
          }
        });
      }

      // Ultimate event STOP !!
      e.stopPropagation();
      e.preventDefault();
      return false;
    });
    // Closing project popup
    self.$project_popup.on('click', function(e) {
      if ($(e.target).hasClass('app-section--project-popup'))
        self.project_popup__close();
    });
    self.$project_popup.on('click', '.-popup-btn-close', function(e) {
      self.project_popup__close();

      // Ultimate event STOP !!
      e.stopPropagation();
      e.preventDefault();
      return false;
    });
    // Easter eggs clicks
    self.$project_popup.find('.simple-slider-fake-buttons').on('click', '.-fk-b', function() {
      var $fk_b         = $(this);
      var $screenshots  = self.$project_popup.find('.-project-screenshots');
      var $slider       = $screenshots.find('.simple-slider');

      if ($fk_b.hasClass('-fk-b--close')) {
        // When clicking on close button if there is an iframe display
        //  remove it & disable "iframe" mode on slider
        if ($slider.hasClass('-iframed')) {
          $slider.find('.simple-slider-item--iframe').remove();
          $slider.removeClass('-iframed');
        } else {
          $screenshots.addClass('d-none');
        }
      } else if ($fk_b.hasClass('-fk-b--reduce')) {
        $slider.removeClass('-expanded').toggleClass('-reduced');
      } else if ($fk_b.hasClass('-fk-b--expand')) {
        $slider.removeClass('-reduced').toggleClass('-expanded');
      }
    });
    var nb_clicks = 0;
    var timeout_project_link = null;
    self.$project_popup.on('click', '.-project-link .-link', function(e) {
      var $link = $(this);
      nb_clicks++;

      // Wait few times to trigger ifram easter egg
      //  or simply go to the project's link
      clearTimeout(timeout_project_link);
      timeout_project_link = setTimeout(function() {
        if (nb_clicks < 2) {
          window.open($link.attr('href'), $link.attr('target'));
        } else {
          var $slider       = self.$project_popup.find('.simple-slider');
          var $slider_inner = self.$project_popup.find('.simple-slider-inner');
          var $iframe       = $slider_inner.find('iframe');

          // Create iframe if not exist yet
          if ($iframe.length < 1) {
            $slider_inner.append($('<li class="simple-slider-item simple-slider-item--iframe -current">' +
                '<iframe src="">' +
            '</li>'));
            $iframe = $slider_inner.find('iframe');
          }

          // Update iframe url & activate "iframe" mode on slider
          $iframe.attr('src', $link.attr('href'));
          $slider.addClass('-iframed');
        }
        nb_clicks = 0;
      }, 160);

      // Ultimate event STOP !!
      e.stopPropagation();
      e.preventDefault();
      return false;
    });


    // // Toggle Menu display CSS class on body
    self.$navbar_toggler.on('click', function() {
      var $toggler = $(this);

      if( self.$body.hasClass('app-core--navbar-show') ) {
        self.navbar__close();
      } else {
        self.navbar__open();
      }
    });



    //
    // Doc ready
    //

    (function() {
      console.log('🌱 Radis ! ~');

      // Remove loading (not used yet...)
      self.unload();

      // Enable Bootstrap Tooltips
      self.$body.find('[data-toggle="tooltip"]').tooltip();

      // Trigger scroll event after ready to display elements already on screen
      self.$window.trigger('scroll');

      // Trigger goto() on projects slider to init -current classes
      self.simple_sliders.goto(self.$body.find('.simple-slider--projects'), 0);
    })();
  }
};

// Launching shuri
shuri.launch();
