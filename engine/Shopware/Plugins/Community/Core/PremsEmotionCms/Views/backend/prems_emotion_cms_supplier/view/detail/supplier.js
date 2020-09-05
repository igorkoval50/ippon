Ext.define('Shopware.apps.PremsEmotionCmsSupplier.view.detail.Supplier', {
    extend: 'Shopware.grid.Association',
    alias: 'widget.premsemotioncms-supplier-window',
    height: 200,
    title: 'Herstellerseite',

    configure: function() {
        return {
            controller: 'PremsEmotionCmsSupplier',
            columns: {
                name: {}
            }
        };
    }
});