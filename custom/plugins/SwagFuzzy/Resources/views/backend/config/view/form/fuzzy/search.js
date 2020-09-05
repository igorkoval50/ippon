
// {namespace name=backend/config/view/search}
// {block name="backend/config/view/form/search"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.Config.view.form.fuzzy.Search', {
    override: 'Shopware.apps.Config.view.form.Search',

    getItems: function () {
        var me = this,
            elements = me.formRecord.getElements(),
            elementItems = elements.data.items,
            elementKeys = elements.data.keys,
            elementIndex,
            options = [
                'fuzzysearchdistance',
                'fuzzysearchexactmatchfactor',
                'fuzzysearchmatchfactor',
                'fuzzysearchmindistancentop',
                'fuzzysearchpartnamedistancen',
                'fuzzysearchpatternmatchfactor'
            ];

        Ext.Array.each(options, function (option) {
            Ext.Array.each(elementItems, function (element, index) {
                if (element.data.name == option) {
                    elementIndex = index;
                    return false;
                }
            });
            if (elementIndex) {
                elementItems.splice(elementIndex, 1);
                elementKeys.splice(elementIndex, 1);
                elementIndex = null;
            }
        });

        return [
            me.getConfigForm()
        ];
    }
});
// {/block}
