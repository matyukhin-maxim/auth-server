/**
 * Created by Матюхин_МП on 05.05.2016.
 */

var access  = 0,
    deny    = 0;

$(function () {

    function calcAccess() {
        access = deny = 0;

        $('.group.active').map(function (id, g) {access |= parseInt($(g).data('access'))});
        $('.site.block').map(function (id, g) {deny |= 1 << parseInt($(g).data('key'))});

        $('.site').each(function (id, item) {
            var btn = $(item);

            btn.removeClass('btn-default btn-success btn-danger').blur();

            if (btn.hasClass('block')) btn.addClass('btn-danger');
            else if (1 << btn.data('key') & access) btn.addClass('btn-success');
            else btn.addClass('btn-default');
        });
    }

    $('.group').click(function(e) {
        e.preventDefault();

        $(this).toggleClass('active');
        calcAccess();
    });

    $('.site').click(function (e) {
        e.preventDefault();

        $(this).toggleClass('block');
        calcAccess();
    });

    calcAccess();
});