Ext.define('Shopware.apps.PremsEmotionCmsSite.view.detail.Site', {
    extend: 'Shopware.grid.Association',
    alias: 'widget.premsemotioncms-site-window',
    height: 200,
    title: 'Inhaltsseite',

    configure: function() {
        return {
            controller: 'PremsEmotionCmsSite',
            columns: {
                name: {}
            }
        };
    }
});