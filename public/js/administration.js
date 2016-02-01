/**
 * Created by Матюхин_МП on 01.02.2016.
 */
$(function () {

    function showusers() {
        $.ajax({
            type : 'POST',
            url  : '/admin/filter/',
            data : {q: $('#filter').val()},
            success: function(data) {
                $('.panel-response').html(data);
                showPopup();
            }
        });
    }

    var tmr = undefined;
    $('#filter').on('keyup change', function () {
        clearTimeout(tmr);
        tmr = setTimeout(function () {
            showusers();
            showPopup();
        }, 500);
    });
});