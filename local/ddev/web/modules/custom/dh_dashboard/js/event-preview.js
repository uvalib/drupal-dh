(function ($, Drupal) {
  'use strict';

  // Immediate check if script loads
  console.log('Event preview script loaded');
  
  function formatDateTime(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    });
  }

  Drupal.behaviors.eventPreview = {
    attach: function (context, settings) {
      // Debug markers
      $('.js-event-preview-debug').attr('data-script-loaded', 'true');
      console.log('Event preview behavior attached', context);
      
      once('event-preview', '.event-preview-trigger', context).forEach(function (element) {
        const $trigger = $(element);
        console.log('Found trigger element:', $trigger.data());
        
        const $popup = $('#event-preview-popup').length ? 
          $('#event-preview-popup') : 
          $('<div id="event-preview-popup" class="event-preview-popup"></div>').appendTo('body');

        // Click handler for showing/hiding preview
        $trigger.on('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          // If clicking the same trigger that's currently active, close the popup
          if ($popup.hasClass('active') && $popup.data('current-trigger') === this) {
            $popup.removeClass('active');
            return;
          }

          // Store the current trigger and show popup
          $popup.data('current-trigger', this);

          const data = {
            title: $trigger.data('title'),
            date: $trigger.data('date'),
            location: $trigger.data('location'),
            type: $trigger.data('type'),
            department: $trigger.data('department'),
            summary: $trigger.data('summary'),
            image: $trigger.data('image'),
            moreInfo: $trigger.data('more-info'),
            meeting: $trigger.data('meeting')
          };

          console.log('Preview data:', data);

          let content = `
            <div class="preview-border"></div>
            <div class="preview-arrow"></div>
            <div class="preview-content">
              ${data.image ? `
                <div class="preview-image">
                  <img src="${data.image}" alt="${data.title}">
                </div>` : ''}
              <div class="preview-details">
                <h3>${data.title}</h3>
                ${data.date ? `<p class="preview-date"><i class="fas fa-calendar"></i> ${formatDateTime(data.date)}</p>` : ''}
                ${data.location ? `<p class="preview-location"><i class="fas fa-map-marker-alt"></i> ${data.location}</p>` : ''}
                ${data.type ? `<p class="preview-type"><i class="fas fa-tag"></i> ${data.type}</p>` : ''}
                ${data.department ? `<p class="preview-department"><i class="fas fa-university"></i> ${data.department}</p>` : ''}
                ${data.summary ? `<div class="preview-summary">${data.summary}</div>` : ''}
              </div>
              <div class="preview-links">
                <a href="${$trigger.attr('href')}" class="preview-view">View Details</a>
                ${data.meeting ? `<a href="${data.meeting}" class="preview-join">Join Meeting</a>` : ''}
              </div>
            </div>`;

          const triggerOffset = $trigger.offset();
          const windowWidth = $(window).width();
          const windowHeight = $(window).height();
          const scrollTop = $(window).scrollTop();
          
          // Calculate popup dimensions
          const popupWidth = 400;
          const popupHeight = 300; // Approximate height
          
          // Default to right position
          let position = 'right';
          let leftPosition = triggerOffset.left + $trigger.outerWidth() + 15;
          let topPosition = Math.min(
            Math.max(0, triggerOffset.top - (popupHeight/2) + ($trigger.outerHeight()/2)),
            windowHeight - popupHeight + scrollTop
          );
          
          // If popup would go off right edge, position to left
          if (leftPosition + popupWidth > windowWidth) {
            position = 'left';
            leftPosition = triggerOffset.left - popupWidth - 15;
          }
          
          // If would go off left edge, default to bottom
          if (position === 'left' && leftPosition < 0) {
            position = 'bottom';
            leftPosition = Math.min(
              Math.max(0, triggerOffset.left - (popupWidth/2) + ($trigger.outerWidth()/2)),
              windowWidth - popupWidth
            );
            topPosition = triggerOffset.top + $trigger.outerHeight() + 15;
          }

          // Close any other open popups first
          $('.event-preview-popup.active').removeClass('active');
          
          $popup
            .html(content)
            .removeClass('position-top position-bottom position-left position-right')
            .addClass(`position-${position}`)
            .css({
              top: topPosition,
              left: leftPosition
            })
            .addClass('active')
            .data('current-trigger', this);
        });
      });

      // Improve click-outside handling
      $(document).off('click.eventPreview').on('click.eventPreview', function(e) {
        const $popup = $('#event-preview-popup');
        if ($popup.length && 
            $popup.hasClass('active') && 
            !$(e.target).closest('.event-preview-popup').length && 
            !$(e.target).closest('.event-preview-trigger').length) {
          $popup.removeClass('active');
        }
      });
    }
  };
})(jQuery, Drupal);
