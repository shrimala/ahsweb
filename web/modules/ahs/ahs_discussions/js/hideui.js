(function ($, Drupal) {
    Drupal.behaviors.ahsPreviewHideUi = {
        attach: function (context, settings) {
            // Apply the ahsEditClick effect to the elements only once.
            $(context).find('.ahs-preview-hideui-requested').once('ahsPreviewHideShow').each(function () {
                $(this).addClass("ahs-preview-hideui");
                if($(this).hasClass("ahs-preview-hide-move-requested")){
                    $(this).addClass("ahs-preview-hide-move");
                }
                if($(this).hasClass("ahs-preview-hide-remove-requested")){
                    $(this).addClass("ahs-preview-hide-remove");
                }
                if($(this).hasClass("ahs-preview-hide-new-requested")){
                    $(this).addClass("ahs-preview-hide-new");
                }
                $editLink = '<a class="btn btn-sm btn-default ahs-preview-edit" href="#"><span class="glyphicon glyphicon-pencil"></span></a>';
                //$(this).find('th.field-label').append($editLink);
                $(this).append($editLink);
                $(this).find('a.ahs-preview-edit').on('click',function(event){
                    // Stop the link clicking from moving the page
                    event.preventDefault();
                    $(this).closest('.ahs-preview-hideui').removeClass('ahs-preview-hideui');
                    $(this).closest('.ahs-preview-hide-move').removeClass('ahs-preview-hide-move');
                    $(this).closest('.ahs-preview-hide-remove').removeClass('ahs-preview-hide-remove');
                    $(this).closest('.ahs-preview-hide-new').removeClass('ahs-preview-hide-new');
                    //$(this).removeClass('ahs-preview-hideui');
                });
            });
        }
    };
})(jQuery, Drupal);