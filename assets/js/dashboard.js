// assets/js/dashboard.js

require('../css/dashboard.scss');

var dashboard = {
  // Params
  settings : {},
  // Methods
  toggle_testimonial : function(id, is_active) {
    console.log('toggle_testimonial', is_active, dashboard.settings.urls.toggle_testimonial.replace('__ID__', id));
    $.ajax({
      method: 'POST',
      url: dashboard.settings.urls.toggle_testimonial.replace('__ID__', id),
      data: {
        toggle: is_active
      }
    });
  },
  // Rocket launcher ! > code executed immediately (before document ready)
  launch : function(settings) {
    // $nodes & variables
    this.$body    = $('body');
    this.settings = settings;

    // when doc. is ready :
    $(function() {
      console.log('Radis ! ~', dashboard.$body);
      dashboard.$check_testimonial_toggle = dashboard.$body.find('.checkbox-toggle-testimonial');

      // EVENTS -------------------------
      dashboard.$check_testimonial_toggle.on('change', 'input[type="checkbox"]', function() {
        dashboard.toggle_testimonial(this.value, this.checked);
      });
    });
  }
};

// Launching dashboard
dashboard.launch(((typeof DASH_SETTINGS != 'undefined') ? DASH_SETTINGS : {}));
