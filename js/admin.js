/** admin scripts** */
jQuery(document).ready(function ($) {
    $(document).on('click', '.del-button', function (event) {
        event.preventDefault();
        $.post(
            AjaxParams.adminAjaxUrl,
            {
                action: 'eliminardatos',
                id: $(this).attr('data-id'),
                nonce: AjaxParams.nonce,
            },

            location.reload(),
        );

        return false;
    });


});  
