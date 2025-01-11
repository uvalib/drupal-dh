/**
 * @file
 * Integrates d3 library and functionality with D7 core javascript
 */

(function($, Drupal) {

  /**
   * Global object for holding d3 internal and visualization style methods.
   */
  Drupal.d3 = {
    draw: function(element, settings) {
      // Invoke JavaScript library function, if it exists.
      var name = settings.type.toLowerCase();
      if (Drupal.d3[name]) {
        Drupal.d3[name](element, settings);
      }
    }
  };

  /**
   * Drupal behaviors for D3 module.
   */
  Drupal.behaviors.d3 = {
    attach: function(context, settings) {
      var id, $context = $(context);
      // Check to see if there are visualizations that have been set.
      if (settings.d3 && settings.d3.inventory) {
        // Iterate over each of the visualizations set in inventory.
        for (id in settings.d3.inventory) {
          // Only process the visualization once, if it exists.
          $context.find('#' + id).once('d3', function () {
            Drupal.d3.draw(id, settings.d3.inventory[id]);
          });
        }
      }
    }
  };

})(jQuery, Drupal);
;
/**
 * @file
 * D3 Force Directed library
 */

(function($) {
  Drupal.d3.forcedirected = function (select, settings) {

    var width  = (settings.config.width || 300),
        height = (settings.config.height || 300),
        nodes  = settings.nodes,
        links  = settings.links,
        z      = d3.scale.ordinal().range(["blue", "red", "orange", "green"]),
        k      = Math.sqrt(nodes.length / (width * height)),
        color  = d3.scale.category20();

    var force = d3.layout.force()
      .size([width, height]);

    // additional settings for the advanced force directed graphs
    if (settings.gravity) {
      force.gravity(settings.gravity)
    }
    if (settings.friction) {
      force.friction(settings.friction)
    }
    if (settings.theta) {
      force.theta(settings.theta)
    }
    if (settings.charge) {
      force.charge(settings.charge)
    }
    if (settings.linkDistance) {
      force.linkDistance(settings.linkDistance)
    }

    force.charge(-100);
    force.distance(100);
    force.gravity(.05);
    var svg = d3.select('#' + settings.id).append("svg")
        .attr("width", width)
        .attr("height", height);

    // Add definitions
    var defs = svg.append("defs");

    defs.selectAll("marker")
        .data([
          { id: 'arrow', path : "M-5,-5L5,0L-5,5"},
          { id: 'circle', path: 'M 0, 0  m -5, 0  a 5,5 0 1,0 10,0  a 5,5 0 1,0 -10,0'}
        ])
      .enter().append("marker")
        .attr("id", function (d) {
            return d.id;
        })
        .attr("viewBox", "-5 -5 10 10")
        .attr("refX", 10)
        .attr("refY", 0)
        .attr("markerWidth", 10)
        .attr("markerHeight", 10)
        .attr("orient", "auto")
        .append("path")
        .attr("d", function(d){return d.path;})
        .attr('class', function(d){return 'marker marker-' + d.id})
        .style('fill', '#000')
    ;

    var graph = svg.append("g")
        .attr("class", "data");

    force
        .nodes(nodes)
        .links(links)
        .start();

    var link = graph.selectAll("line.link")
        .data(links)
      .enter().append("line")
        .attr("marker-end", function (d) {
            return "url(#arrow)";
        })
        .attr("class", "link")
        .style("stroke", function(d) {  return d3.hsl(d.data.fill); })
        .style("stroke-width", 1);

    var node = graph.selectAll("g.node")
        .data(nodes)
      .enter().append("svg:g")
        .attr("class", "node")
        .call(force.drag);

    node.append("svg:circle")
      .attr("class", "node")
      .attr("r", function(d) { return (d.data.d) ? d.data.d : 5; })
      .style("fill", d3.hsl('white'))
      .style("stroke", function(d) { return d3.hsl(d.data.fill); })
      .style("stroke-width", 3);

    // filter on items that have and don't have an d.data.uri
    var hasUri = function(d) { return d.data && d.data.uri && d.data.uri.length > 0;};
    var noUri = function(d) { return !hasUri(d)};
    node.filter(hasUri)
      .append("svg:a")
        .attr("xlink:href", function(d) {
          return d.data.uri;
        })
      .append("svg:text")
        .attr("class", "nodetext")
        .attr("dx", 10)
        .attr("dy", ".35em")
        .attr('font-size', '10')
        .text(function(d) { return d.name });

    node.filter(noUri)
      .append("svg:text")
        .attr("class", "nodetext")
        .attr("dx", 10)
        .attr("dy", ".35em")
        .attr('font-size', '10')
        .text(function(d) { return d.name });

    force.on("tick", function() {
      link.attr("x1", function(d) { return d.source.x; })
          .attr("y1", function(d) { return d.source.y; })
          .attr("x2", function(d) { return d.target.x; })
          .attr("y2", function(d) { return d.target.y; });

      node.attr("cx", function(d) { return d.x; })
          .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; })
          .attr("cy", function(d) { return d.y; });
    });

  }

})(jQuery);
;
/**
 * @file better_exposed_filters.js
 *
 * Provides some client-side functionality for the Better Exposed Filters module
 */
