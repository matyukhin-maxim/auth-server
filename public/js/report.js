/**
 * Created by Матюхин_МП on 20.05.2016.
 */
$(function () {

    $('.dpicker').datetimepicker({
        format: 'YYYY-MM-DD',
        ignoreReadonly: true
    });

    $('.tpicker').datetimepicker({
        format: 'LT',
        ignoreReadonly: true
    });

    var dt = moment().format('YYYY-MM-DD');
    $('#bdate, #edate').val(dt);

    $('#scales').submit(function (e) {
        e.preventDefault();

        $(this).ajaxSubmit({
            target: '#report'
        });
    });

    $('[type="submit"]').click();
});