//{namespace name=backend/swag_newsletter/main}
//{block name="backend/newsletter_manager/view/components/fields/link_field"}
Ext.define('Shopware.apps.NewsletterManager.view.components.fields.LinkField', {
    extend: 'Ext.container.Container',
    alias: 'widget.newsletter-components-fields-link-field',
    layout: 'hbox',
    height: 50,

    /**
     * @public
     * @return void
     */
    initComponent: function() {
        var me = this;

        me.items = me.createItems();

        me.callParent(arguments);
    },

    /**
     * @return { Array }
     */
    createItems: function() {
        var me = this;

        return [
            me.createTextField(),
            me.createButton()
        ];
    },

    /**
     * @return { Ext.button.Button }
     */
    createButton: function() {
        var me = this;

        return Ext.create('Ext.button.Button', {
            text: '{s name=select_link}{/s}',
            cls: 'secondary',
            listeners: {
                click: me.onAddLink,
                scope: me
            }
        });
    },

    /**
     * @return { Ext.form.field.Text }
     */
    createTextField: function() {
        var me = this;

        me.textField = Ext.create('Ext.form.field.Text', {
            fieldLabel: '{s name=select_link}{/s}',
            labelWidth: 155,
            flex: 1,
            listeners: {
                specialkey: function(field, e) {
                    if (e.getKey() === e.ENTER) {
                        me.onAddLink();
                    }
                }
            }
        });

        return me.textField;
    },

    /**
     * call the original method of the link object
     */
    onAddLink: function() {
        var me = this;

        me.parent.onAddLinkToGrid(me);
    },

    /**
     * @param { string } value
     */
    setValue: function(value) {
        this.textField.setValue(value);
    },

    /**
     * @return { string }
     */
    getValue: function() {
        return this.textField.getValue();
    },

});
//{/block}
