/**
 * global: Ext, Shopware
 */
//{namespace name="plugins/neti_foundation/backend/send_mail"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.sendMail.model.Mail', {
    'extend':'Ext.data.Model',

    fields : [
        { name: 'id',          type: 'int' },
        { name: 'name',        type: 'string' },
        { name: 'fromName',    type: 'string' },
        { name: 'fromMail',    type: 'email' },
        { name: 'subject',     type: 'string' },
        { name: 'content',     type: 'string' },
        { name: 'contentHtml', type: 'string' },
        { name: 'isHtml',      type: 'boolean' },
        { name: 'attachment',  type: 'string' },
        { name: 'type',        type: 'string' },
        { name: 'context' },
        { name: 'contextPath' }
    ],

    proxy: {
        type: 'ajax',

        /**
         * Configure the url mapping for the different
         * store operations based on
         * @object
         */
        api: {
            read: '{url controller="mail" action="getMails"}',
            create: '{url controller="mail" action="createMail"}',
            update: '{url controller="mail" action="updateMail"}',
            destroy: '{url controller="mail" action="removeMail"}'
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
