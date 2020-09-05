;(function ($, Modernizr, window, Math) {
    $.overridePlugin('swImageSlider', {
        // defaults: {
        //     // dotNavigation: false,
        //     // arrowControls: false
        // },
        getThumbnailOrientation: function () {
            return 'horizontal';
        }
    });
})(jQuery, Modernizr, window, Math);
