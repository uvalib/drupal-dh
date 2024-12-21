(function ($, Drupal, once) {
  'use strict';
  
  Drupal.behaviors.certificateProgress = {
    attach: function (context, settings) {
      once('certificate-progress', '.requirement-item', context).forEach(function (element) {
        $(element).on('click', function() {
          $(this).toggleClass('expanded');
        });
      });
    }
  };
})(jQuery, Drupal, once);
