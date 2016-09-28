(function ($, Drupal) {
    Drupal.behaviors.erEnhancedHideUi = {
        attach: function (context, settings) {
            // Apply the ahsEditClick effect to the elements only once.
            console.log("attachong");
            $(context).find('.er-enhanced-hideui-requested').once('erEnhancedHideShow').each(function () {
                $(this).addClass('er-enhanced-hideui');
                //$(this).find('a.er-enhanced-edit').show();
                $(this).find('a.er-enhanced-edit').on('click',function(event){
                    // Stop the link clicking from moving the page
                    event.preventDefault();
                    $(this).closest('.er-enhanced-previewing').removeClass('er-enhanced-hideui');
                    $(this).hide();
                });
            });
        }
    };
})(jQuery, Drupal);