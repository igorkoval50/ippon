Ext.define('Shopware.apps.PremsEmotionCmsArticle2.store.Base', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'PremsEmotionCmsArticle2'
        };
    },
    model: 'Shopware.apps.PremsEmotionCmsArticle2.model.Base'
});