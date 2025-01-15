(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.dhDashboardUserCard = {
    attach: function (context, settings) {
      once('user-card-toggle', '.accordion-toggle', context).forEach(function (element) {
        const $toggle = $(element);
        const $details = $toggle.closest('.profile-header').next('.profile-details');
        const $icon = $toggle.find('svg');
        
        $toggle.on('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          console.log('Toggle clicked');
          
          $toggle.toggleClass('is-open');
          $details.slideToggle(300);
        });
      });
    }
  };
})(jQuery, Drupal, once);
