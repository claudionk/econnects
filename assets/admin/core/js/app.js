jQuery(function($){

    var btn = $('.btn-fixed');
    var dv = $('.text-page');

    $(window).scroll(function() {
        if ($(window).scrollTop() > 50) {
            btn.addClass('fixed-top');
            dv.addClass('text-sub-fixed');
        } else {
            btn.removeClass('fixed-top');
            dv.removeClass('text-sub-fixed');
        }
    });

});