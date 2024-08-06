(($) => {
  Drupal.behaviors.colIconsHeader = {
    attach() {
      $(document).on('ajaxSuccess', function () {
        setTimeout(() => {
          $('.layout-selection .layout-icon.layout-icon--blb-col-2-header').each(function () {
            $(this).replaceWith('<svg width="60" height="80" class="layout-icon layout-icon--blb-col-2-header"><title>Bootstrap 2 Cols w/ Header</title><g><title>square_one</title><rect x="0.5" y="0.5" width="59" height="17" stroke-width="1" class="layout-icon__region layout-icon__region--square-one"></rect></g><g><title>square_two</title><rect x="0.5" y="21.6" width="27.5" height="57" stroke-width="1" class="layout-icon__region layout-icon__region--square-two"></rect></g><g><title>square_three</title><rect x="32" y="21.6" width="27.5" height="57" stroke-width="1" class="layout-icon__region layout-icon__region--square-three"></rect></g></svg>');
          });
          $('.layout-selection .layout-icon.layout-icon--blb-col-3-header').each(function () {
            $(this).replaceWith('<svg width="60" height="80" class="layout-icon layout-icon--blb-col-3-header"><title>Bootstrap 3 Cols w/ Header</title><g><title>square_one</title><rect x="0.5" y="0.5" width="59" height="17" stroke-width="1" class="layout-icon__region layout-icon__region--square-one"></rect></g><g><title>square_two</title><rect x="0.5" y="21.6" width="17" height="57" stroke-width="1" class="layout-icon__region layout-icon__region--square-one"></rect></g><g><title>square_three</title><rect x="21.5" y="21.6" width="17" height="57" stroke-width="1" class="layout-icon__region layout-icon__region--square-two"></rect></g><g><title>square_four</title><rect x="42.5" y="21.6" width="17" height="57" stroke-width="1" class="layout-icon__region layout-icon__region--square-three"></rect></g></svg>');
          });
          $('.layout-selection .layout-icon.layout-icon--blb-col-4-header').each(function () {
            $(this).replaceWith('<svg width="60" height="80" class="layout-icon layout-icon--blb-col-4-header"><title>Bootstrap 4 Cols w/ Header</title><g><title>square_one</title><rect x="0.5" y="0.5" width="59" height="17" stroke-width="1" class="layout-icon__region layout-icon__region--square-one"></rect></g><g><title>square_two</title><rect x="0.5" y="21.6" width="11.75" height="57" stroke-width="1" class="layout-icon__region layout-icon__region--square-one"></rect></g><g><title>square_three</title><rect x="16.25" y="21.6" width="11.75" height="57" stroke-width="1" class="layout-icon__region layout-icon__region--square-two"></rect></g><g><title>square_four</title><rect x="32" y="21.6" width="11.75" height="57" stroke-width="1" class="layout-icon__region layout-icon__region--square-three"></rect></g><g><title>square_five</title><rect x="47.75" y="21.6" width="11.75" height="57" stroke-width="1" class="layout-icon__region layout-icon__region--square-four"></rect></g></svg>');
          });
          $('form[action*="blb_col_2_header"] div[class*="layout-breakpoints"] label.glb-option, form[action*="blb_col_3_header"] div[class*="layout-breakpoints"] label.glb-option, form[action*="blb_col_4_header"] div[class*="layout-breakpoints"] label.glb-option').each(function () {
            var labelWidth = $(this).width();
            $('.blb_breakpoint_col', this).each(function () {
              var content = $(this).text().trim();
              if (content != '100%') {
                var percentage = parseInt(content.replace('%', ''));
                if (percentage == 33) {
                  percentage == 33.3333333333;
                }
                else if (percentage == 17) {
                  percentage == 16.6666666667;
                }
                var modifiedWidth = labelWidth * (percentage / 100);
                $(this).css('width', (modifiedWidth - 10));
              }
            });
          });
        }, 10);
      });
    },
  };
})(jQuery);
