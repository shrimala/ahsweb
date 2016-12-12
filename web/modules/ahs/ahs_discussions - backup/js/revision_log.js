(function ($, Drupal) {
    Drupal.behaviors.ahsRevisionLog = {
        attach: function (context, settings) {
            // Apply the erEnhancedRemove effect to the elements only once.
            $(context).find('textarea[name="body[0][value]"]').once('ahsRevisionLogShow').each(function () {
                console.log('Textarea has been found'); // Fires on page load
                $(this).on('input selectionchange propertychange',function(event){
                    console.log('On change event fired'); // Never fires
                    if ($this.data('editor-value-is-changed','true')){
                        console.log('Has been changed'); // Never fires
                    }
                });
            });
        }
    };
})(jQuery, Drupal);