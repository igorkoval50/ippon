;(function ($, window) {
    $(".fancybox").fancybox({
        thumbs : {
            autoStart : true,
            axis      : 'x'
        },
    });
    $('.fancybox').on('touchend', (event) => {
        $.fancybox.open($(".fancybox"),{
            autoSize: true,
            thumbs : {
                autoStart : true,
                axis      : 'x'
            }
        });
    });
    $('.zoom').zoom({
        magnify: 2
    });
})(jQuery, window);



