//{namespace name=backend/prems_emotion_cms/article/view/grid}
//{block name="backend/article/view/prems_emotion_cms/Grid"}
Ext.define('Shopware.apps.Article.PremsEmotionCms.view.Grid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.prems-emotion-cms-article-grid',

    /**
     * Sets up the ui component
     * @return void
     */
    initComponent: function() {
        var me = this;

        me.emotionStore = Ext.create('Shopware.apps.Article.PremsEmotionCms.store.Emotion').load();
        me.shopStore = Ext.create('Shopware.apps.Base.store.ShopLanguage').load({});

        me.toolbar     = me.getToolbar();
        me.columns     = me.getColumns();
        me.dockedItems = [ me.toolbar, me.getPagingbar() ];

        me.plugins = me.createPlugins();

        me.callParent(arguments);
    },

    createPlugins: function() {
        var me = this,
            rowEditor = Ext.create('Ext.grid.plugin.RowEditing', {
                clicksToEdit: 2,
                autoCancel: true,
                listeners: {
                    scope: me,
                    edit: function(editor, e) {
                        var me = this,
                            record = me.store.getAt(e.rowIdx);

                        if(record == null) {
                            return;
                        }

                        Ext.Ajax.request({
                            url: '{url controller="PremsEmotionCmsArticle" action="updateEmotionArticle"}',
                            method: 'POST',
                            params: {
                                id: record.data.id,
                                position: record.data.position,
                                shopId: me.comboShop.getValue(),
                            },
                            success: function(response, opts) {
                                Shopware.Notification.createGrowlMessage('{s name=grid/growl/successfull}{/s}', '{s name=grid/growl/emotion_updated}{/s}', '{s name=grid/growl/emotion}{/s}');
                                me.store.load();
                            }
                        });
                    }
                }
            });

        return [ rowEditor ];
    },

    /**
     * Creates the grid toolbar with the different buttons.
     * @return [Ext.toolbar.Toolbar] grid toolbar
     */
    getToolbar: function () {
        var me = this;

        //creates the add button for the toolbar to grant the user the option to add esds manual.
        me.addButton = Ext.create('Ext.button.Button', {
            iconCls:'sprite-plus-circle-frame',
            text: '{s name=grid/button/add}{/s}',
            action:'addEmotion',
            disabled: true,
            handler: function() {
                //me.fireEvent('addEmotion', me.combo.getValue());

                var store = me.getStore();

                Ext.Ajax.request({
                    url: '{url controller="PremsEmotionCmsArticle" action="createEmotionArticle"}',
                    method: 'POST',
                    params: {
                        emotionId: me.comboEmotion.getValue(),
                        position: me.comboPosition.getValue(),
                        shopId: me.comboShop.getValue(),
                        articleId: me.articleId
                    },
                    success: function(response, opts) {
                        Shopware.Notification.createGrowlMessage('{s name=grid/growl/successfull}{/s}', '{s name=grid/growl/emotion_assigned}{/s}', '{s name=grid/growl/emotion}{/s}');
                        me.store.load();
                    }
                });

                this.disable();
            }
        });

        me.comboEmotion = Ext.create('Ext.form.ComboBox', {
            store: me.emotionStore,
            forceSelection: true,
            queryMode: 'local',
            valueField: 'id',
            displayField: 'name',
            fieldLabel: '{s name=grid/field_label/emotion}{/s}',
            emptyText: '{s name=grid/empty_text/choose_emotion}{/s}',
            anchor: '100%',
            listeners: {
                select: function(field, records) {
                    me.addButton.enable();
                }
            }
        });

        me.comboPosition = Ext.create('Ext.form.ComboBox', {
            forceSelection: true,
            queryMode: 'local',
            valueField: 'id',
            displayField: 'name',
            fieldLabel: '{s name=grid/field_label/position}{/s}',
            store: Ext.create( "Ext.data.Store",
                {
                    fields: [ "id", "name" ],
                    data:
                        [
                            { id: 0, name: "{s name=grid/select/position_above_article_description}{/s}" },
                            { id: 1, name: "{s name=grid/select/position_below_article_description}{/s}" },
                            { id: 2, name: "{s name=grid/select/position_own_block}{/s}" }
                        ]
                }
            ),
            anchor: '100%'
        });

        me.comboShop = Ext.create('Ext.form.ComboBox', {
            store: me.shopStore,
            forceSelection: true,
            queryMode: 'local',
            valueField: 'id',
            displayField: 'name',
            fieldLabel: '{s name=grid/field_label/shop}{/s}',
            emptyText: '{s name=grid/empty_text/shop}{/s}',
            anchor: '100%'
        });

        //creates the add button for the toolbar to grant the user the option to add esds manual.
        me.batchButton = Ext.create('Ext.button.Button', {
            iconCls:'sprite-blue-folders-stack',
            text: '{s name=grid/button/batch}{/s}',
            disabled: false,
            handler: function() {
                var store = me.getStore();

                me.batchWindow = Ext.create('Shopware.apps.Article.PremsEmotionCms.view.batch.Mask',{
                    parentStore: store
                }).show();
            }
        });

        return Ext.create('Ext.toolbar.Toolbar', {
            dock:'top',
            ui: 'shopware-ui',
            cls: 'shopware-toolbar',
            items:[
                me.comboEmotion,
                me.comboPosition,
                me.comboShop,
                me.addButton,
                me.batchButton,
                { xtype:'tbspacer', width:6 }
            ]
        });
    },

    /**
     * Creates the grid columns
     *
     * @return [array] grid columns
     */
    getColumns: function () {
        var me = this,
            actionColumItems = [],
            store = me.getStore();

        actionColumItems.push(
            {
                iconCls:'sprite-pin marketing--shopping-worlds',
                tooltip: '{s name=grid/tooltip/open_emotion}{/s}',
                handler: function(grid, rowIndex, colIndex, metaData, event, record) {
                    me.openEmotion(record.get('emotionId'));
                }
            },
            {
                iconCls:'sprite-minus-circle-frame',
                action:'delete',
                tooltip:'{s name=grid/tooltip/delete}{/s}',
                handler: function (view, rowIndex, colIndex, item, opts, record) {
                    var records = [ record ];

                    if (records.length > 0) {
                        // we do not just delete - we are polite and ask the user if he is sure.
                        Ext.MessageBox.confirm('{s name=grid/message_box/emotion}{/s}', '{s name=grid/message_box/remove_emotion_relation_question}{/s}' , function (response) {
                            if ( response !== 'yes' ) {
                                return;
                            }
                            store.remove(records);
                            store.sync({
                                callback: function() {
                                    Shopware.Notification.createGrowlMessage('{s name=grid/growl/successfull}{/s}', '{s name=grid/growl/emotion_relation_removed}{/s}', '{s name=grid/growl/emotion}{/s}');
                                    store.currentPage = 1;
                                    store.load();
                                }
                            });
                        });
                    }

                }
            }
        );

        var columns = [
            {
                header: '{s name=grid/columns/emotion}{/s}',
                dataIndex: 'name',
                flex: 1
            },
            {
                header: '{s name=grid/columns/position}{/s}',
                dataIndex: 'position',
                flex: 1,
                renderer: me.renderLayout,
                editor: {
                    xtype:  Ext.create('Ext.form.ComboBox', {
                        forceSelection: true,
                        queryMode: 'local',
                        valueField: 'id',
                        displayField: 'name',

                        store: Ext.create( "Ext.data.Store",
                            {
                                fields: [ "id", "name" ],
                                data:
                                    [
                                        { id: 0, name: "{s name=grid/columns/position_above_article_description}{/s}" },
                                        { id: 1, name: "{s name=grid/columns/position_below_article_description}{/s}" },
                                        { id: 2, name: "{s name=grid/columns/position_own_block}{/s}" }
                                    ]
                            }
                        ),
                        anchor: '100%'
                    }),
                    allowBlank: false
                }
            },
            {
                header: '{s name=grid/columns/shop}{/s}',
                dataIndex: 'shopId',
                renderer: me.renderShopLayout,
                flex: 1
            },
            {
                /**
                 * Special column type which provides
                 * clickable icons in each row
                 */
                xtype: 'actioncolumn',
                width: actionColumItems.length * 26,
                items: actionColumItems
            }
        ];

        return columns;
    },

    renderLayout: function( value, metaData, record ) {
        var me = this;
        if (value == 0) {
            return '{s name=grid/columns/position_above_article_description}{/s}';
        } else if(value == 1) {
            return '{s name=grid/columns/position_below_article_description}{/s}';
        } else if (value == 2) {
            return '{s name=grid/columns/position_own_block}{/s}';
        }
    },

    renderShopLayout: function( value, metaData, record ) {
        var me = this;

        if (value == 0) {
            return '{s name=grid/columns/all}{/s}';
        } else {
            //return record.get('name');
            var arrayLength = me.shopStore.data.length;
            for (var i = 0; i < arrayLength; i++) {
                if (me.shopStore.data.items[i].data.id == value) {
                   return me.shopStore.data.items[i].data.name;
                }
                //Do something
            }
        }
    },

    /**
     * @param { int } emotionId
     */
    openEmotion: function(emotionId) {
        Shopware.app.Application.addSubApplication({
            name: 'Shopware.apps.Emotion',
            action: 'editemotion',
            params: {
                emotionId: emotionId
            }
        });
    },

    /**
     * Creates pagingbar shown at the bottom of the grid
     *
     * @return Ext.toolbar.Paging
     */
    getPagingbar: function () {
        var pagingbar =  Ext.create('Ext.toolbar.Paging', {
            store: this.store,
            dock: 'bottom',
            displayInfo: true
        });

        return pagingbar;
    }
});

//{/block}