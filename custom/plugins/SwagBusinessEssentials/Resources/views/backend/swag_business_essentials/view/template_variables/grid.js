// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/view/template_variables/grid"}
Ext.define('Shopware.apps.SwagBusinessEssentials.view.template_variables.Grid', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.businessEssentials-template-variables-grid',
    title: '{s name="TemplateVariablesTab"}{/s}',
    flex: 1,

    // Necessary to properly display the template-variables
    cls: Ext.baseCSSPrefix + 'article-properties-grid',

    deleteButtonText: '{s name="TplDeleteButtonText"}{/s}',

    snippets: {
        columnHeader: {
            variableColumnHeader: '{s name="VariableColumnHeader"}{/s}',
            descriptionColumnHeader: '{s name="DescriptionColumnHeader"}{/s}',
            customerGroupsColumnHeader: '{s name="CustomerGroupsColumnHeader"}{/s}'
        }
    },

    initComponent: function() {
        var me = this;

        me.callParent(arguments);

        me.on('itemclick', Ext.bind(function(gridView, record, dom, index, event) {
            me.fireEvent('deleteElement', me, record, event);
        }, me));
    },

    configure: function() {
        var me = this;
        return {
            detailWindow: 'Shopware.apps.SwagBusinessEssentials.view.template_variables.Window',
            columns: {
                variable: {
                    xtype: 'templatecolumn',
                    header: me.snippets.columnHeader.variableColumnHeader,
                    tpl: '{literal}{${variable}}{/literal}',
                    flex: 1
                },
                description: { header: me.snippets.columnHeader.descriptionColumnHeader, flex: 2 },
                assignedCustomerGroups: {
                    header: me.snippets.columnHeader.customerGroupsColumnHeader,
                    flex: 3,
                    renderer: Ext.bind(me.renderCustomerGroups, me)
                }
            }
        };
    },

    /**
     * Renders the customer-groups as bubbles, same as in the article-properties.
     *
     * @param { Array } values
     * @param { string } style
     * @param { Ext.data.Model } model
     * @returns { string }
     */
    renderCustomerGroups: function(values, style, model) {
        var result = [Ext.String.format('<ul class="[0]item-bubble-list">', Ext.baseCSSPrefix)],
            customerGroupsStore = model.customerGroups();

        customerGroupsStore.each(function(customerGroup, index) {
            if (!customerGroup) {
                return;
            }

            result.push(Ext.String.format(
                '<li><span class="[0]item-bubble" data-value-id="[1]" data-index="[2]">[3]<span class="cross-btn">x</span></span></li>',
                Ext.baseCSSPrefix, customerGroup.data.id, index, customerGroup.data.name
            ));
        });
        result.push('</ul>');

        return result.join(' ');
    }
});
// {/block}
