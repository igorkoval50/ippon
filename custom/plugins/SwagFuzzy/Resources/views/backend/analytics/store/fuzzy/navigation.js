// {namespace name=backend/analytics/view/fuzzy}
// {block name="backend/analytics/store/navigation/items"}
// {$smarty.block.parent}
{
    id: 'searches_without_results',
    text: '{s name=navigationStores/searchesWithoutResults}Search terms without results{/s}',
    store: 'analytics-store-navigation-searches_without_results',
    iconCls: 'sprite-magnifier',
    comparable: true,
    leaf: true,
    multiShop: true
},
// {/block}
