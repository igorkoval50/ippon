Ext.define('Shopware.apps.PremsEmotionCmsSupplier.view.list.Base', {
    extend: 'Shopware.grid.Panel',
    alias:  'widget.premsemotioncms-listing-grid',
    region: 'center',

    configure: function() {
        return {
            detailWindow: 'Shopware.apps.PremsEmotionCmsSupplier.view.detail.Window',
            columns: {
                name: {
                    header: 'Name',
                    flex: 3
                }
            }
        };
    }
});
