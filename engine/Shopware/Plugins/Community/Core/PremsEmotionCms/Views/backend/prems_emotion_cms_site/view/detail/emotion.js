Ext.define('Shopware.apps.PremsEmotionCmsSite.view.detail.Emotion', {
    extend: 'Shopware.grid.Association',
    alias: 'widget.premsemotioncms-emotion-window',
    height: 200,
    title: 'Einkaufswelt',

    configure: function() {
        return {
            controller: 'PremsEmotionCmsSite',
            columns: {
                name: {}
            }
        };
    }
});