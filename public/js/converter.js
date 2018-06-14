$(document).ready(function ($) {
    $('#currency_source').on('change', function () {
        var convertSource = $('#currency_source').val();
        if (isNaN(convertSource)) {
            $('#currency_target').val('source currency is not a number. Try again');
            $('#convert_action').attr('disabled', 'disabled');
        } else {
            $('#currency_target').val('');
            $('#convert_action').removeAttr('disabled');
        }
    });


    $('#convert_action').click(function (e) {
        e.preventDefault();
        var convertSource = $('#currency_source').val();
        if (isNaN(convertSource)) {
            $('#currency_target').val('source currency is not a number. Try again');
            return;
        }

        $.ajax('/application/convert', {
            'method': 'POST',
            'dataType': 'json',
            'data': {
                'source': convertSource
            },
            beforeSend: function () {
                $('#currency_target').val('converting...');
            },
            complete: function (a) {
                if (a.responseJSON.success) {
                    $('#currency_target').val(a.responseJSON.target);
                } else {
                    $('#currency_target').val('error occured');
                }
            },
            error: function (r) {
                $('#currency_target').val('error occured');
            }
        });
    });
});