;(function ($) {
    'use strict';

    function loadLazy()
    {
        new LazyLoad({
            elements_selector: ".lazyLoad"
        });
    }
    loadLazy();

    $.subscribe('plugin/swInfiniteScrolling/onFetchNewPageFinished', function () {
        loadLazy();
    });
    $.subscribe('plugin/swInfiniteScrolling/onLoadPreviousFinished', function () {
        loadLazy();
    });

    $.subscribe('plugin/swEmotion/onInitElements', function () {
        loadLazy();
    });

    $.subscribe('plugin/swProductSlider/onLoadItemsSuccess', function () {
        loadLazy();
    });

    $.subscribe('plugin/swImageSlider/onRegisterEvents', function () {
        loadLazy();
    });

    $.subscribe('plugin/swLastSeenProducts/onCreateProductList', function () {
        loadLazy();
    });

    $.subscribe('plugin/swListingActions/updateListing', function () {
        loadLazy();
    });

})(jQuery);
