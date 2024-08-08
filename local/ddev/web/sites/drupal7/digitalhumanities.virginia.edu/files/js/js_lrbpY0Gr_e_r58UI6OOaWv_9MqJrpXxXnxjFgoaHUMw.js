// TODO: This code could likely all be converted to Form API #states
// (allowing us to remove this file entirely).
// https://api.drupal.org/api/drupal/includes!common.inc/function/drupal_process_states/7

Drupal.behaviors.seckit = {
  attach: function(context) {
    seckit_listener_hsts(context);
    seckit_listener_csp(context);
    seckit_listener_origin(context);
    seckit_listener_clickjacking_x_frame(context);
    seckit_listener_clickjacking_noscript(context);
    seckit_listener_various(context);
    (function ($) {
      $('#edit-seckit-ssl-hsts', context).click(function () {
        seckit_listener_hsts(context);
      });
      $('#edit-seckit-xss-csp-checkbox', context).click(function () {
        seckit_listener_csp(context);
      });
      $('#edit-seckit-xss-csp-policy-uri', context).blur(function () {
        seckit_listener_csp(context);
      });
      $('#edit-seckit-csrf-origin', context).click(function () {
        seckit_listener_origin(context);
      });
      $('#edit-seckit-clickjacking-x-frame', context).change(function () {
        seckit_listener_clickjacking_x_frame(context);
      });
      $('#edit-seckit-clickjacking-js-css-noscript', context).click(function () {
        seckit_listener_clickjacking_noscript(context);
      });
      $('#edit-seckit-various-from-origin', context).click(function () {
        seckit_listener_various(context);
      });
    })(jQuery);
  }
};

/**
 * Adds/removes attributes for input fields in
 * SSL/TLS fieldset for HTTP Strict Transport Security.
 */
function seckit_listener_hsts(context) {
  (function ($) {
    if ($('#edit-seckit-ssl-hsts').is(':checked')) {
      $('#edit-seckit-ssl-hsts-max-age', context).removeAttr('disabled');
      $('#edit-seckit-ssl-hsts-subdomains', context).removeAttr('disabled');
      $('#edit-seckit-ssl-hsts-preload', context).removeAttr('disabled');
      $('label[for="edit-seckit-ssl-hsts-max-age"]', context).append('<span title="' + Drupal.t('This field is required.') + '" class="form-required">*</span>');
    }
    else {
      $('#edit-seckit-ssl-hsts-max-age', context).attr('disabled', 'disabled');
      $('#edit-seckit-ssl-hsts-subdomains', context).attr('disabled', 'disabled');
      $('#edit-seckit-ssl-hsts-preload', context).attr('disabled', 'disabled');
      $('label[for="edit-seckit-ssl-hsts-max-age"] > span', context).remove();
    }
  })(jQuery);
}

/**
 * Adds/removes attributes for input fields in
 * Content Security Policy fieldset.
 */
function seckit_listener_csp(context) {
  (function ($) {
    var checkbox_status = $('#edit-seckit-xss-csp-checkbox').is(':checked');
    var policy_uri_status = $('#edit-seckit-xss-csp-policy-uri').val().length === 0;
    if (checkbox_status) {
      $('#edit-seckit-xss-csp-upgrade-req', context).removeAttr('disabled');
      $('#edit-seckit-xss-csp-report-only', context).removeAttr('disabled');
      $('#edit-seckit-xss-csp-policy-uri', context).removeAttr('disabled');
      if (!policy_uri_status) {
        _seckit_csp_add_attributes(context);
      }
      else {
        _seckit_csp_remove_attributes(context);
      }
    }
    else {
      $('#edit-seckit-xss-csp-upgrade-req', context).attr('disabled', 'disabled');
      $('#edit-seckit-xss-csp-report-only', context).attr('disabled', 'disabled');
      $('#edit-seckit-xss-csp-policy-uri', context).attr('disabled', 'disabled');
      _seckit_csp_add_attributes(context);
    }
  })(jQuery);
}

/**
 * Removes attributes for CSP input fields.
 */
function _seckit_csp_remove_attributes(context) {
  (function ($) {
    $('#edit-seckit-xss-csp-default-src', context).removeAttr('disabled');
    $('#edit-seckit-xss-csp-script-src', context).removeAttr('disabled');
    $('#edit-seckit-xss-csp-object-src', context).removeAttr('disabled');
    $('#edit-seckit-xss-csp-style-src', context).removeAttr('disabled');
    $('#edit-seckit-xss-csp-img-src', context).removeAttr('disabled');
    $('#edit-seckit-xss-csp-media-src', context).removeAttr('disabled');
    $('#edit-seckit-xss-csp-frame-src', context).removeAttr('disabled');
    $('#edit-seckit-xss-csp-frame-ancestors', context).removeAttr('disabled');
    $('#edit-seckit-xss-csp-child-src', context).removeAttr('disabled');
    $('#edit-seckit-xss-csp-font-src', context).removeAttr('disabled');
    $('#edit-seckit-xss-csp-connect-src', context).removeAttr('disabled');
    $('#edit-seckit-xss-csp-report-uri', context).removeAttr('disabled');
  })(jQuery);
}

