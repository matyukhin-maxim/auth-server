/**
 * Created by fellix on 28.01.16.
 */

$(function () {
    var ua = navigator.userAgent;
    var rx = new RegExp('MSIE [0-9]');
    if (!rx.test(ua)) {
        $('label').hide();
    }

    $('#user-field').autocomplete({
        delay: 1500,
        minLength: 3,
        source: function (request, response) {
            $.post('/login/complete/', {q: request.term},
            function(data) {
                //$('#response').html(data);
                response($.map(data, function(item) {
                    return {
                        id: 0,
                        label: item.fullname,
                        value: item.tabnumber
                    }
                }));
            }, 'json');
        },
        select: function (ev, ui) {
            $('#userid').val(ui.item.data);
            console.info(ui);
        }
    });
});