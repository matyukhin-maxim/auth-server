/**
 * Created by fellix on 28.01.16.
 */

$(function () {
    var ua = navigator.userAgent;
    var rx = new RegExp('MSIE [0-9]');
    if (!rx.test(ua)) $('.control-label').hide();

    $('#user-field').autocomplete({
        delay: 500,
        minLength: 3,
        autoFocus: true,
        source: function (request, response) {
            $.post('/login/complete/', {q: request.term},
            function(data) {
                response(data);
            }, 'json');
        },
        select: function (ev, ui) {
            $(this).val(ui.item.label);
            $('input[type="password"]').focus();
            $('#user-id').val(ui.item.value);
            return false;
        },
        focus: function() {
            return false;
        },
        response: function (ev, ui) {
            $(this).parent().toggleClass('has-error', ui.content.length === 0);
            $('#user-id').val('');

            if (ui.content.length === 0) {
                //showPopup('Пользователь не найден. Проверьте правильность ввода');
                $(this).val('');
            }
            // автозавершение ввода, если в списке "живого поиска" остался только один вариант
            if (ui.content.length === 1) {
                ui.item = ui.content[0];
                $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                $(this).autocomplete('close');
            }
        }
    }).autocomplete( "instance" )._renderItem = function( ul, item ) {
        return $( "<li>")
            .append("<a>" + item.label + "</a>")
            .append( $('<em/>').addClass('pull-right text-muted').html(item.value) )
            .appendTo( ul );
    };

    $('[type="password"]').keydown(function (e) {
        if (e.which == 13) $('#btn-login').trigger('click');
    });

    $('#btn-login').click(function (e) {
        e.preventDefault();
        if ($('#user-id').val().length === 0) {
            showPopup('Пользователь не указан.');
            $('#login-form').trigger('reset');
            return false;
        }

        $.ajax({
            url: '/login/check/',
            type: 'post',
            data: $('#login-form').serialize(),
            success: function(data) {
                if (data.length) window.location = '/sites/';
                $('input[type="password"]').focus().val('');
            }
        });
    });
});