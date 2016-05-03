/**
 * Created by Матюхин_МП on 04.02.2016.
 */

$(function () {

    // Привязка событий и свойств к котролам страницы (в том числе подгруженным динамически)
    function refreshControls() {

        // событие клика по "кресту" у юзера
        $('.deluser').off('click').click(function(e) {
            e.preventDefault();
            var self= $(this);
            var userid = self.data('user');

            $.ajax({
                type: 'post',
                url : '/admin/accessoryUser/',
                data: {user: userid, group: $('#gid').val(), remove: 1},
                success: function(data) {
                    $('#selection').focus();
                    if (data.length) {
                        $('button[data-user="' + userid + '"]').removeProp('disabled');
                        self.closest('li').remove();

                        refreshControls();
                    }
                }
            });
        });

        // Счетчик пользователей в группе
        $('#cnt-user').text($('.group-user').length);
    }

    var mt = 0;
    $('#selection').on('change', function () {
        if ($(this).val().length == 0) return;
        clearTimeout(mt);
        tmr = setTimeout(function () {
            $.ajax({
                type : 'POST',
                url  : '/admin/selection/',
                data : {q: $('#selection').val()},
                success: function(data) {
                    $('#select-response').html(data);

                    // заблокируем кнопки в подборе, пользователи которые уже привязанны к группе
                    $('.deluser').each(function () {
                        var userid = $(this).data('user');
                        $('button[data-user="' + userid + '"]').prop('disabled', true);
                    });
                    $('.btn-select').click(function() {
                        var btn = $(this);
                        $.ajax({
                            type: 'post',
                            url : '/admin/accessoryUser/',
                            data: {user: $(this).data('user'), group: $('#gid').val()},
                            success: function(data) {
                                btn.prop('disabled', true);
                                $('#group-users').append(data);
                                $('#selection').focus();
                                refreshControls();
                            }
                        });
                    });
                    refreshControls();
                }
            });
        }, 1000);
    });

    $('.site-link').click(function () {
        $(this).toggleClass('btn-success');
    });

    $('#access-save').click(function () {

        var list = $.map($('.site-link.btn-success'), function(link) {return $(link).data('key')});
        $.ajax({
            type: 'post',
            url : '/admin/groupAccess/',
            data: {sites: list, group: $('#gid').val()},
            success: function(data) {$('#response').html(data)}
        });
    });

    refreshControls();
});