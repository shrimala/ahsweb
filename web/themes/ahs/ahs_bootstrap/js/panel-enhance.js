(function ($, Drupal) {
  Drupal.behaviors.ahsPanelEnhance = {
    attach: function (context, settings) {
      // Enhance each initially collapsed panel
      $(context).find('.panel').once('ahsPanelEnhance').each(function () {
        console.log('active');
        // Add class on panel container marking initial collapsed state
        $(this).addClass('collapsed');

        // Listen for the panel being collapsed
        $(this).on('hidden.bs.collapse', function (e) {
          // Toggle class on panel container marking collapsed state
          $(this).addClass('collapsed');
        });

          // Listen for the panel being expanded
        $(this).on('shown.bs.collapse', function (e) {
          // Toggle class on panel container marking collapsed state
          $(this).removeClass('collapsed');

          // When expanded, check visibility every 5 seconds
          var intervalId = setInterval( function() {
            // If all of the panel is no longer in viewport
            if (!($(e.currentTarget).visible( true, true ))) {
              // Collapse the panel
              $(e.currentTarget).find('.panel-collapse').collapse('hide');
              // Stop further checking
              clearInterval(intervalId);
            }
          }, 5000 );
        });
      });
    }
  };
})(jQuery, Drupal);