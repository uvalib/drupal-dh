(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.dhDashboard = {
    attach: function (context, settings) {
      console.log('Dashboard behavior attached'); // Debug line
      once('dhDashboard', '.dh-dashboard', context).forEach(function (element) {
        // Dashboard initialization code can go here
        console.log('Initializing dashboard'); // Debug line

        // Debug progress bar
        const progressBar = $(element).find('.progress-bar');
        const completedFill = progressBar.find('.progress-fill.completed');
        const inProgressFill = progressBar.find('.progress-fill.in-progress');

        // Check data attributes
        const completedPercentage = $(element).data('completed-percentage');
        const inProgressPercentage = $(element).data('in-progress-percentage');

        console.log('Completed percentage:', completedPercentage); // Debug line
        console.log('In-progress percentage:', inProgressPercentage); // Debug line

        console.log('Completed fill width before:', completedFill.css('width')); // Debug line
        console.log('In-progress fill width before:', inProgressFill.css('width')); // Debug line

        // Update progress bar widths
        completedFill.css('width', completedPercentage + '%');
        inProgressFill.css('width', inProgressPercentage + '%');
        inProgressFill.css('left', completedPercentage + '%');

        console.log('Completed fill width after:', completedFill.css('width')); // Debug line
        console.log('In-progress fill width after:', inProgressFill.css('width')); // Debug line
      });
    }
  };
})(jQuery, Drupal);