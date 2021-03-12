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
      // console.log('Radis ! ~', dashboard.$body);

      // EVENTS -------------------------
      dashboard.$check_testimonial_toggle = dashboard.$body.find('.checkbox-toggle-testimonial');
      dashboard.$check_testimonial_toggle.on('change', 'input[type="checkbox"]', function() {
        dashboard.toggle_testimonial(this.value, this.checked);
      });

      dashboard.$body.find('.custom-file-input').on('change', function(e) {
        $(this).parents('.custom-file').first().find('.custom-file-label').html(this.value.replace(/^.*?([^\\\/]*)$/, '$1'));
      });

      // Auto-fill invoices & quotes dates & reference based on file name
      dashboard.$body.find('.form-invoice, .form-quote').on('change', '.custom-file-input', function() {
        var filename  = this.value.replace(/^.*?([^\\\/]*)$/, '$1');
        var $form     = $(this).parents('.form');
        var matches   = filename.match(/^([D|F|FA]+)([0-9]+)/);
        var is_quote_form   = $form.hasClass('form-quote');
        var is_invoice_form = $form.hasClass('form-invoice');

        if (!!matches && typeof matches == 'object') {
          var ref = matches[0];
          var date = matches[2];
          var year  = '20' + date[0] + date[1];
          var month = date[2] + date[3];
          var day   = date[4] + date[5];

          if (is_quote_form) {
            $form.find('#quote_sku').val(ref);
            $form.find('#quote_date_created, #quote_date_signed').val(year + '-' + month + '-' + day);
          } else if (is_invoice_form) {
            $form.find('#invoice_sku').val(ref);
            $form.find('#invoice_date_paid').val(year + '-' + month + '-' + day);
          }
        }
      });
    });
  }
};

// Launching dashboard
dashboard.launch(((typeof DASH_SETTINGS != 'undefined') ? DASH_SETTINGS : {}));
