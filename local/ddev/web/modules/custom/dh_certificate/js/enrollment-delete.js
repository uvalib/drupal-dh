(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.enrollmentDelete = {
    attach: function (context, settings) {
      $('.enrollment-delete-button', context).once('enrollment-delete').on('click', function (e) {
        e.preventDefault();
        const courseTitle = $(this).data('enrollment-course');
        if (confirm(Drupal.t('Are you sure you want to delete the enrollment for "@course"? This action cannot be undone.', {'@course': courseTitle}))) {
          window.location = $(this).attr('href');
        }
      });
    }
  };
})(jQuery, Drupal);
