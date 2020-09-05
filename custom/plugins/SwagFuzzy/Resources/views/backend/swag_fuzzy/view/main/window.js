// {namespace name=backend/plugins/swag_fuzzy/main}
// {block name="backend/swag_fuzzy/view/main/window"}
Ext.define('Shopware.apps.SwagFuzzy.view.main.Window', {
    extend: 'Enlight.app.Window',

    alias: 'widget.swagFuzzy-main-window',

    minWidth: 800,
    minHeight: 300,

    layout: 'border',

    overflowY: 'auto',

    listeners: {
        resize: function (window, width, height) {
            if (height < 570) {
                window.setBodyStyle('padding: 0 15px 0 0;');
                window.doLayout();
            } else {
                window.setBodyStyle('padding: 0;');
                window.doLayout();
            }
        }
    },

    initComponent: function () {
        var me = this,
            topToolbar = me.getTopToolbar(),
            bottomToolbar = me.getBottomToolbar(),
            tabPanel;

        me.title = '{s name=window/title}Intelligent Search{/s}';

        tabPanel = Ext.create('Ext.tab.Panel', {
            items: [
                {
                    xtype: 'swagFuzzy-main-settings'
                },
                {
                    title: '{s name=window/tab/synonyms}Synonym groups / promotions{/s}',
                    xtype: 'swagFuzzy-main-synonymGroups'
                },
                {
                    title: '{s name=window/tab/relevance}Relevance / fields{/s}',
                    xtype: 'swagFuzzy-main-relevance'
                },
                {
                    title: '{s name=window/tab/tables}Table configuration{/s}',
                    xtype: 'swagFuzzy-main-searchTables'
                },
                {
                    title: '{s name=window/tab/profiles}Profiles{/s}',
                    xtype: 'swagFuzzy-main-profiles'
                },
                {
                    title: '{s name=window/tab/preview}Preview{/s}',
                    xtype: 'swagFuzzy-main-preview'
                }
            ]
        });

        me.formPanel = Ext.create('Ext.form.Panel', {
            minHeight: 462,

            layout: 'fit',
            region: 'center',

            name: 'swagFuzzy-form-panel',
            items: [tabPanel],
            border: false
        });

        me.dockedItems = [topToolbar];
        me.dockedItems.push(bottomToolbar);
        me.items = [me.formPanel];

        me.callParent(arguments);
    },

    /**
     * Creates the grid toolbar with the shop picker
     *
     * @return [Ext.toolbar.Toolbar] grid toolbar
     */
    getTopToolbar: function () {
        var me = this,
            shopStore = Ext.create('Shopware.apps.Base.store.Shop'),
            shopCombo,
            toolbar;

        shopStore.filters.clear();
        shopStore.load({
            callback: function (records) {
                shopCombo.setValue(records[0].get('id'));
            }
        });

        shopCombo = Ext.create('Ext.form.field.ComboBox', {
            fieldLabel: '{s name=window/chooseShop}Choose shop{/s}',
            store: shopStore,
            labelWidth: 80,
            name: 'shop-combo',
            margin: '3px 6px 3px 0',
            queryMode: 'local',
            valueField: 'id',
            editable: false,
            displayField: 'name',
            listeners: {
                'select': function () {
                    if (this.store.getAt('0')) {
                        me.fireEvent('changeShop');
                    }
                }
            }
        });

        toolbar = Ext.create('Ext.toolbar.Toolbar', {
            dock: 'top',
            ui: 'shopware-ui',
            items: [
                '->',
                shopCombo
            ]
        });

        return toolbar;
    },

    /**
     * Creates buttons shown in form panel
     *
     * @return array
     */
    getBottomToolbar: function () {
        var me = this,
            toolbar;

        toolbar = Ext.create('Ext.toolbar.Toolbar', {
            ui: 'shopware-ui',
            dock: 'bottom',
            cls: 'shopware-toolbar',
            items: [
                '->',
                {
                    text: '{s name=window/save}Save{/s}',
                    action: 'save',
                    cls: 'primary',
                    formBind: true,
                    handler: function () {
                        me.fireEvent('saveSwagFuzzy');
                    }
                }
            ]
        });

        return toolbar;
    }
});
// {/block}