/**
 * Adds attributes for CSP input fields.
 */
function _seckit_csp_add_attributes(context) {
  (function ($) {
    $('#edit-seckit-xss-csp-default-src', context).attr('disabled', 'disabled');
    $('#edit-seckit-xss-csp-script-src', context).attr('disabled', 'disabled');
    $('#edit-seckit-xss-csp-object-src', context).attr('disabled', 'disabled');
    $('#edit-seckit-xss-csp-style-src', context).attr('disabled', 'disabled');
    $('#edit-seckit-xss-csp-img-src', context).attr('disabled', 'disabled');
    $('#edit-seckit-xss-csp-media-src', context).attr('disabled', 'disabled');
    $('#edit-seckit-xss-csp-frame-src', context).attr('disabled', 'disabled');
    $('#edit-seckit-xss-csp-frame-ancestors', context).attr('disabled', 'disabled');
    $('#edit-seckit-xss-csp-child-src', context).attr('disabled', 'disabled');
    $('#edit-seckit-xss-csp-font-src', context).attr('disabled', 'disabled');
    $('#edit-seckit-xss-csp-connect-src', context).attr('disabled', 'disabled');
    $('#edit-seckit-xss-csp-report-uri', context).attr('disabled', 'disabled');
  })(jQuery);
}

/**
 * Adds/removes attributes for input fields in
 * Cross-site Request Forgery fieldset for HTTP Origin.
 */
function seckit_listener_origin(context) {
  (function ($) {
    if ($('#edit-seckit-csrf-origin').is(':checked')) {
      $('#edit-seckit-csrf-origin-whitelist', context).removeAttr('disabled');
    }
    else {
      $('#edit-seckit-csrf-origin-whitelist', context).attr('disabled', 'disabled');
    }
  })(jQuery);
}

/**
 * Adds/removes attributes for input fields in
 * Clickjacking "X-Frame-Options" fields.
 */
function seckit_listener_clickjacking_x_frame(context) {
  (function ($) {
    if ($('#edit-seckit-clickjacking-x-frame').find(":selected").text() == 'ALLOW-FROM') {
      $('#edit-seckit-clickjacking-x-frame-allow-from', context).removeAttr('disabled');
      $('label[for="edit-seckit-clickjacking-x-frame-allow-from"]', context).append('<span title="' + Drupal.t('This field is required.') + '" class="form-required">*</span>');
    }
    else {
      $('#edit-seckit-clickjacking-x-frame-allow-from', context).attr('disabled', 'disabled');
      $('label[for="edit-seckit-clickjacking-x-frame-allow-from"] > span', context).remove();
    }
  })(jQuery);
}

/**
 * Adds/removes attributes for input fields in
 * Clickjacking NoScript fields.
 */
function seckit_listener_clickjacking_noscript(context) {
  (function ($) {
    if ($('#edit-seckit-clickjacking-js-css-noscript').is(':checked')) {
      $('#edit-seckit-clickjacking-noscript-message', context).removeAttr('disabled');
    }
    else {
      $('#edit-seckit-clickjacking-noscript-message', context).attr('disabled', 'disabled');
    }
  })(jQuery);
}

/**
 * Adds/removes attributes for input fields in
 * Various fieldset.
 */
function seckit_listener_various(context) {
  (function ($) {
    if ($('#edit-seckit-various-from-origin').is(':checked')) {
      $('#edit-seckit-various-from-origin-destination', context).removeAttr('disabled');
    }
    else {
      $('#edit-seckit-various-from-origin-destination', context).attr('disabled', 'disabled');
    }
  })(jQuery);
}
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
(function ($) {

Drupal.behaviors.textarea = {
  attach: function (context, settings) {
    $('.form-textarea-wrapper.resizable', context).once('textarea', function () {
      var staticOffset = null;
      var textarea = $(this).addClass('resizable-textarea').find('textarea');
      var grippie = $('<div class="grippie"></div>').mousedown(startDrag);

      grippie.insertAfter(textarea);

      function startDrag(e) {
        staticOffset = textarea.height() - e.pageY;
        textarea.css('opacity', 0.25);
        $(document).mousemove(performDrag).mouseup(endDrag);
        return false;
      }

      function performDrag(e) {
        textarea.height(Math.max(32, staticOffset + e.pageY) + 'px');
        return false;
      }

      function endDrag(e) {
        $(document).unbind('mousemove', performDrag).unbind('mouseup', endDrag);
        textarea.css('opacity', 1);
      }
    });
  }
};

})(jQuery);
;
