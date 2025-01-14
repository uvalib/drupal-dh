(function ($, Drupal) {
  'use strict';

  Drupal.behaviors.dhDashboardNewsPager = {
    attach: function (context, settings) {
      // Debug entire settings object
      console.log('All drupalSettings:', settings);
      console.log('dhDashboard settings:', settings.dhDashboard);
      
      once('dhDashboardNewsPager', '.news-items-container', context).forEach(function (element) {
        const container = $(element);
        const newsItems = container.find('.news-item');
        
        const configValue = settings.dhDashboard?.items_per_page;
        console.log('Config items_per_page (raw):', configValue, typeof configValue);
        
        const itemsPerPage = parseInt(configValue) || 3;
        console.log('Final items_per_page:', itemsPerPage, typeof itemsPerPage);
        
        const totalPages = Math.ceil(newsItems.length / itemsPerPage);
        let currentPage = 1;
        
        // Don't proceed if we don't need pagination
        if (newsItems.length <= itemsPerPage) {
          container.next('.news-pager').hide();
          return;
        }

        // Look for pager in parent block
        const pager = container.closest('.dh-news-block').find('.news-pager');
        
        if (!pager.length) {
          console.error('Pager element not found');
          return;
        }

        const prevButton = pager.find('.news-pager__prev');
        const nextButton = pager.find('.news-pager__next');
        const pageInfo = pager.find('.news-pager__info');

        function showPage(page) {
          console.log(`Showing page ${page}`);
          newsItems.hide();
          
          const start = (page - 1) * itemsPerPage;
          const end = start + itemsPerPage;
          
          newsItems.slice(start, end).show();
          
          prevButton.prop('disabled', page === 1);
          nextButton.prop('disabled', page === totalPages);
          pageInfo.text(`Page ${page} of ${totalPages}`);
          
          currentPage = page;
        }

        // Event handlers
        prevButton.on('click', function(e) {
          e.preventDefault();
          if (currentPage > 1) showPage(currentPage - 1);
        });

        nextButton.on('click', function(e) {
          e.preventDefault();
          if (currentPage < totalPages) showPage(currentPage + 1);
        });

        // Initialize display
        if (newsItems.length > itemsPerPage) {
          showPage(1);
        } else {
          pager.hide();
        }
      });
    }
  };
})(jQuery, Drupal);