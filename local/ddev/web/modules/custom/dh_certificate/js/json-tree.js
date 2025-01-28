(function ($, Drupal) {
  Drupal.behaviors.jsonTree = {
    attach: function (context, settings) {
      $('.json-tree', context).once('jsonTree').each(function () {
        $(this).find('details').each(function () {
          var $details = $(this);
          var $summary = $details.children('summary');
          var $content = $details.children('ul');

          $summary.on('click', function () {
            $details.toggleClass('open');
            if ($details.hasClass('open')) {
              $content.show();
            } else {
              $content.hide();
            }
          });

          // Initially hide the content
          $content.hide();
        });
      });
    }
  };
})(jQuery, Drupal);
