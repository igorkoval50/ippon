/*
 * Copyright (c) Kickbyte GmbH - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 */

//{block name="backend/base/attribute/form"}
//{$smarty.block.parent}

Ext.define('KibVariantListing.FieldHandler-KibVariantListingImgMapping', {
    extend: 'Shopware.attribute.FieldHandlerInterface',
    mixins: {
        factory: 'Shopware.attribute.SelectionFactory'
    },

    /**
     * @override
     * @param { Shopware.model.AttributeConfig } attribute
     * @returns { boolean }
     */
    supports: function (attribute) {
        var name = attribute.get('columnName');
        if (attribute.get('tableName') !== 's_articles_img_attributes') {
            return false;
        }
        return (name === 'kib_variantlisting_prop_img_mapping');
    },

    /**
     * @override
     * @param { Object } field
     * @param { Shopware.model.AttributeConfig } attribute
     * @returns { object }
     */
    create: function (field, attribute) {
        var attributeSelection = this.createSelection(
            field,
            attribute,
            'Shopware.form.field.Grid',
            this.createDynamicSearchStore(attribute),
            this.createDynamicSearchStore(attribute)
        );

        var articleCmp = Ext.ComponentQuery.query('article-base-field-set')[0],
            queryString = '&filters[0][property]=option.filterable&filters[0][value]=1' +
                '&filters[1][property]=articles.id&filters[1][value]=' + articleCmp.article.internalId;

        attributeSelection.searchStore.getProxy().url = attributeSelection.searchStore.getProxy().url + queryString;
        attributeSelection.store.getProxy().url = attributeSelection.store.getProxy().url + queryString;

        return attributeSelection;
    }
});

Ext.define('Shopware.attribute.Form-KibVariantListingImgMapping', {
    override: 'Shopware.attribute.Form',

    registerTypeHandlers: function () {
        var handlers = this.callParent(arguments);

        return Ext.Array.insert(handlers, 0, [Ext.create('KibVariantListing.FieldHandler-KibVariantListingImgMapping')]);
    }
});
// {/block}
