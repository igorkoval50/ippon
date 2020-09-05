;(function ($) {

    /**
     * Shopware Last Seen Products Plugin
     *
     * This plugin creates a list of collected articles.
     * Those articles will be collected, when the user opens a detail page.
     * The created list will be showed as a product slider.
     */
    $.overridePlugin('swLastSeenProducts', {
        /**
         * Creates a product image with all media queries for the
         * picturefill plugin
         *
         * @public
         * @method createProductImage
         * @param {Object} data
         */
        createProductImage: function (data) {
            var me = this,
                image = data.images[0],
                element,
                imageEl,
                imageMedia,
                srcSet;

            element = $('<a>', {
                'class': me.opts.imageCls,
                'href': data.linkDetailsRewritten,
                'title': data.articleName
            });

            imageEl = $('<span>', {'class': 'image--element'}).appendTo(element);
            imageMedia = $('<span>', {'class': 'image--media'}).appendTo(imageEl);

            if (image) {
                srcSet = image.sourceSet;
            } else {
                srcSet = me.opts.noPicture;
            }

            $('<img>', {
                'class': "lazyLoad",
                'data-srcset': srcSet,
                'alt': data.articleName,
                'title': data.articleName
            }).appendTo(imageMedia);

            $.publish('plugin/swLastSeenProducts/onCreateProductImage', [me, element, data]);

            return element;
        },
    });
}(jQuery));
