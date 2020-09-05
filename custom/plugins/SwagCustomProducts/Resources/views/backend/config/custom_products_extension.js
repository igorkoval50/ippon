
// {block name="backend/config/view/custom_search/facet/detail"}
// {$smarty.block.parent}
// {include file="backend/config/custom_products_facet.js"}

Ext.define('Shopware.apps.Config.CustomProductFacetExtension', {
    override: 'Shopware.apps.Config.view.custom_search.facet.Detail',

    initHandlers: function() {
        var handlers = this.callParent(arguments);
        handlers.push(Ext.create('Shopware.apps.Config.CustomProductsFacet'));
        return handlers;
    }
});

// {/block}

// {block name="backend/config/view/custom_search/sorting/sorting/selection"}
// {$smarty.block.parent}
// {include file="backend/config/custom_products_sorting.js"}

Ext.define('Shopware.apps.Config.CustomProductsSortingExtension', {
    override: 'Shopware.apps.Config.view.custom_search.sorting.SortingSelection',

    initSortings: function() {
        var handlers = this.callParent(arguments);
        handlers.push(Ext.create('Shopware.apps.Config.CustomProductsSorting'));
        return handlers;
    }
});

// {/block}
