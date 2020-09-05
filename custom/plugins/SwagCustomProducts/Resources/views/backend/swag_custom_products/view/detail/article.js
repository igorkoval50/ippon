/**
 * Grid which displays the assigned products which belong to the called template.
 */

// {namespace name="backend/swag_custom_products/detail/article"}
// {block name="backend/swag_custom_products/view/detail/article"}
Ext.define('Shopware.apps.SwagCustomProducts.view.detail.Article', {
    extend: 'Shopware.grid.Panel',
    alias: 'widget.template-view-detail-article',
    height: 300,

    /**
     * @type { Object }
     */
    snippets: {
        title: '{s name="title"}Products{/s}',

        columns: {
            name: {
                header: '{s name="columns/name/header"}Name{/s}'
            },
            number: {
                header: '{s name="columns/number/header"}Product number{/s}'
            }
        },
        errorMessage: {
            title: '{s name="error_message/title"}Article has been selected already{/s}',
            text: '{s name="error_message/text"}The selected article [0] is already assigned to <strong>[1]</strong>.{/s}',
            fallbackName: '{s name="error_message/fallback_name"}this template{/s}'
        }
    },

    searchUrl: '{url action=searchArticle}',
    addArticleUrl: '{url action=addArticle}',
    deleteArticleUrl: '{url action=removeArticle}',

    /**
     * newRecords is a helperVariable to save added products.
     * If you click on cancel it is necessary to delete them from the relationTable
     * in the database.
     */
    newRecords: null,

    /**
     * @returns { Object }
     */
    configure: function () {
        var me = this;

        me.title = me.snippets.title;

        return {
            controller: 'SwagCustomProducts',
            pagingbar: true,
            editColumn: false,
            columns: {
                name: {
                    header: me.snippets.columns.name.header
                },
                number: {
                    header: me.snippets.columns.number.header
                }
            }
        };
    },

    /**
     * On init Component we set handler on the articles tab.
     */
    initComponent: function () {
        var me = this;

        Ext.QuickTips.init();
        me.newRecords = [];
        me.callParent(arguments);

        // this is a little hack for a memory problem if there a to much articles.
        // So we load the articles on click on the tabPanel.
        me.on('activate', Ext.bind(me.reloadStore, me));
    },

    /**
     * Create the toolbarItems
     *
     * @overwrite
     * @returns { Shopware.form.field.Search[] }
     */
    createToolbarItems: function () {
        var me = this;

        return [
            me.createSearchCombo(
                me.createArticleSearchStore()
            )
        ];
    },

    /**
     * @returns { Array }
     */
    createActionColumnItems: function () {
        var me = this,
            items = me.callParent(arguments);

        items.push(me.createProductDetailColumn());

        return items;
    },

    /**
     * @override
     */
    createDeleteColumn: function () {
        var me = this;

        return {
            action: 'delete',
            iconCls: 'sprite-minus-circle-frame',
            handler: Ext.bind(me.onDeleteArticle, me)
        };
    },

    /**
     * @param { Ext.grid.View } view
     * @param { integer } rowIndex
     * @param { integer } colIndex
     * @param { object } item
     * @param { object } opts
     * @param { Ext.data.Model } record
     */
    onDeleteArticle: function (view, rowIndex, colIndex, item, opts, record) {
        var me = this;

        me.callAjax(
            me.deleteArticleUrl,
            { articleId: record.get('id'), templateId: me.up('window').record.get('id') }
        );

        me.store.remove(record);
    },

    /**
     * @returns { Object }
     */
    createProductDetailColumn: function () {
        return {
            action: 'openArticle',
            iconCls: 'sprite-inbox',
            handler: function (view, rowIndex, colIndex, item, opts, record) {
                Shopware.app.Application.addSubApplication({
                    name: 'Shopware.apps.Article',
                    action: 'detail',
                    params: {
                        articleId: record.get('id')
                    }
                });
            }
        };
    },

    /**
     * Creates a search combo box for selecting and searching products.
     *
     * @param store { Ext.data.Store }
     * @returns { Shopware.form.field.Search }
     */
    createSearchCombo: function (store) {
        var me = this;

        return Ext.create('Shopware.form.field.Search', {
            name: 'associationSearchField',
            store: store,
            pageSize: 20,
            flex: 1,
            subApp: me.subApp,
            fieldLabel: me.searchComboLabel,
            margin: 5,
            multiSelect: true,
            listeners: {
                select: function (combo, records) {
                    var record = records[records.length - 1],
                        responseData;

                    Ext.Ajax.request({
                        url: '{url action="validateArticleSelectionAjax"}',
                        params: { id: record.get('id') },
                        success: function (response) {
                            responseData = Ext.JSON.decode(response.responseText);
                            me.successSelectArticleCallback(responseData, combo, records);
                        }
                    });
                }
            }
        });
    },

    /**
     * @returns { Ext.data.Store }
     */
    createArticleSearchStore: function () {
        var me = this;

        return Ext.create('Ext.data.Store', {
            model: 'Shopware.apps.SwagCustomProducts.model.Article',
            proxy: {
                type: 'ajax',
                url: me.searchUrl,
                reader: { type: 'json', root: 'data', totalProperty: 'total' }
            }
        });
    },

    /**
     * Callback function for the ajax validation request.
     *
     * @param { Array } responseData
     * @param { Ext.form.field.ComboBox } combo
     * @param { Array } records
     */
    successSelectArticleCallback: function (responseData, combo, records) {
        var me = this,
            record = records[records.length - 1];

        if (responseData.success) {
            me.onSelectSearchItem(combo, records);
        } else {
            me.onFailSelectedItem(combo, record.get('number'), responseData.data.internal_name);
        }
    },

    /**
     * Override to validate if the product already exist in the product store.
     *
     * @param { Ext.form.field.ComboBox } combo
     * @param { Array } records
     * @returns
     */
    onSelectSearchItem: function (combo, records) {
        var me = this,
            record = records[records.length - 1];

        if (me.gridStoreHasArticle(combo, record)) {
            me.onFailSelectedItem(combo, record.get('number'), me.getTemplateInternalName());
            return;
        }

        me.addArticleToStore(record);
    },

    /**
     * @param { Shopware.apps.SwagCustomProducts.model.Article } record
     */
    addArticleToStore: function (record) {
        var me = this;

        me.newRecords.push(record);
        me.callAjax(
            me.addArticleUrl,
            { articleId: record.get('id'), templateId: me.up('window').record.get('id') },
            me.reloadStore,
            null,
            me
        );
    },

    /**
     * @param { string } url
     * @param { object | null } parameter
     * @param { function | null } successCallback
     * @param { function | null } errorCallback
     * @param { object | null } scope
     */
    callAjax: function (url, parameter, successCallback, errorCallback, scope) {
        var callbackScope = Ext.isEmpty(scope) ? this : scope;

        Ext.Ajax.request({
            url: url,
            params: parameter,
            scope: callbackScope,
            success: function (response) {
                var responseData = Ext.JSON.decode(response.responseText);
                if (Ext.isEmpty(successCallback)) {
                    return;
                }

                Ext.callback(successCallback, callbackScope, [responseData]);
            },
            failure: function () {
                if (Ext.isEmpty(errorCallback)) {
                    return;
                }

                Ext.callback(errorCallback, callbackScope);
            }
        });
    },

    /**
     * Creates a growl message and deletes the selected value if the product is assigned to another template.
     *
     * @param { Ext.form.field.ComboBox } combo
     * @param { String } number
     * @param { String } internalName
     */
    onFailSelectedItem: function (combo, number, internalName) {
        var me = this;

        Shopware.Notification.createGrowlMessage(
            me.snippets.errorMessage.title,
            Ext.String.format(me.snippets.errorMessage.text, number, internalName)
        );
        combo.setValue('');
    },

    /**
     * @returns { String }
     */
    getTemplateInternalName: function () {
        var me = this;

        if (me.up('window').record.get('internalName')) {
            return me.up('window').record.get('internalName');
        }
        return me.snippets.errorMessage.fallbackName;
    },

    /**
     * @param { Ext.form.field.ComboBox } combo
     * @param { Ext.data.Model } record
     * @returns { boolean }
     */
    gridStoreHasArticle: function (combo, record) {
        var me = this;

        if (me.store.findExact('id', record.get('id')) < 0) {
            return false;
        }

        return true;
    },

    /**
     * We don`t need a selectionModel.
     *
     * @overwrite
     * @returns { null }
     */
    createSelectionModel: function () {
        return null;
    },

    /**
     * reload the store
     */
    reloadStore: function () {
        this.store.load();
    }
});
// {/block}
