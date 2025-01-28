(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.viewModeToggle = {
    attach: function (context, settings) {
      // Initialize view mode from localStorage or default
      const savedView = localStorage.getItem('dhCertificateViewMode') || 'grid';
      $('.course-list', context).attr('data-view', savedView);
      $(`.view-toggle button[data-view="${savedView}"]`, context).addClass('active');

      // Handle view mode toggle clicks
      once('view-mode-toggle', '.view-toggle button', context).forEach(function (button) {
        $(button).on('click', function (e) {
          e.preventDefault();
          const viewMode = $(this).data('view');
          
          // Update active button state
          $('.view-toggle button').removeClass('active');
          $(this).addClass('active');
          
          // Update view mode on course list
          $('.course-list').attr('data-view', viewMode);
          
          // Save preference
          localStorage.setItem('dhCertificateViewMode', viewMode);
          
          // Trigger custom event for other scripts
          $(document).trigger('viewModeChanged', [viewMode]);
        });
      });
    }
  };
})(jQuery, Drupal);
