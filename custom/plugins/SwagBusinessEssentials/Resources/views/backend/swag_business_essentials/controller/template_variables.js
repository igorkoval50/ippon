// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/controller/template_variables"}
Ext.define('Shopware.apps.SwagBusinessEssentials.controller.TemplateVariables', {
    extend: 'Enlight.app.Controller',

    init: function() {
        var me = this;

        me.control({
            'businessEssentials-template-variables-grid': {
                deleteElement: me.onDeleteElement
            }
        });
    },

    /**
     * Deletes a single customer-group association on click on the bubbles of the grid.
     *
     * @param { Shopware.grid.Panel } templateGrid
     * @param { Ext.data.Model } record
     * @param { Object } event
     */
    onDeleteElement: function(templateGrid, record, event) {
        var store = templateGrid.getStore(),
            itemIndex,
            element = Ext.get(event.target),
            notFound = true;

        if (element.hasCls('cross-btn')) {
            element = element.parent();
        }

        if (element.hasCls(Ext.baseCSSPrefix + 'item-bubble')) {
            notFound = false;
        }

        if (notFound) {
            return;
        }

        itemIndex = ~~(1 * element.getAttribute('data-index'));

        record.customerGroupsStore.removeAt(itemIndex);
        templateGrid.reconfigure(store);
        templateGrid.getSelectionModel().deselectAll();

        record.save();
    }
});
// {/block}
