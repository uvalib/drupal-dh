(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.dhDashboardPager = {
    attach: function (context, settings) {
      const debug = settings.dhDashboard && settings.dhDashboard.debug;
      
      once('dh-pager', '.dashboard-pager .pager__link', context).forEach(function(element) {
        $(element).on('click', function(e) {
          e.preventDefault();
          
          const $button = $(this);
          const $pager = $button.closest('.dashboard-pager');
          const $block = $pager.closest('.dh-dashboard-block');
          const blockId = $pager.data('block-id');
          const page = $button.data('page');

          if (debug) {
            console.log('Pager clicked:', { blockId, page });
          }

          if (!blockId) {
            console.error('No block_id found in pager data');
            return;
          }

          // Add loading state
          $block.addClass('is-loading');
          $button.prop('disabled', true);

          // Make AJAX request using jQuery instead of Drupal.ajax
          $.ajax({
            url: Drupal.url(`dh-dashboard/ajax/${blockId.includes('news') ? 'news' : 'events'}`),
            type: 'GET',
            data: {
              block_id: blockId,
              page: page
            },
            success: function(response) {
              if (response.content) {
                $block.html(response.content);
                // Reattach behaviors to the new content
                Drupal.attachBehaviors($block[0], settings);
              }
            },
            error: function(xhr, status, error) {
              console.error('AJAX error:', error);
            },
            complete: function() {
              $block.removeClass('is-loading');
              $button.prop('disabled', false);
            }
          });
        });
      });
    }
  };
})(jQuery, Drupal, once);
