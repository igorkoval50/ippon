//{block name="backend/newsletter_manager/model/voucher"}
Ext.define('Shopware.apps.NewsletterManager.model.Voucher', {
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
        //{block name="backend/canceled_order/model/voucher/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'value', type: 'string' },
        {
            name: 'description',
            type: 'string',
            convert: function (value, record) {
                if (value == null) {
                    return value;
                }

                if (record && record.get('id') == -1) {
                    return value;
                }
                return Ext.String.format("{s name=voucherDescription}{literal}{0} ({1}{3}, {2} total){/literal}{/s}", value, record.get('value'), record.get('numberofunits'), record.get('type_sign'));

            }
        },
        { name: 'numberofunits', type: 'int' },
        { name: 'type_sign', type: 'string' }
    ]
});
//{/block}
