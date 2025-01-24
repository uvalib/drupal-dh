(function ($, Drupal) {
  'use strict';

  console.log('debug-tree.js loaded');

  Drupal.behaviors.debugTree = {
    attach: function (context, settings) {
      console.log('debugTree behavior attaching', context);
      
      // Target all details elements within debug-tree
      const trees = once('debug-tree', '.debug-tree details', context);
      console.log('Found detail elements:', trees.length);
      
      trees.forEach(function (details) {
        const $details = $(details);
        const $summary = $details.children('summary');
        
        console.log('Attaching click handler to summary:', $summary[0]);
        
        $summary.on('click', function(e) {
          console.log('Summary clicked', e);
          // If any modifier key is pressed (Shift, Ctrl, Alt, Meta)
          if (e.shiftKey || e.ctrlKey || e.altKey || e.metaKey) {
            console.log('Modifier key detected, handling recursive open/close');
            e.preventDefault();
            e.stopPropagation();
            
            const isOpen = !$details.prop('open');
            
            // Set this element and all nested details to the same state
            $details.prop('open', isOpen);
            $details.find('details').prop('open', isOpen);
          }
        });
      });
    }
  };
})(jQuery, Drupal);
