// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/detail/relevance/window"}
Ext.define('Shopware.apps.SwagFuzzy.view.detail.searchTable.Window', {
    extend: 'Shopware.window.Detail',
    alias: 'widget.searchTable-detail-window',
    title: '{s name=searchTableDetail/title}Search table details{/s}',
    height: 300,
    width: 480,
    padding: '0 0 5',

    onSave: function () {
        var me = this;

        me.callParent();
        me.destroy();
    }
});
// {/block}
