(function ($, Drupal) {
  Drupal.behaviors.dhDashboardAdminForm = {
    attach: function (context, settings) {
      const form = once('dh-dashboard-form', '#dh-dashboard-settings', context);
      if (form.length) {
        let formChanged = false;

        // Track form changes
        $(form).find('input, select, textarea').on('change', function() {
          formChanged = true;
        });

        // Add warning when leaving with unsaved changes
        $(window).on('beforeunload', function() {
          if (formChanged) {
            return 'You have unsaved changes. Are you sure you want to leave?';
          }
        });

        // Clear warning when form is submitted
        $(form).on('submit', function() {
          formChanged = false;
        });
      }
    }
  };
})(jQuery, Drupal);
