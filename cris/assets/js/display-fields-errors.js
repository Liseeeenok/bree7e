
/**
 * Listen error field message and display error message
 */
$(window).on('ajaxInvalidField', function (event, field, fieldName, fieldMessages, isFirst) {

    if (isFirst) {
        $('.field-has-error').remove();
    }

    $('<div class="field-has-error text-danger">' + fieldMessages[0] + '</div>').insertBefore($(field))
});

/**
 * Remove error messages on Success
 */
$(window).on('ajaxBeforeUpdate', function (event, context, data, textStatus, jqXHR) {

    // Check handler to avoid to clear message when other Ajax request occur (e.g.: recordFinder)
    if (typeof context.handler !== 'undefined' && context.handler === 'onSave') {
        $('.field-has-error').remove();
    }
});