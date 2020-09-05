//

// {block name="backend/swag_custom_products/components/articleNumberSearch"}
Ext.define('Shopware.apps.SwagCustomProducts.view.components.ArticleNumberSearch', {
    extend: 'Shopware.form.field.ArticleSearch',

    returnValue: 'ordernumber',
    hiddenReturnValue: 'ordernumber',

    articleStore: Ext.create('Shopware.apps.Base.store.Variant'),

    /**
     * @overwrite
     * @returns { * }
     */
    getValue: function() {
        return this.getSearchField().getValue();
    },

    /**
     * @overwrite
     * @param { string } value
     */
    setValue: function(value) {
        this.getSearchField().setValue(value);
    },

    /**
     * @overwrite
     * @return { Ext.form.field.Trigger | [object] }
     */
    createSearchField: function () {
        var me = this,
            fieldConfig = Ext.apply({
                componentLayout: 'textfield',
                triggerCls: 'reset',
                emptyText: me.snippets.emptyText,
                fieldLabel: (me.fieldLabel || undefined),
                labelWidth: (me.labelWidth || undefined),
                cls: Ext.baseCSSPrefix + 'search-article-live-field',
                name: me.searchFieldName,
                enableKeyEvents: true,
                anchor: (me.anchor || undefined),
                hideTrigger: true,
                onTriggerClick: function() {
                    this.reset();
                    this.focus();
                    this.setHideTrigger(true);
                    me.dropDownMenu.hide();
                    me.fireEvent('reset', me, this);
                },
                listeners: {
                    scope: me,
                    keyup: me.onSearchKeyUp,
                    blur: me.onSearchBlur,
                    change: Ext.bind(me.onTriggerFieldChange, me)
                }
            }, me.formFieldConfig);

        return Ext.create('Ext.form.field.Trigger', fieldConfig);
    },

    /**
     * The event handler for the searchField.
     * This is for trigger the change event in the next layer.
     *
     * @param { Ext.form.field.Trigger } triggerField
     * @param { string } newValue
     * @param { string } oldValue
     * @param { mixed } eOpts
     */
    onTriggerFieldChange: function (triggerField, newValue, oldValue, eOpts) {
        var me = this;

        me.fireEvent('change', me, newValue, oldValue, eOpts);
    }
});
// {/block}
