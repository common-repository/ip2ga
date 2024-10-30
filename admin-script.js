jQuery(document).ready(function($) {
    // Check if the elements exist before adding event listeners
    if ($('#ip2c_token').length > 0 && $('#rapidapi_token').length > 0) {

        // Disable the second field when the first is filled
        $('#ip2c_token').on('input', function() {
            if ($(this).val().length > 0) {
                $('#rapidapi_token').attr('disabled', 'disabled').css('background-color', '#f0f0f0');
            } else {
                $('#rapidapi_token').removeAttr('disabled').css('background-color', '');
            }
        });

        // Disable the first field when the second is filled
        $('#rapidapi_token').on('input', function() {
            if ($(this).val().length > 0) {
                $('#ip2c_token').attr('disabled', 'disabled').css('background-color', '#f0f0f0');
            } else {
                $('#ip2c_token').removeAttr('disabled').css('background-color', '');
            }
        });

        // Initial check on page load in case one of the fields is already filled
        if ($('#ip2c_token').val().length > 0) {
            $('#rapidapi_token').attr('disabled', 'disabled').css('background-color', '#f0f0f0');
        }

        if ($('#rapidapi_token').val().length > 0) {
            $('#ip2c_token').attr('disabled', 'disabled').css('background-color', '#f0f0f0');
        }
    }

    // Initial nav-tab
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.tab-content').hide();
        $($(this).attr('href')).show();
    });

    // Show Consent Settings only when GA4 Tracking Code is enabled
    $('#enable_ga_tracking').on('change', function() {
        if ($(this).is(':checked')) {
            $('a[href="#consent-settings"]').show();
        } else {
            $('a[href="#consent-settings"]').hide();
            $('#consent-settings').hide();
        }
     }).trigger('change'); // Trigger change on page load to ensure correct state

});
