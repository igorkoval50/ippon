// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/view/template_variables/customer_groups"}
Ext.define('Shopware.apps.SwagBusinessEssentials.view.template_variables.CustomerGroups', {
    extend: 'Shopware.grid.Association',
    alias: 'widget.template_variables-customer-groups',
    title: '{s name="CustomerGroupsColumnHeader"}{/s}',
    height: 220,

    snippets: {
        emptyText: '{s name="TemplateDetailSearchEmpty"}{/s}'
    },

    configure: function() {
        return {
            controller: 'SwagBETemplateVariables',
            columns: {
                name: 'Name'
            }
        };
    },

    /**
     * Overwrites the original "createSearchCombo"-method to remove the field-label and add an empty-text.
     *
     * @param { Ext.data.Store } store
     * @returns { Ext.form.field.ComboBox }
     */
    createSearchCombo: function(store) {
        var me = this,
            item = me.callParent(arguments);

        Ext.apply(item, {
            labelWidth: 0,
            fieldLabel: '',
            emptyText: me.snippets.emptyText
        });

        return item;
    },

    /**
     * No page-size combo necessary in this component.
     *
     * @returns { Object }
     */
    createPageSizeCombo: function() {
        return {};
    },

    /**
     * No selection-model necessary in this component.
     *
     * @returns { Object }
     */
    createSelectionModel: function() {
        return {};
    }
});
// {/block}
