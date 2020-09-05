//{namespace name=backend/newsletter_manager/view/components/numberfield}
//{block name="backend/newsletter_manager/view/components/fields/numberfield"}
/**
 * Extends the default "number" component and sets the minValue to zero
 */
Ext.define('Shopware.apps.NewsletterManager.view.components.fields.NumberField', {
    extend: 'Ext.form.field.Number',
    alias: 'widget.newsletter-components-fields-numberfield',
    name: 'numberfield',

    /**
     * Initiliaze the component.
     *
     * @public
     * @return void
     */
    initComponent: function () {
        var me = this;

        Ext.apply(me, {
            minValue: 1
        });

        me.callParent(arguments);
    }
});
//{/block}