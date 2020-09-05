/**
 * The detail window for a custom products template.
 */
// {namespace name="backend/swag_custom_products/detail/window"}
// {block name="backend/swag_custom_products/view/detail/window"}
Ext.define('Shopware.apps.SwagCustomProducts.view.detail.Window', {
    alias: 'widget.swag-custom-products-detail-window',
    extend: 'Shopware.window.Detail',

    // Preventing to open more than one detail window at the same time
    modal: true,
    minimizable: false,

    height: 700,
    width: 800,

    getArticleUrl: '{url action=getSelectedArticles}',

    /**
     * @type { Object }
     */
    snippets: {
        tooltip: '{s name="article/tooltip"}You have to save the template first, then you can add a product.{/s}',
        title: {
            create: '{s name="title/create"}Create template{/s}',
            edit: '{s name="title/edit"}Edit template - [0]{/s}'
        }
    },

    /**
     * Returns the configuration of this component.
     *
     * @overwrite
     * @returns { Object }
     */
    initComponent: function () {
        var me = this;
        me.title = me.getTitle();

        // Proxy the save event and expose it to the shopware application to react to it globally.
        me.on('template-save', function (window, record) {
            var templateId = record.get('id');

            // set the template ID for each option, because new options are not loaded correctly on saving the template
            record.getOptionsStore.data.each(function (item) {
                item.set({
                    'templateId': templateId
                });
            });

            Ext.defer(function () {
                Shopware.app.Application.fireEvent('SwagCustomProducts-onSave');
            }, 1500);
        });

        me.on('afterrender', Ext.bind(me.onAfterRender, me));

        me.callParent(arguments);
    },

    /**
     * After render we check if the record is saved.
     * if the record save and a newProduct is defined (in case of: you open the template over the articleModule)
     * the relation get saved
     */
    onAfterRender: function () {
        var me = this;

        if (!me.record.get('id')) {
            me.articleTab.setDisabled(true);
        }
    },

    /**
     * on cancel... delete the added
     * @overwrite
     */
    onCancel: function () {
        var me = this;

        Ext.Array.each(me.articleTab.newRecords, function (product) {
            me.articleTab.callAjax(
                me.articleTab.deleteArticleUrl,
                { articleId: product.get('id'), templateId: me.record.get('id') }
            );
        });

        me.callParent(arguments);
    },

    /**
     * @overwrite
     */
    configure: function () {
        return {
            associations: [
                'options',
                'articles'
            ],
            translationKey: 'customProductTemplateTranslations'
        };
    },

    /**
     * @returns { String }
     */
    getTitle: function () {
        var me = this;

        if (me.record.get('internalName')) {
            return Ext.String.format(me.snippets.title.edit, me.record.get('internalName'));
        }
        return me.snippets.title.create;
    },

    /**
     * Method overriding to pass the template record into other components
     *
     * @param type { String }
     * @param model { Shopware.data.Model }
     * @param store { Ext.data.Store }
     * @param association { Ext.data.Association }
     * @param baseRecord { Shopware.data.Model }
     * @returns { Ext.container.Container } | { Ext.grid.Panel }
     */
    createAssociationComponent: function (type, model, store, association, baseRecord) {
        var me = this;

        var component = me.callParent(arguments);
        component.templateRecord = me.record;

        return component;
    },

    /**
     * Sets the indexes of the tab items to reload the window at the current activated tab.
     *
     * @returns { Array }
     */
    createTabItems: function () {
        var me = this,
            articleTab = me.createArticleTab(),
            items = me.callParent(arguments);

        Ext.Array.insert(items, 1, [articleTab]);

        items.forEach(function (item, index) {
            item.index = index;
        });

        return items;
    },

    /**
     * Create the articleTab
     *
     * @returns { Shopware.apps.SwagCustomProducts.view.detail.Article | * }
     */
    createArticleTab: function () {
        var me = this;

        me.articleTab = Ext.create('Shopware.apps.SwagCustomProducts.view.detail.Article', {
            store: me.createArticleStore(),
            tabConfig: me.record.get('id') ? null : { tooltip: me.snippets.tooltip }
        });

        return me.articleTab;
    },

    /**
     * Create the articleStore
     *
     * @returns { Ext.data.Store }
     */
    createArticleStore: function () {
        var me = this;

        return Ext.create('Ext.data.Store', {
            autoLoad: false,
            fields: ['id', 'name', 'number'],
            proxy: {
                type: 'ajax',
                extraParams: {
                    templateId: me.record.get('id')
                },
                api: {
                    read: me.getArticleUrl
                },
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });
    }
});
// {/block}
