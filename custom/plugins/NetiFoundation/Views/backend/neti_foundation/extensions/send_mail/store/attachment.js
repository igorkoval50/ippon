/**
 * global: Ext
 */
//{namespace name="plugins/neti_foundation/backend/send_mail"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.sendMail.store.Attachment', {
    'extend': 'Ext.data.TreeStore',
    'alias': 'widget.neti_foundation_extensions_send_mail_store_attachment',
    'batch': true,
    'clearOnLoad': false,
    'model': 'Shopware.apps.NetiFoundationExtensions.sendMail.model.Attachment'
});
//{/block}