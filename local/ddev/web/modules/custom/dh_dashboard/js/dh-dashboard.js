(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.dhDashboard = {
    attach: function (context, settings) {
      // Generic pager functionality
      once('dh-dashboard-pager', '.dh-dashboard-pager', context).forEach(function (pager) {
        const $pager = $(pager);
        const $container = $pager.closest('.dh-dashboard-items');
        const $items = $container.find('.dh-dashboard-item');
        const perPage = parseInt($pager.data('per-page'));
        const totalPages = Math.ceil($items.length / perPage);
        let currentPage = 1;

        function showPage(page) {
          const start = (page - 1) * perPage;
          const end = start + perPage;
          
          $items.hide();
          $items.slice(start, end).show();
          
          $pager.find('.current-page').text(page);
          $pager.find('.pager-prev').prop('disabled', page === 1);
          $pager.find('.pager-next').prop('disabled', page === totalPages);
        }

        $pager.find('.pager-prev').on('click', function() {
          if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
          }
        });

        $pager.find('.pager-next').on('click', function() {
          if (currentPage < totalPages) {
            currentPage++;
            showPage(currentPage);
          }
        });

        showPage(1);
      });

      // News specific paging
      once('dh-dashboard-news', '.news-items-container', context).forEach(function (element) {
        const container = $(element);
        const newsItems = container.find('.news-item');
        const itemsPerPage = parseInt(settings.dhDashboard?.items_per_page) || 3;
        const totalPages = Math.ceil(newsItems.length / itemsPerPage);
        
        if (newsItems.length <= itemsPerPage) {
          container.next('.news-pager').hide();
          return;
        }

        const pager = container.closest('.dh-news-block').find('.news-pager');
        if (!pager.length) return;

        const prevButton = pager.find('.news-pager__prev');
        const nextButton = pager.find('.news-pager__next');
        const pageInfo = pager.find('.news-pager__info');
        let currentPage = 1;

        function showNewsPage(page) {
          newsItems.hide();
          newsItems.slice((page - 1) * itemsPerPage, page * itemsPerPage).show();
          prevButton.prop('disabled', page === 1);
          nextButton.prop('disabled', page === totalPages);
          pageInfo.text(`Page ${page} of ${totalPages}`);
          currentPage = page;
        }

        prevButton.on('click', e => {
          e.preventDefault();
          if (currentPage > 1) showNewsPage(currentPage - 1);
        });

        nextButton.on('click', e => {
          e.preventDefault();
          if (currentPage < totalPages) showNewsPage(currentPage + 1);
        });

        showNewsPage(1);
      });

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
})(jQuery, Drupal, once);
