(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.dhDashboard = {
    attach: function (context, settings) {
      // Remove client-side pagination logic

      // Display mode toggle
      once('dh-dashboard-display', '.display-mode-toggle', context).forEach(function (toggle) {
        $(toggle).on('click', function() {
          const $container = $(this).closest('.dh-dashboard-block').find('.dh-dashboard-items');
          const newMode = $container.hasClass('display-mode-grid') ? 'list' : 'grid';
          $container.removeClass('display-mode-grid display-mode-list').addClass('display-mode-' + newMode);
        });
      });

      // Accordion functionality
      once('dh-dashboard-accordion', '.accordion-toggle', context).forEach(function (toggle) {
        const $toggle = $(toggle);
        const $content = $toggle.next('.accordion-content');
        
        if (!$toggle.hasClass('is-open')) {
          $content.hide();
        }

        $toggle.on('click', function() {
          $toggle.toggleClass('is-open');
          $content.slideToggle(300);
          $toggle.attr('aria-expanded', $toggle.hasClass('is-open'));
          $content.attr('aria-hidden', !$toggle.hasClass('is-open'));
        });
      });
    }
  };

  Drupal.behaviors.dhDashboardPager = {
    attach: function (context, settings) {
      once('dh-dashboard-pager', '.dashboard-pager', context).forEach(function (pager) {
        const $pager = $(pager);
        
        // Handle pagination clicks
        $pager.on('click', '.pager__link', function(e) {
          e.preventDefault();
          const $link = $(this);
          
          if ($link.attr('disabled')) {
            return;
          }

          // Get the page from the URL
          const url = new URL($link.attr('href'), window.location.origin);
          const page = url.searchParams.get('page');

          // Update URL without reload
          window.history.pushState({}, '', url);
          
          // Trigger custom event for block refresh
          $pager.trigger('pagerChanged', [page]);
        });

        // Listen for pagerChanged event
        $pager.on('pagerChanged', function(e, page) {
          // Update active state
          $pager.find('.pager__item').removeClass('is-active');
          $pager.find(`.pager__link[href="?page=${page}"]`).parent().addClass('is-active');
          
          // Update prev/next states
          const totalPages = parseInt($pager.data('total-pages'));
          $pager.find('.pager__item--prev .pager__link').attr('disabled', page <= 0);
          $pager.find('.pager__item--next .pager__link').attr('disabled', page >= (totalPages - 1));
        });
      });
    }
  };
})(jQuery, Drupal, once);
