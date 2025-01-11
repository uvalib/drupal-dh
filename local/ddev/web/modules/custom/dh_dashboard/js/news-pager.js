(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.dhDashboardNewsPager = {
    attach: function (context, settings) {
      once('dhDashboardNewsPager', '.block-dh-dashboard-news', context).forEach(function (element) {
        const container = $(element).find('.news-items-container');
        const newsItems = container.find('.news-item');
        const itemsPerPage = 3;
        let currentPage = 1;
        const totalPages = Math.ceil(newsItems.length / itemsPerPage);

        // Create pager elements
        const pager = $('<div class="news-pager"></div>');
        const prevButton = $('<button class="news-pager__button news-pager__prev">&laquo; Previous</button>');
        const nextButton = $('<button class="news-pager__button news-pager__next">Next &raquo;</button>');
        const pageInfo = $('<span class="news-pager__info"></span>');

        // Add pager to DOM
        pager.append(prevButton).append(pageInfo).append(nextButton);
        container.after(pager);

        function showPage(page) {
          const start = (page - 1) * itemsPerPage;
          const end = start + itemsPerPage;

          newsItems.hide();
          newsItems.slice(start, end).show();

          // Update buttons state
          prevButton.prop('disabled', page === 1);
          nextButton.prop('disabled', page === totalPages);
          
          // Update page info
          pageInfo.text(`Page ${page} of ${totalPages}`);
          currentPage = page;
        }

        // Event handlers
        prevButton.on('click', function() {
          if (currentPage > 1) {
            showPage(currentPage - 1);
          }
        });

        nextButton.on('click', function() {
          if (currentPage < totalPages) {
            showPage(currentPage + 1);
          }
        });

        // Initialize first page
        if (newsItems.length > itemsPerPage) {
          showPage(1);
        } else {
          pager.hide();
        }
      });
    }
  };
})(jQuery, Drupal);
