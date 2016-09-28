(function ($, Drupal) {
    Drupal.behaviors.erEnhanced = {
        attach: function (context, settings) {
            $(context).find('a.er-enhanced-remove').once('erEnhancedRemove').each(function () {
                // Apply the erEnhancedRemove effect to the elements only once.
                $(this).css('color', 'green');
                $(this).click(function(event){
                    // Stop the link clicking from moving the page
                    event.preventDefault();
                    // Set the autocomplete reference field to nothing
                    $(this).parent().first().find('input').val('');
                    // Hide the whole reference row
                    $(this).closest('tr.draggable').hide();
                });
            });
        }
    };
})(jQuery, Drupal);