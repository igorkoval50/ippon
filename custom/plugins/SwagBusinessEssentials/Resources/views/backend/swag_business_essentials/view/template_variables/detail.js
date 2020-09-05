// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/view/template_variables/detail"}
Ext.define('Shopware.apps.SwagBusinessEssentials.view.template_variables.Detail', {
    extend: 'Shopware.model.Container',

    snippets: {
        variableFieldLabel: '{s name="VariableName"}{/s}',
        descriptionFieldLabel: '{s name="DescriptionColumnHeader"}{/s}',
        infoText: '{s name="TemplateVariableDetailInfo"}{/s}'
    },

    configure: function() {
        var me = this;

        return {
            controller: 'SwagBETemplateVariables',
            associations: [ 'customerGroups' ],
            splitFields: false,
            fieldSets: [{
                title: '',
                border: 0,
                padding: 10,
                fields: {
                    variable: me.snippets.variableFieldLabel,
                    description: me.snippets.descriptionFieldLabel
                }
            }]
        };
    },

    /**
     * Creates and returns a small info-container.
     *
     * @returns { Array }
     */
    createItems: function() {
        var me = this,
            items = me.callParent(arguments);

        items.push(Ext.create('Ext.container.Container', {
            style: {
                color: '#61677f',
                fontStyle: 'italic'
            },
            padding: 10,
            html: me.snippets.infoText
        }));

        return items;
    }
});
// {/block}
