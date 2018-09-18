jQuery(function($) {
    $.noConflict();

    $(document).ready(function() {
        // Setting non functional filters to disabled
        $("#filter_access").attr('disabled', true).trigger("liszt:updated");
        $("#filter_language").attr('disabled', true).trigger("liszt:updated");
    });
});
