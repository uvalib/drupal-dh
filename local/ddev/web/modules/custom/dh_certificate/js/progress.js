(function ($, Drupal) {
  // ...existing code...
  
  Drupal.behaviors.certificateProgress = {
    attach: function (context, settings) {
      // Update any hardcoded URLs to use admin_progress instead of progress
      const baseUrl = settings.path.baseUrl + 'admin/config/dh-certificate/progress';
      // ...existing code...
    }
  };
  
  // ...existing code...
})(jQuery, Drupal);
