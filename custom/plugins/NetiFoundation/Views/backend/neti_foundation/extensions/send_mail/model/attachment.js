/**
 * global: Ext, Shopware
 */
//{namespace name="plugins/neti_foundation/backend/send_mail"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.sendMail.model.Attachment', {
    'extend':'Ext.data.Model',

    fields : [
        { name: 'id',          type: 'int' },
        { name: 'filename',    type: 'string' },
        { name: 'size',        type: 'string' },
        { name: 'shopId',      type: 'int' }
    ],

    proxy: {
        type: 'ajax',

        /**
         * Configure the url mapping for the different
         * store operations based on
         * @object
         */
        api: {
            read: '{url controller="mail" action="getAttachments"}',
            create: '{url controller="mail" action="addAttachment"}',
            update: '{url controller="mail" action="updateAttachment"}',
            destroy: '{url controller="mail" action="removeAttachment"}'
        },

        /**
         * Configure the data reader
         * @object
         */
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
//{/block}
