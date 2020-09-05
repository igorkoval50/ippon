;(function($) {
    
    $(function() {

        var touch = ("ontouchstart" in document.documentElement);

        if(!touch){
            $(".listing--content").on('mouseenter', '.product--image .image--element', function(){

                var me = $(this),
                    meImg = me.find('.image--media img'),
                    hoverSrc = me.find('.tab10--listing--hover-image img');

                //console.log(me);
                if ( hoverSrc.length ){

                    if ( hoverSrc.hasClass('lh-load')) {
                        // hoverSrc.attr('srcset', hoverSrc.attr('data-srcset'));

                        if ($('html').hasClass('is--ie')){
                            hoverSrc.attr('src', hoverSrc.attr('data-srcset'));
                        } else{
                            hoverSrc.attr('srcset', hoverSrc.attr('data-srcset'));
                        }
                    }

                    meImg.addClass('tt-img-hover-hide');
                    hoverSrc.addClass('tt-img-hover-active');
                }
            });

            $(".listing--content").on('mouseleave', '.product--image .image--element', function(){

                var me = $(this),
                    meImg = me.find('.image--media img'),
                    hoverSrc = me.find('.tab10--listing--hover-image img');

                //console.log(me);
                if ( hoverSrc.length ){
                    meImg.removeClass('tt-img-hover-hide');
                    hoverSrc.removeClass('tt-img-hover-active');
                }

            });
        }

    });
})(jQuery);
