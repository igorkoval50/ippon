/**
 * global: Ext
 */
//{namespace name="plugins/neti_foundation/backend/send_mail"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.sendMail.store.Mail', {
    'extend': 'Ext.data.Store',
    'alias': 'widget.neti_foundation_extensions_send_mail_store_mail',
    'batch': true,
    'clearOnLoad': false,
    'model': 'Shopware.apps.NetiFoundationExtensions.sendMail.model.Mail'
});
//{/block}
