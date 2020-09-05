// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/detail/profile/window"}
Ext.define('Shopware.apps.SwagFuzzy.view.detail.profile.Window', {
    extend: 'Shopware.window.Detail',
    alias: 'widget.profile-detail-window',
    title: '{s name=profileDetail/title}Save profile{/s}',
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
