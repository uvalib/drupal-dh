
(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.dhDashboard = {
    attach: function (context, settings) {
      once('dhDashboard', '.dh-dashboard', context).forEach(function (element) {
        // Dashboard initialization code can go here
      });
    }
  };
})(jQuery, Drupal);