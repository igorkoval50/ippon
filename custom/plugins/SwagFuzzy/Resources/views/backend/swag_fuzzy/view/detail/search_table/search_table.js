// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/detail/relevance/relevance"}
Ext.define('Shopware.apps.SwagFuzzy.view.detail.searchTable.SearchTable', {
    extend: 'Shopware.model.Container',
    alias: 'widget.searchTable-detail-container',
    padding: 10,

    configure: function () {
        return {
            controller: 'SwagFuzzySearchTable',
            splitFields: false,
            fieldSets: {
                title: '',
                fields: {
                    table: {
                        fieldLabel: '{s name=searchTableDetail/table}Choose table{/s}',
                        allowBlank: false
                    },
                    referenceTable: {
                        fieldLabel: '{s name=searchTableDetail/referenceTable}Choose reference table{/s}'
                    },
                    foreignKey: {
                        fieldLabel: '{s name=searchTableDetail/foreignKey}Choose foreign key{/s}'
                    },
                    additionalCondition: {
                        fieldLabel: '{s name=searchTableDetail/additionalCondition}Define additional condition{/s}'
                    }
                }
            }
        };
    }
});
// {/block}
