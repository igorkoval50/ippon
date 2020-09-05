//{namespace name=backend/stutt_seo_redirects/view/list}

Ext.define('Shopware.apps.StuttSeoRedirects.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.stutt-seo-redirects-list-window',
    height: 450,
    title : '{s name=window_title}SEO-Weiterleitungen (301 und 302 Redirects){/s}',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.StuttSeoRedirects.view.list.Redirect',
            listingStore: 'Shopware.apps.StuttSeoRedirects.store.Redirect'
        };
    }
});