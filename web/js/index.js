(function ($) {
    var timeout;

    $('#books-filter').change(function () {
        var $form = $(this);

        if (timeout) {
            clearTimeout(timeout);
        }

        timeout = setTimeout(function () {
            $.pjax.reload('#books-pjax', {
                data: $form.serializeArray(),
                push: false,
                replace: false
            });
        }, 5000);
    });
})(jQuery);