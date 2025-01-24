(function ($, Drupal, once) {
  'use strict';

  Drupal.behaviors.dhCertificateCourseListing = {
    attach: function (context, settings) {
      once('dh-certificate-course-listing', '.course-listing', context).forEach(function (element) {
        const $wrapper = $(element);
        const $viewButtons = $wrapper.find('.view-toggle button');
        const $searchInput = $wrapper.find('.course-search input');
        const $filterInputs = $wrapper.find('.course-filters select');
        
        // Get settings with defaults
        const config = settings.dhCertificate || {};
        let currentPage = 0;
        let currentView = config.defaultView || 'grid';
        
        // Debug log settings
        if (config.debug) {
          console.log('DH Certificate Settings:', config);
        }

        function loadCourses() {
          const $courseList = $wrapper.find('.course-list');
          const $loading = $courseList.find('.course-list__loading');
          const $error = $courseList.find('.course-list__error');
          
          $loading.show();
          $error.hide();
          
          const searchTerm = $searchInput.val();
          const filters = {};
          
          $filterInputs.each(function() {
            filters[$(this).attr('name')] = $(this).val();
          });

          // Use ajaxPath from settings or construct from baseUrl
          const ajaxPath = config.paths?.ajax || 
            `${drupalSettings.path.baseUrl}admin/config/dh-certificate/courses/ajax`;

          // Remove any duplicate slashes except for protocol
          const cleanPath = ajaxPath.replace(/([^:]\/)\/+/g, "$1");

          $.ajax({
            url: cleanPath,
            method: 'GET',
            data: {
              view: currentView,
              page: currentPage,
              search: searchTerm,
              'filter[type]': $wrapper.find('.course-filters select[name="type"]').val()
            },
            success: function(response) {
              $loading.hide();
              updateCourseDisplay(response.courses);
              updatePagination(response.total);
            },
            error: function(xhr, status, error) {
              $loading.hide();
              $error.show().text('Error loading courses: ' + (error || 'Unknown error'));
              console.error('Failed to load courses:', {xhr, status, error, path: cleanPath});
            }
          });
        }

        function updateCourseDisplay(courses) {
          const $courseList = $wrapper.find('.course-list');
          $courseList.empty();

          if (courses.length === 0) {
            $courseList.append('<div class="no-results">No courses found</div>');
            return;
          }

          courses.forEach(course => {
            const courseElement = createCourseElement(course, currentView);
            $courseList.append(courseElement);
          });

          $wrapper.attr('data-view', currentView);
        }

        function createCourseElement(course, view) {
          const template = $wrapper.find(`template[data-view="${view}"]`).html();
          return template.replace(/\{\{(\w+)\}\}/g, (match, key) => course[key] || '');
        }

        function updatePagination(total) {
          const $pagination = $wrapper.find('.course-pagination');
          $pagination.empty();

          const itemsPerPage = drupalSettings.dhCertificate.itemsPerPage || 12;
          const totalPages = Math.ceil(total / itemsPerPage);

          if (totalPages <= 1) {
            return;
          }

          // Add previous button
          if (currentPage > 0) {
            $pagination.append(
              $('<button>')
                .text('«')
                .on('click', () => {
                  currentPage--;
                  loadCourses();
                })
            );
          }

          // Add page numbers
          for (let i = 0; i < totalPages; i++) {
            $pagination.append(
              $('<button>')
                .text(i + 1)
                .toggleClass('active', i === currentPage)
                .on('click', () => {
                  currentPage = i;
                  loadCourses();
                })
            );
          }

          // Add next button
          if (currentPage < totalPages - 1) {
            $pagination.append(
              $('<button>')
                .text('»')
                .on('click', () => {
                  currentPage++;
                  loadCourses();
                })
            );
          }
        }

        // Event handlers
        $viewButtons.on('click', function() {
          currentView = $(this).data('view');
          $viewButtons.removeClass('active');
          $(this).addClass('active');
          loadCourses();
        });

        $searchInput.on('input debounce', function() {
          currentPage = 0;
          loadCourses();
        });

        $filterInputs.on('change', function() {
          currentPage = 0;
          loadCourses();
        });

        // Initial load
        loadCourses();
      });
    }
  };

  Drupal.behaviors.courseViewToggle = {
    attach: function (context, settings) {
      once('course-view-toggle', '.view-toggle button', context).forEach(function (button) {
        $(button).on('click', function() {
          const $button = $(this);
          const $container = $button.closest('.certificate-courses');
          const viewMode = $button.data('view');
          
          // Update active state
          $button.siblings().removeClass('active');
          $button.addClass('active');
          
          // Update view mode
          $container.find('.course-list').attr('data-view', viewMode);
          
          // Store preference
          if (window.localStorage) {
            localStorage.setItem('dhCertificateViewMode', viewMode);
          }
        });
      });

      // Apply stored preference on load
      if (window.localStorage && localStorage.getItem('dhCertificateViewMode')) {
        const storedView = localStorage.getItem('dhCertificateViewMode');
        $(`.view-toggle button[data-view="${storedView}"]`).trigger('click');
      }
    }
  };

  Drupal.behaviors.courseDescriptionScroll = {
    attach: function (context, settings) {
      once('course-description-scroll', '.course-description', context).forEach(function(element) {
        const $description = $(element);
        const $wrapper = $description.closest('.course-description-wrapper');
        
        function updateFade() {
          const maxScroll = element.scrollHeight - element.clientHeight;
          const isScrollable = element.scrollHeight > element.clientHeight;
          const isAtBottom = Math.ceil(element.scrollTop) >= maxScroll;
          
          $wrapper.toggleClass('at-bottom', !isScrollable || isAtBottom);
        }
        
        // Initial check
        updateFade();
        
        // Update on scroll
        $description.on('scroll', updateFade);
        
        // Update on window resize
        $(window).on('resize', updateFade);
      });
    }
  };

  Drupal.behaviors.courseListingFilters = {
    attach: function (context, settings) {
      once('course-filters', '.course-list', context).forEach(function(element) {
        const $courseList = $(element);
        const $items = $courseList.find('.course-item');
        const $search = $('.course-search input');
        const $filters = $('.course-filters select');
        const $sortButtons = $('.sort-controls button');

        function filterCourses() {
          const searchTerm = $search.val().toLowerCase();
          const filters = {};
          $filters.each(function() {
            filters[$(this).attr('name')] = $(this).val();
          });

          $items.each(function() {
            const $item = $(this);
            const text = $item.text().toLowerCase();
            const type = $item.data('type');
            const semester = $item.data('semester');
            
            const matchesSearch = !searchTerm || text.includes(searchTerm);
            const matchesType = !filters.type || type === filters.type;
            const matchesSemester = !filters.semester || semester === filters.semester;

            $item.toggle(matchesSearch && matchesType && matchesSemester);
          });
        }

        function sortCourses(field, direction = 'asc') {
          const $items = $courseList.find('.course-item').toArray();
          $items.sort((a, b) => {
            let valA = $(a).find(`.course-${field}`).text();
            let valB = $(b).find(`.course-${field}`).text();
            
            if (direction === 'desc') {
              [valA, valB] = [valB, valA];
            }
            
            return valA.localeCompare(valB);
          });
          
          $courseList.append($items);
        }

        // Event handlers
        $search.on('input debounce', filterCourses);
        $filters.on('change', filterCourses);
        $sortButtons.on('click', function() {
          const field = $(this).data('sort');
          const direction = $(this).hasClass('asc') ? 'desc' : 'asc';
          
          $sortButtons.removeClass('asc desc');
          $(this).addClass(direction);
          
          sortCourses(field, direction);
        });

        // Initialize tooltips
        Drupal.behaviors.tooltips.attach(context);
      });
    }
  };

})(jQuery, Drupal, once);
