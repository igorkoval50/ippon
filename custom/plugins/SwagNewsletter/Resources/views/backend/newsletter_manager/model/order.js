//{namespace name="backend/swag_newsletter/main"}
//{block name="backend/newsletter_manager/model/order"}
Ext.define('Shopware.apps.NewsletterManager.model.Order', {
    /**
     * Extends the standard Ext Model
     * @string
     */
    extend: 'Ext.data.Model',

    /**
     * The fields used for this model
     * @array
     */
    fields: [
        //{block name="backend/order/model/order/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'customerId', type: 'int' },
        { name: 'newsletterId', type: 'int' },
        { name: 'customer', type: 'string' },
        { name: 'status', type: 'int' },
        { name: 'cleared', type: 'int' },
        { name: 'partnerId', type: 'int' },
        { name: 'subject', type: 'string' },
        { name: 'currency', type: 'string' },
        { name: 'currencyFactor', type: 'float' },
        { name: 'shopId', type: 'int' },
        { name: 'invoiceAmount', type: 'float' },
        { name: 'orderTime', type: 'date', dateFormat: 'Y-m-d H:i:s' },
        { name: 'newsletterDate', type: 'date' },
        {
            name: 'invoiceAmountEuro',
            type: 'float',
            convert: function (value, record) {
                var factor = record.get('currencyFactor');
                if (!Ext.isNumeric(factor)) {
                    factor = 1;
                }
                return Ext.util.Format.round(record.get('invoiceAmount') / factor, 2);
            }
        },
        {
            name: 'grouping',
            type: 'string',
            convert: function (value, record) {
                if (record) {
                    var subject = record.get('subject');
                    if (!subject) {
                        subject = "{s name='newsletterNotFound'}Unknown Newsletter{/s}";
                        return Ext.String.format('<i>[0]</i> - (ID: [1])', subject, record.get('partnerId'));
                    }

                    return Ext.String.format('[2] - &laquo;<i>[0]</i>&raquo; - (ID: [1])', subject, record.get('partnerId'), Ext.util.Format.date(record.get('newsletterDate')));
                }
            }
        }
    ]
});
//{/block}