(function ($) {
  Drupal.behaviors.betterExposedFilters = {
    attach: function(context) {
      // Add highlight class to checked checkboxes for better theming
      $('.bef-tree input[type=checkbox], .bef-checkboxes input[type=checkbox]')
        // Highlight newly selected checkboxes
        .change(function() {
          _bef_highlight(this, context);
        })
        .filter(':checked').closest('.form-item', context).addClass('highlight')
      ;
    }
  };

  Drupal.behaviors.betterExposedFiltersSelectAllNone = {
    attach: function(context) {

      /*
       * Add Select all/none links to specified checkboxes
       */
      var selected = $('.form-checkboxes.bef-select-all-none:not(.bef-processed)');
      if (selected.length) {
        var selAll = Drupal.t('Select All');
        var selNone = Drupal.t('Select None');

        // Set up a prototype link and event handlers
        var link = $('<a class="bef-toggle" href="#">'+ selAll +'</a>')
        link.click(function(event) {
          // Don't actually follow the link...
          event.preventDefault();
          event.stopPropagation();

          if (selAll == $(this).text()) {
            // Select all the checkboxes
            $(this)
              .html(selNone)
              .siblings('.bef-checkboxes, .bef-tree')
                .find('.form-item input:checkbox').each(function() {
                  $(this).attr('checked', true);
                  _bef_highlight(this, context);
                })
              .end()

              // attr() doesn't trigger a change event, so we do it ourselves. But just on
              // one checkbox otherwise we have many spinning cursors
              .find('input[type=checkbox]:first').change()
            ;
          }
          else {
            // Unselect all the checkboxes
            $(this)
              .html(selAll)
              .siblings('.bef-checkboxes, .bef-tree')
                .find('.form-item input:checkbox').each(function() {
                  $(this).attr('checked', false);
                  _bef_highlight(this, context);
                })
              .end()

              // attr() doesn't trigger a change event, so we do it ourselves. But just on
              // one checkbox otherwise we have many spinning cursors
              .find('input[type=checkbox]:first').change()
            ;
          }
        });

        // Add link to the page for each set of checkboxes.
        selected
          .addClass('bef-processed')
          .each(function(index) {
            // Clone the link prototype and insert into the DOM
            var newLink = link.clone(true);

            newLink.insertBefore($('.bef-checkboxes, .bef-tree', this));

            // If all checkboxes are already checked by default then switch to Select None
            if ($('input:checkbox:checked', this).length == $('input:checkbox', this).length) {
              newLink.click();
            }
          })
        ;
      }

      // Check for and initialize datepickers
      var befSettings = Drupal.settings.better_exposed_filters;
      if (befSettings && befSettings.datepicker && befSettings.datepicker_options && $.fn.datepicker) {
        var opt = [];
        $.each(befSettings.datepicker_options, function(key, val) {
          if (key && val) {
            opt[key] = JSON.parse(val);
          }
        });
        $('.bef-datepicker').datepicker(opt);
      }

    }                   // attach: function() {
  };                    // Drupal.behaviors.better_exposed_filters = {

  Drupal.behaviors.betterExposedFiltersAllNoneNested = {
    attach:function (context, settings) {
      $('.form-checkboxes.bef-select-all-none-nested li').has('ul').once('bef-all-none-nested', function () {
        $(this)
          // To respect term depth, check/uncheck child term checkboxes.
          .find('input.form-checkboxes:first')
          .click(function() {
            var checkedParent = $(this).attr('checked');
            if (!checkedParent) {
              // Uncheck all children if parent is unchecked.
              $(this).parents('li:first').find('ul input.form-checkboxes').removeAttr('checked');
            }
            else {
              // Check all children if parent is checked.
              $(this).parents('li:first').find('ul input.form-checkboxes').attr('checked', $(this).attr('checked'));
            }
          })
          .end()
          // When a child term is checked or unchecked, set the parent term's
          // status.
          .find('ul input.form-checkboxes')
          .click(function() {
            var checked = $(this).attr('checked');

            // Determine the number of unchecked sibling checkboxes.
            var ct = $(this).parents('ul:first').find('input.form-checkboxes:not(:checked)').size();

            // If the child term is unchecked, uncheck the parent.
            if (!checked) {
              // Uncheck parent if any of the childres is unchecked.
              $(this).parents('li:first').parents('li:first').find('input.form-checkboxes:first').removeAttr('checked');
            }

            // If all sibling terms are checked, check the parent.
            if (!ct) {
              // Check the parent if all the children are checked.
              $(this).parents('li:first').parents('li:first').find('input.form-checkboxes:first').attr('checked', checked);
            }
          });
      });
    }
  };

  Drupal.behaviors.better_exposed_filters_slider = {
    attach: function(context, settings) {
      var befSettings = settings.better_exposed_filters;
      if (befSettings && befSettings.slider && befSettings.slider_options) {
        $.each(befSettings.slider_options, function(i, sliderOptions) {
          var containing_parent = "#" + sliderOptions.viewId + " #edit-" + sliderOptions.id + "-wrapper .views-widget";
          var $filter = $(containing_parent);

          // If the filter is placed in a secondary fieldset, we may not have
          // the usual wrapper element.
          if (!$filter.length) {
            containing_parent = "#" + sliderOptions.viewId + " .bef-slider-wrapper";
            $filter = $(containing_parent);
          }

          // Only make one slider per filter.
          $filter.once('slider-filter', function() {
            var $input = $(this).find('input[type=text]');

            // This is a "between" or "not between" filter with two values.
            if ($input.length == 2) {
              var $min = $input.parent().find('input#edit-' + sliderOptions.id + '-min'),
                  $max = $input.parent().find('input#edit-' + sliderOptions.id + '-max'),
                  default_min,
                  default_max;

              if (!$min.length || !$max.length) {
                return;
              }

              // Get the default values.
              // We use slider min & max if there are no defaults.
              default_min = parseFloat(($min.val() == '') ? sliderOptions.min : $min.val(), 10);
              default_max = parseFloat(($max.val() == '') ? sliderOptions.max : $max.val(), 10);
              // Set the element value in case we are using the slider min & max.
              $min.val(default_min);
              $max.val(default_max);

              $min.parents(containing_parent).after(
                $('<div class="bef-slider"></div>').slider({
                  range: true,
                  min: parseFloat(sliderOptions.min, 10),
                  max: parseFloat(sliderOptions.max, 10),
                  step: parseFloat(sliderOptions.step, 10),
                  animate: sliderOptions.animate ? sliderOptions.animate : false,
                  orientation: sliderOptions.orientation,
                  values: [default_min, default_max],
                  // Update the textfields as the sliders are moved
                  slide: function (event, ui) {
                    $min.val(ui.values[0]);
                    $max.val(ui.values[1]);
                  },
                  // This fires when the value is set programmatically or the
                  // stop event fires.
                  // This takes care of the case that a user enters a value
                  // into the text field that is not a valid step of the slider.
                  // In that case the slider will go to the nearest step and
                  // this change event will update the text area.
                  change: function (event, ui) {
                    $min.val(ui.values[0]);
                    $max.val(ui.values[1]);
                  },
                  // Attach stop listeners.
                  stop: function(event, ui) {
                    // Click the auto submit button.
                    $(this).parents('form').find('.ctools-auto-submit-click').click();
                  }
                })
              );

              // Update the slider when the fields are updated.
              $min.blur(function() {
                befUpdateSlider($(this), 0, sliderOptions);
              });
              $max.blur(function() {
                befUpdateSlider($(this), 1, sliderOptions);
              });
            }
            // This is single value filter.
            else if ($input.length == 1) {
              if ($input.attr('id') != 'edit-' + sliderOptions.id) {
                return;
              }

              // Get the default value. We use slider min if there is no default.
              var default_value = parseFloat(($input.val() == '') ? sliderOptions.min : $input.val(), 10);
              // Set the element value in case we are using the slider min.
              $input.val(default_value);

              $input.parents(containing_parent).after(
                $('<div class="bef-slider"></div>').slider({
                  min: parseFloat(sliderOptions.min, 10),
                  max: parseFloat(sliderOptions.max, 10),
                  step: parseFloat(sliderOptions.step, 10),
                  animate: sliderOptions.animate ? sliderOptions.animate : false,
                  orientation: sliderOptions.orientation,
                  value: default_value,
                  // Update the textfields as the sliders are moved.
                  slide: function (event, ui) {
                    $input.val(ui.value);
                  },
                  // This fires when the value is set programmatically or the
                  // stop event fires.
                  // This takes care of the case that a user enters a value
                  // into the text field that is not a valid step of the slider.
                  // In that case the slider will go to the nearest step and
                  // this change event will update the text area.
                  change: function (event, ui) {
                    $input.val(ui.value);
                  },
                  // Attach stop listeners.
                  stop: function(event, ui) {
                    // Click the auto submit button.
                    $(this).parents('form').find('.ctools-auto-submit-click').click();
                  }
                })
              );

              // Update the slider when the field is updated.
              $input.blur(function() {
                befUpdateSlider($(this), null, sliderOptions);
              });
            }
            else {
              return;
            }
          })
        });
      }
    }
  };

  // This is only needed to provide ajax functionality
  Drupal.behaviors.better_exposed_filters_select_as_links = {
    attach: function(context, settings) {

      $('.bef-select-as-links', context).once(function() {
        var $element = $(this);

        // Check if ajax submission is enabled. If it's not enabled then we
        // don't need to attach our custom submission handling, because the
        // links are already properly built.

        // First check if any ajax views are contained in the current page.
        if (typeof settings.views == 'undefined' || typeof settings.views.ajaxViews == 'undefined') {
          return;
        }

        // Now check that the view for which the current filter block is used,
        // is part of the configured ajax views.
        var $uses_ajax = false;
        $.each(settings.views.ajaxViews, function(i, item) {
          var $view_name = item.view_name.replace(/_/g, '-');
          var $view_display_id = item.view_display_id.replace(/_/g, '-');
          var $id = 'views-exposed-form-' + $view_name + '-' + $view_display_id;
          var $form_id = $element.parents('form').attr('id');
          if ($form_id == $id) {
            $uses_ajax = true;
            return;
          }
        });

        // If no ajax is used for form submission, we quit here.
        if (!$uses_ajax) {
          return;
        }

        // Attach selection toggle and form submit on click to each link.
        $(this).find('a').click(function(event) {
          var $wrapper = $(this).parents('.bef-select-as-links');
          var $options = $wrapper.find('select option');
          // We have to prevent the page load triggered by the links.
          event.preventDefault();
          event.stopPropagation();
          // Un select if previously seleted toogle is selected.
          var link_text = $(this).text();
          removed = '';
          $($options).each(function(i) {
            if ($(this).attr('selected')) {
              if (link_text == $(this).text()) {
                removed = $(this).text();
                $(this).removeAttr('selected');
              }
            }
          });

          // Set the corresponding option inside the select element as selected.
          $selected = $options.filter(function() {
            return $(this).text() == link_text && removed != link_text;
          });
          $selected.attr('selected', 'selected');
          $wrapper.find('.bef-new-value').val($selected.val());
          $wrapper.find('.bef-new-value[value=""]').attr("disabled", "disabled");
          $(this).addClass('active');
          // Submit the form.
          $wrapper.parents('form').find('.views-submit-button *[type=submit]').click();
        });

        $('.bef-select-as-link').ready(function() {
          $('.bef-select-as-link').find('a').removeClass('active');
          $('.bef-new-value').each(function(i, val) {
            id = $(this).parent().find('select').attr('id') + '-' + $(this).val();
            $('#'+id).find('a').addClass('active');
          });
        });
      });
    }
  };

  Drupal.behaviors.betterExposedFiltersRequiredFilter = {
    attach: function(context, settings) {
      // Required checkboxes should re-check all inputs if a user un-checks
      // them all.
      $('.bef-select-as-checkboxes', context).once('bef-required-filter').ajaxComplete(function (e, xhr, s) {
        var $element = $(this);

        if (typeof settings.views == 'undefined' || typeof settings.views.ajaxViews == 'undefined') {
          return;
        }

        // Now check that the view for which the current filter block is used,
        // is part of the configured ajax views.
        var $view_name;
        var $view_display_id;
        var $uses_ajax = false;
        $.each(settings.views.ajaxViews, function(i, item) {
          $view_name = item.view_name;
          $view_display_id = item.view_display_id;
          var $id = 'views-exposed-form-' + $view_name.replace(/_/g, '-') + '-' + $view_display_id.replace(/_/g, '-');
          var $form_id = $element.parents('form').attr('id');
          if ($form_id == $id) {
            $uses_ajax = true;
            return false;
          }
        });

        //Check if we have any filters at all because of Views Selective Filter
        if($('input', this).length > 0) {
          var $filter_name = $('input', this).attr('name').slice(0, -2);
          if (Drupal.settings.better_exposed_filters.views[$view_name].displays[$view_display_id].filters[$filter_name].required && $('input:checked', this).length == 0) {
            $('input', this).prop('checked', true);
          }
        }
      });
    }
  }

  /*
   * Helper functions
   */

  /**
   * Adds/Removes the highlight class from the form-item div as appropriate
   */
  function _bef_highlight(elem, context) {
    $elem = $(elem, context);
    $elem.attr('checked')
      ? $elem.closest('.form-item', context).addClass('highlight')
      : $elem.closest('.form-item', context).removeClass('highlight');
  }

  /**
   * Update a slider when a related input element is changed.
   *
   * We don't need to check whether the new value is valid based on slider min,
   * max, and step because the slider will do that automatically and then we
   * update the textfield on the slider's change event.
   *
   * We still have to make sure that the min & max values of a range slider
   * don't pass each other though, however once this jQuery UI bug is fixed we
   * won't have to. - http://bugs.jqueryui.com/ticket/3762
   *
   * @param $el
   *   A jQuery object of the updated element.
   * @param valIndex
   *   The index of the value for a range slider or null for a non-range slider.
   * @param sliderOptions
   *   The options for the current slider.
   */
  function befUpdateSlider($el, valIndex, sliderOptions) {
    var val = parseFloat($el.val(), 10),
        currentMin = $el.parents('div.views-widget').next('.bef-slider').slider('values', 0),
        currentMax = $el.parents('div.views-widget').next('.bef-slider').slider('values', 1);
    // If we have a range slider.
    if (valIndex != null) {
      // Make sure the min is not more than the current max value.
      if (valIndex == 0 && val > currentMax) {
        val = currentMax;
      }
      // Make sure the max is not more than the current max value.
      if (valIndex == 1 && val < currentMin) {
        val = currentMin;
      }
      // If the number is invalid, go back to the last value.
      if (isNaN(val)) {
        val = $el.parents('div.views-widget').next('.bef-slider').slider('values', valIndex);
      }
    }
    else {
      // If the number is invalid, go back to the last value.
      if (isNaN(val)) {
        val = $el.parents('div.views-widget').next('.bef-slider').slider('value');
      }
    }
    // Make sure we are a number again.
    val = parseFloat(val, 10);
    // Set the slider to the new value.
    // The slider's change event will then update the textfield again so that
    // they both have the same value.
    if (valIndex != null) {
      $el.parents('div.views-widget').next('.bef-slider').slider('values', valIndex, val);
    }
    else {
      $el.parents('div.views-widget').next('.bef-slider').slider('value', val);
    }
  }

}) (jQuery);
;
(function ($) {

/**
 * Toggle the visibility of a fieldset using smooth animations.
 */
Drupal.toggleFieldset = function (fieldset) {
  var $fieldset = $(fieldset);
  if ($fieldset.is('.collapsed')) {
    var $content = $('> .fieldset-wrapper', fieldset).hide();
    $fieldset
      .removeClass('collapsed')
      .trigger({ type: 'collapsed', value: false })
      .find('> legend span.fieldset-legend-prefix').html(Drupal.t('Hide'));
    $content.slideDown({
      duration: 'fast',
      easing: 'linear',
      complete: function () {
        Drupal.collapseScrollIntoView(fieldset);
        fieldset.animating = false;
      },
      step: function () {
        // Scroll the fieldset into view.
        Drupal.collapseScrollIntoView(fieldset);
      }
    });
  }
  else {
    $fieldset.trigger({ type: 'collapsed', value: true });
    $('> .fieldset-wrapper', fieldset).slideUp('fast', function () {
      $fieldset
        .addClass('collapsed')
        .find('> legend span.fieldset-legend-prefix').html(Drupal.t('Show'));
      fieldset.animating = false;
    });
  }
};

/**
 * Scroll a given fieldset into view as much as possible.
 */
Drupal.collapseScrollIntoView = function (node) {
  var h = document.documentElement.clientHeight || document.body.clientHeight || 0;
  var offset = document.documentElement.scrollTop || document.body.scrollTop || 0;
  var posY = $(node).offset().top;
  var fudge = 55;
  if (posY + node.offsetHeight + fudge > h + offset) {
    if (node.offsetHeight > h) {
      window.scrollTo(0, posY);
    }
    else {
      window.scrollTo(0, posY + node.offsetHeight - h + fudge);
    }
  }
};

Drupal.behaviors.collapse = {
  attach: function (context, settings) {
    $('fieldset.collapsible', context).once('collapse', function () {
      var $fieldset = $(this);
      // Expand fieldset if there are errors inside, or if it contains an
      // element that is targeted by the URI fragment identifier.
      var anchor = location.hash && location.hash != '#' ? ', ' + location.hash : '';
      if ($fieldset.find('.error' + anchor).length) {
        $fieldset.removeClass('collapsed');
      }

      var summary = $('<span class="summary"></span>');
      $fieldset.
        bind('summaryUpdated', function () {
          var text = $.trim($fieldset.drupalGetSummary());
          summary.html(text ? ' (' + text + ')' : '');
        })
        .trigger('summaryUpdated');

      // Turn the legend into a clickable link, but retain span.fieldset-legend
      // for CSS positioning.
      var $legend = $('> legend .fieldset-legend', this);

      $('<span class="fieldset-legend-prefix element-invisible"></span>')
        .append($fieldset.hasClass('collapsed') ? Drupal.t('Show') : Drupal.t('Hide'))
        .prependTo($legend)
        .after(' ');

      // .wrapInner() does not retain bound events.
      var $link = $('<a class="fieldset-title" href="#"></a>')
        .prepend($legend.contents())
        .appendTo($legend)
        .click(function () {
          var fieldset = $fieldset.get(0);
          // Don't animate multiple times.
          if (!fieldset.animating) {
            fieldset.animating = true;
            Drupal.toggleFieldset(fieldset);
          }
          return false;
        });

      $legend.append(summary);
    });
  }
};

})(jQuery);
;
