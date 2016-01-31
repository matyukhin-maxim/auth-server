/**
 * Created by fellix on 31.01.16.
 */

$(function () {

    $('button').click(function (e) {
        e.preventDefault();
        var btn = $(e.target);
        $.ajax({
            type: 'POST',
            url : '/sites/happy/',
            data: {
                action: btn.hasClass('btn-primary'),
                text: $('#text').val()
            },
            success: function(data) {
                $('#encode').html(data);
            }
        });
    });
});