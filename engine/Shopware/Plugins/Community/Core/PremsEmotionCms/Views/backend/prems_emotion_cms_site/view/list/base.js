Ext.define('Shopware.apps.PremsEmotionCmsSite.view.list.Base', {
    extend: 'Shopware.grid.Panel',
    alias:  'widget.premsemotioncms-listing-grid',
    region: 'center',

    configure: function() {
        return {
            detailWindow: 'Shopware.apps.PremsEmotionCmsSite.view.detail.Window',
            columns: {
                name: {
                    header: 'Name',
                    flex: 3
                }
            }
        };
    }
});
