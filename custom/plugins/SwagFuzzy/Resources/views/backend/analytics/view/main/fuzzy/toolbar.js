
// {namespace name=backend/analytics/view/fuzzy}
// {block name="backend/analytics/view/main/toolbar"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.Analytics.view.main.fuzzy.Toolbar', {
    override: 'Shopware.apps.Analytics.view.main.Toolbar',

    initComponent: function () {
        var me = this;

        me.callOverridden(arguments);

        me.insert(3, {
            xtype: 'tbspacer',
            width: 25
        });
        me.insert(4, {
            xtype: 'textfield',
            cls: 'searchfield',
            name: 'searchTerm',
            width: 150,
            emptyText: '{s name="toolbar/search_field_text"}Search ...{/s}'
        });
    }
});
// {/block}
