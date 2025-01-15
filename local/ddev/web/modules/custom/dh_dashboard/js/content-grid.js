(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.dhContentGrid = {
    attach: function (context, settings) {
      once('content-grid', '.grid-items-container', context).forEach(function (element) {
        const $container = $(element);
        const $grid = $container.find('.content-grid');
        const $items = $grid.find('.grid-item');
        const gridType = $container.closest('.dh-content-grid').data('type');
        
        const itemsPerPage = settings.dhDashboard?.[gridType + '_items_per_page'] || 3;
        const totalPages = Math.ceil($items.length / itemsPerPage);
        let currentPage = 1;

        // Don't proceed if we don't need pagination
        if ($items.length <= itemsPerPage) {
          $container.siblings('.content-pager').hide();
          return;
        }

        const $pager = $container.siblings('.content-pager');
        const $prevButton = $pager.find('.pager__prev');
        const $nextButton = $pager.find('.pager__next');
        const $pageInfo = $pager.find('.pager__info');

        function showPage(page) {
          $items.hide();
          const start = (page - 1) * itemsPerPage;
          const end = start + itemsPerPage;
          
          $items.slice(start, end).show();
          $prevButton.prop('disabled', page === 1);
          $nextButton.prop('disabled', page === totalPages);
          $pageInfo.text(`Page ${page} of ${totalPages}`);
          currentPage = page;
        }

        $prevButton.on('click', () => currentPage > 1 && showPage(currentPage - 1));
        $nextButton.on('click', () => currentPage < totalPages && showPage(currentPage + 1));

        showPage(1);
      });
    }
  };
})(jQuery, Drupal, once);
