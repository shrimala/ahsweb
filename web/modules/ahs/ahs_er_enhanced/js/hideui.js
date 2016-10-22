(function ($, Drupal) {
    Drupal.behaviors.erEnhancedHideUi = {
        attach: function (context, settings) {
            // Apply the ahsEditClick effect to the elements only once.
            $(context).find('.er-enhanced-hideui-requested').once('erEnhancedHideShow').each(function () {
                $(this).addClass('er-enhanced-hideui');
                $editLink = '  <span class="er-enhanced-edit er-enhanced-hideui">(<a href="">edit</a>)</span>';
                $(this).find('th.field-label').append($editLink);
                //$(this).find('a.er-enhanced-edit').addClass('er-enhanced-hideui');
                $(this).find('span.er-enhanced-edit').on('click',function(event){
                    // Stop the link clicking from moving the page
                    event.preventDefault();
                    $(this).closest('.er-enhanced-previewing').removeClass('er-enhanced-hideui');
                    $(this).removeClass('er-enhanced-hideui');
                });
            });
        }
    };
})(jQuery, Drupal);