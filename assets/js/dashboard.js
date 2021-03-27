// assets/js/dashboard.js

require('../css/dashboard.scss');

// Add ChartJS
var ChartJS = require('chart.js');

var dashboard = {
  // Params
  settings : {},
  // Viewport sizes
  viewport: {
    $sizes: null,
    last_call_size: null,
    init: function() {
      this.$sizes = dashboard.$body.find('.viewport-sizes > *');
      this.last_call_size = this.$sizes.filter(':visible').data('viewport-size-slug');
    },
    current: function() {
      return this.$sizes.filter(':visible').data('viewport-size-slug');
    },
    has_change: function() {
      var new_size = this.$sizes.filter(':visible').data('viewport-size-slug');
      var has_change = new_size != this.last_call_size;

      this.last_call_size = new_size;

      return has_change;
    }
  },
  // ChartJS
  chartJS: {
    $items: null,
    is_legend_visible: function(hide_sizes) {
      var show = true;
      for (var i = 0; i < hide_sizes.length; i++) {
        if (dashboard.viewport.current() == hide_sizes[i]) {
          show = false;
          break;
        }
      }
      return show;
    },
    init: function() {
      var self    = this;
      this.$items = dashboard.$body.find('.chart-js');

      this.$items.each(function() {
        var $chart = $(this),
            canvas = $chart.find('canvas')
            data_name   = $chart.data('chartjs-data-name'),
            data_type   = $chart.data('chartjs-data-type'),
            chart_type  = $chart.data('chartjs-type'),
            chart_min   = $chart.data('chartjs-min'),
            chart_max   = $chart.data('chartjs-max'),
            grid_color  = $chart.data('chartjs-grid-color'),
            legend_display    = (typeof $chart.data('chartjs-legend-display') != 'undefined') ? $chart.data('chartjs-legend-display') : true
            legend_position   = $chart.data('chartjs-legend-position')
            legend_hide_sizes = (typeof $chart.data('chartjs-legend-hide') == 'string') ? $chart.data('chartjs-legend-hide').split('|') : [];

        if (typeof data_name != 'undefined' && typeof window[data_name] != 'undefined') {
          var opts = {};
          var data = window[data_name];

          if (typeof chart_min != 'undefined' && typeof chart_max != 'undefined') {
            opts.scale = {
              ticks : {
                min : chart_min,
                max : chart_max
              }
            };
          }

          // Chart legend
          if (legend_display)
            legend_display = self.is_legend_visible(legend_hide_sizes);

          opts.legend = {
            display: legend_display
          };

          if (typeof grid_color != 'undefined') {
            if (grid_color == 'white' || grid_color == 'light') {
              opts.scales = {
                xAxes: [{
                  gridLines: {
                    zeroLineColor: 'rgba(255, 255, 255, .5)',
                    color: 'rgba(255, 255, 255, .1)',
                  },
                  ticks: {
                    fontColor: 'rgba(255, 255, 255, .75)'
                  }
                }],
                yAxes: [{
                  gridLines: {
                    zeroLineColor: 'rgba(255, 255, 255, .5)',
                    color: 'rgba(255, 255, 255, .1)',
                  },
                  ticks: {
                    fontColor: 'rgba(255, 255, 255, .75)'
                  }
                }]
              };
            }
          }

          if (typeof legend_position != 'undefined')
            opts.legend.position = legend_position;

          // Custom tooltips
          if (typeof data_type != 'undefined') {
            opts.tooltips = {
              callbacks: {
                label: function(tooltipItem, data) {
                  // Add % character to percent data type values
                  if (data_type == 'percent') {
                    var label = tooltipItem.value + '%';
                    // Push counting things for percent values
                    var datasets = data.datasets[tooltipItem.datasetIndex];
                    if (typeof datasets != 'undefined' && typeof datasets.data_count != 'undefined') {
                      label += ' (' + datasets.data_count[tooltipItem.index] + ' ' + datasets.label + ((datasets.data_count[tooltipItem.index] > 1) ? 's': '') + ')';
                    }

                    return label;
                  }
                }
              }
            };
          }

          var chartJS = new Chart(canvas, {
            type : chart_type,
            data : data,
            options : opts,
            plugins: [{
              resize: function() {
                $chart.data('chartJS').options.legend.display = self.is_legend_visible(legend_hide_sizes);
                $chart.data('chartJS').update();
              }
            }]
          });

          $chart.data('chartJS', chartJS);
        }
      });

      // Fix for printing (NOTE: but doesn't work...)
      function beforePrintHandler () {
        for (var id in Chart.instances) {
          Chart.instances[id].resize();
        }
      }
      window.addEventListener("beforeprint", beforePrintHandler);
    }
  },
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
      // Viewport
      dashboard.viewport.init();

      // Charts
      dashboard.chartJS.init();

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

      // Form filters quotes & invoices
      dashboard.$body.on('submit', '.form-filter-quotes, .form-filter-invoices', function(e) {
        var $form = $(this);
        var year = parseInt($form.find('#filter-year').val());
        var base_url = '/dashboard/compta/' + ($form.hasClass('form-filter-quotes') ? 'devis' : 'factures');

        if (year > 0) window.location.href = base_url + '/' + year;
        else window.location.href = base_url;

        // Yay ! ~
        e.preventDefault();
        e.stopPropagation();
        return false;
      });

      // Logs.
      // console.log('Radis ! ~', dashboard.viewport.current());
    });
  }
};

// Launching dashboard
dashboard.launch(((typeof DASH_SETTINGS != 'undefined') ? DASH_SETTINGS : {}));
