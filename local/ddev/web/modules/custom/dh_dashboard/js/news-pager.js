(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.dhDashboardNewsPager = {
    attach: function (context, settings) {
      // console.log('Pager behavior attached'); // Debug line
      
      once('dhDashboardNewsPager', '.news-items-container', context).forEach(function (element) {
        // console.log('Processing news container'); // Debug line
        const container = $(element);
        const newsItems = container.find('.news-item');
        const itemsPerPage = 3;
        const totalPages = Math.ceil(newsItems.length / itemsPerPage);

        // console.log(`Found ${newsItems.length} items, ${totalPages} pages`); // Debug line

        if (newsItems.length <= itemsPerPage) {
          // console.log('Not enough items for pagination'); // Debug line
          return;
        }

        // Create and add pager
        const pager = $(`
          <div class="news-pager">
            <button class="news-pager__button news-pager__prev" disabled>&laquo; Previous</button>
            <span class="news-pager__info">Page 1 of ${totalPages}</span>
            <button class="news-pager__button news-pager__next">Next &raquo;</button>
          </div>
        `);
        
        container.after(pager);
        
        let currentPage = 1;

        function showPage(page) {
          newsItems.hide();
          newsItems.slice((page - 1) * itemsPerPage, page * itemsPerPage).show();
          
          pager.find('.news-pager__prev').prop('disabled', page === 1);
          pager.find('.news-pager__next').prop('disabled', page === totalPages);
          pager.find('.news-pager__info').text(`Page ${page} of ${totalPages}`);
          
          currentPage = page;
        }

        pager.on('click', '.news-pager__prev', function() {
          if (currentPage > 1) showPage(currentPage - 1);
        });

        pager.on('click', '.news-pager__next', function() {
          if (currentPage < totalPages) showPage(currentPage + 1);
        });

        // Initialize
        showPage(1);
      });
    }
  };
})(jQuery, Drupal);
