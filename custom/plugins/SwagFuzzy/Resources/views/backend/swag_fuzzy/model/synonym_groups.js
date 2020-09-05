// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/model/synonym_groups"}
Ext.define('Shopware.apps.SwagFuzzy.model.SynonymGroups', {
    extend: 'Shopware.data.Model',

    configure: function () {
        return {
            controller: 'SwagFuzzySynonyms',
            detail: 'Shopware.apps.SwagFuzzy.view.detail.synonymGroup.SynonymGroup'
        };
    },

    fields: [
        { name: 'id', type: 'int' },
        { name: 'shopId', type: 'int' },
        { name: 'groupName', type: 'string' },
        { name: 'active', type: 'boolean' },
        { name: 'normalSearchEmotionId', type: 'int' },
        { name: 'normalSearchBanner', type: 'string' },
        { name: 'normalSearchLink', type: 'string' },
        { name: 'normalSearchHeader', type: 'string' },
        { name: 'normalSearchDescription', type: 'string' },
        { name: 'ajaxSearchBanner', type: 'string' },
        { name: 'ajaxSearchLink', type: 'string' },
        { name: 'ajaxSearchHeader', type: 'string' },
        { name: 'ajaxSearchDescription', type: 'string' }
    ],

    associations: [
        {
            relation: 'OneToMany',

            type: 'hasMany',
            model: 'Shopware.apps.SwagFuzzy.model.Synonyms',
            name: 'getSynonyms',
            associationKey: 'synonyms'
        },
        {
            relation: 'ManyToOne',
            field: 'ajaxSearchBanner',

            type: 'hasMany',
            model: 'Shopware.apps.Base.model.Media',
            name: 'getMedia1',
            associationKey: 'media'
        },
        {
            relation: 'ManyToOne',
            field: 'normalSearchBanner',

            type: 'hasMany',
            model: 'Shopware.apps.Base.model.Media',
            name: 'getMedia2',
            associationKey: 'media'
        },
        {
            relation: 'ManyToOne',
            field: 'shopId',

            type: 'hasMany',
            model: 'Shopware.apps.Base.model.Shop',
            name: 'getShop',
            associationKey: 'shop'
        }
    ]
});
// {/block}
