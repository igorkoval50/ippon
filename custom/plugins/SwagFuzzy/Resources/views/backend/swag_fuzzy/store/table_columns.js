// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/store/table_columns"}
Ext.define('Shopware.apps.SwagFuzzy.store.TableColumns', {
    extend: 'Ext.data.Store',
    model: 'Shopware.apps.SwagFuzzy.model.TableColumns',
    proxy: {
        type: 'ajax',
        url: '{url action=getTableColumns}',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
// {/block}
