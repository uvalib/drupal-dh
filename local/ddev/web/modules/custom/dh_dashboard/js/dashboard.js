(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.dhDashboard = {
    attach: function (context, settings) {
      console.log('Dashboard behavior attached'); // Debug line
      once('dhDashboard', '.dashboard-page', context).forEach(function (element) {
        // Dashboard initialization code can go here
        console.log('Initializing dashboard'); // Debug line

        // Initialize Lightbox2
        lightbox.option({
          'resizeDuration': 200,
          'wrapAround': true
        });

        // Debug Lightbox2 initialization
        console.log('Lightbox2 initialized'); // Debug line

        // Add event listeners for Lightbox2
        $(document).on('lightbox:open', function() {
          console.log('Lightbox opened'); // Debug line
        });

        $(document).on('lightbox:close', function() {
          console.log('Lightbox closed'); // Debug line
        });

        $(document).on('lightbox:load', function() {
          console.log('Lightbox content loaded'); // Debug line
        });

        $(document).on('lightbox:error', function() {
          console.log('Lightbox error'); // Debug line
        });

        // Check if video thumbnail exists
        const videoThumbnail = $(element).find('.video-thumbnail');
        if (videoThumbnail.length) {
          console.log('Video thumbnail found:', videoThumbnail.attr('src')); // Debug line
        } else {
          console.log('Video thumbnail not found'); // Debug line
        }

        // Debug progress bar
        const progressBar = $(element).find('.progress-bar');
        const completedFill = progressBar.find('.progress-fill.completed');
        const inProgressFill = progressBar.find('.progress-fill.in-progress');

        // Check data attributes
        const completedPercentage = $(element).data('completed-percentage');
        const inProgressPercentage = $(element).data('in-progress-percentage');

        // console.log('Completed percentage:', completedPercentage); // Debug line
        // console.log('In-progress percentage:', inProgressPercentage); // Debug line

        // console.log('Completed fill width before:', completedFill.css('width')); // Debug line
        // console.log('In-progress fill width before:', inProgressFill.css('width')); // Debug line

        // Update progress bar widths
        completedFill.css('width', completedPercentage + '%');
        inProgressFill.css('width', inProgressPercentage + '%');
        inProgressFill.css('left', completedPercentage + '%');

        // console.log('Completed fill width after:', completedFill.css('width')); // Debug line
        // console.log('In-progress fill width after:', inProgressFill.css('width')); // Debug line

        // Log the entire element data for debugging
        // console.log('Element data:', $(element).data()); // Debug line

        // Accordion functionality
        const userCard = $(element).find('.user-card');
        console.log('User card found:', userCard.length); // Debug line

        const accordionToggle = userCard.find('.accordion-toggle');
        console.log('Accordion toggle found:', accordionToggle.length); // Debug line

        accordionToggle.on('click', function() {
          console.log('Accordion toggle clicked'); // Debug line
          const details = $(this).closest('.user-card').find('.profile-details');
          console.log('Profile details found:', details.length); // Debug line
          details.slideToggle();
          const icon = $(this).find('svg');
          icon.toggleClass('rotated');
          console.log('Icon classes:', icon.attr('class')); // Debug line
        });

        // Limit the number of news items shown
        const newsItems = $(element).find('.news-item');
        const itemsPerPage = settings.dhDashboard?.news_items_per_page || 3;
        console.log('Items per page:', itemsPerPage);

        newsItems.each(function(index) {
          if (index >= itemsPerPage) {
            $(this).hide();
          }
        });
      });
    }
  };
})(jQuery, Drupal);