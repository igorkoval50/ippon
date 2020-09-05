// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/view/template_variables/window"}
Ext.define('Shopware.apps.SwagBusinessEssentials.view.template_variables.Window', {
    extend: 'Shopware.window.Detail',
    alias: 'widget.businessEssentials-detail-template-variables-window',
    height: 450,
    title: '{s name="TplDetailTitle"}{/s}',
    width: 350,

    // Destroys the detail-window when saving the entry
    onSave: function () {
        var me = this;

        Shopware.app.Application.on('templatevariables-save-successfully', function (controller, result, window) {
            window.destroy();
        });

        me.callParent(arguments);
    }
});
// {/block}
