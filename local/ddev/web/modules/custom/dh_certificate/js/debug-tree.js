(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.debugTree = {
    attach: function (context, settings) {
      once('debug-tree', '.debug-tree', context).forEach(function (tree) {
        $(tree).on('click', 'summary', function(e) {
          // If any modifier key is pressed (Shift, Ctrl, Alt, Meta)
          if (e.shiftKey || e.ctrlKey || e.altKey || e.metaKey) {
            e.preventDefault();
            e.stopPropagation();
            
            const details = $(this).parent('details');
            const isOpen = !details.prop('open');
            
            // Set this element and all nested details to the same state
            details.prop('open', isOpen);
            details.find('details').prop('open', isOpen);
          }
        });
      });
    }
  };
})(jQuery, Drupal);
