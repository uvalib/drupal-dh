(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.dhDashboard = {
    attach: function (context, settings) {
      console.log('Dashboard behavior attached');
      once('dhDashboard', '.dashboard-page', context).forEach(function (element) {
        console.log('Initializing dashboard');

        // Initialize Lightbox2 if available
        if (typeof lightbox !== 'undefined') {
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
        }

        // Check if video thumbnail exists
        const videoThumbnail = $(element).find('.video-thumbnail');
        if (videoThumbnail.length) {
          console.log('Video thumbnail found:', videoThumbnail.attr('src'));
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

        // Update progress bar widths
        completedFill.css('width', completedPercentage + '%');
        inProgressFill.css('width', inProgressPercentage + '%');
        inProgressFill.css('left', completedPercentage + '%');

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
})(jQuery, Drupal, once);