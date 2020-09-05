//{block name="backend/mag_newsletter_box/model/list"}
Ext.define('Shopware.apps.MagNewsletterBox.model.List', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'MagNewsletterBox'
        };
    },

    fields: [
        { name : 'id', type: 'int', useNull: true },
        { name : 'email', type: 'string' },
        { name : 'code', type: 'string' },
    ],
});
//{/block}