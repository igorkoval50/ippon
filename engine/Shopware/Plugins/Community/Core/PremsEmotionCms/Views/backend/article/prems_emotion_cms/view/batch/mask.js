//{namespace name=backend/prems_emotion_cms/article/view/mask}

Ext.define('Shopware.apps.Article.PremsEmotionCms.view.batch.Mask', {
    /**
     * Define that the order main window is an extension of the enlight application window
     * @string
     */
    extend: 'Enlight.app.SubWindow',
    /**
     * List of short aliases for class names. Most useful for defining xtypes for widgets.
     * @string
     */
    alias: 'widget.prems-emotion-cms-view-batch-mask',
    /**
     * Define window width
     * @integer
     */
    width: 600,
    /**
     * Define window height
     * @integer
     */
    height: 540,
    /**
     * Display no footer button for the detail window
     * @boolean
     */
    footerButton: false,
    /**
     * Set vbox layout and stretch align to display the toolbar on top and the button container
     * under the toolbar.
     * @object
     */
    layout: {
        align: 'stretch',
        type: 'vbox',
    },
    /**
     * If the modal property is set to true, the user can't change the window focus to another window.
     * @boolean
     */
    modal: false,
    /**
     * The body padding is used in order to have a smooth side clearance.
     * @integer
     */
    //bodyPadding: 10,

    autoScroll: true,

    /**
     * Disable the close icon in the window header
     * @boolean
     */
    closable: true,
    /**
     * Disable window resize
     * @boolean
     */
    resizable: true,
    /**
     * Disables the maximize button in the window header
     * @boolean
     */
    maximizable: true,
    /**
     * Disables the minimize button in the window header
     * @boolean
     */
    minimizable: true,
    /**
     * The title shown in the window header
     */
    title: '{s name=mask/window/title}{/s}',

    /**
     * Constructor for the generation window
     * Registers events and adds all needed content items to the window
     */
    initComponent: function() {
        var me = this;

        me.emotionStore = Ext.create('Shopware.apps.Article.PremsEmotionCms.store.Emotion').load();
        me.shopStore = Ext.create('Shopware.apps.Base.store.ShopLanguage').load({});

        me.items = me.createFormPanel(),

        me.callParent(arguments);
    },

    /**
     * Creates the main form panel for the component which
     * features all neccessary form elements
     *
     * @return [object] me.formPnl - generated Ext.form.Panel
     */
    createFormPanel: function() {
        var me = this;

        // Form panel which holds off all options
        me.formPanel = Ext.create('Ext.form.Panel', {
            bodyPadding: 15,
            border: 0,
            autoScroll: true,
            defaults: {
                labelStyle: 'font-weight: 700; text-align: right;',
            },
            items: [
                me.createMainFieldset(),
                me.createFilterFieldset(),
            ],
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'bottom',
                ui: 'shopware-ui',
                cls: 'shopware-toolbar',
                items: ['->', {
                    disabled: false,
                    text: '{s name=mask/window/start}{/s}',
                    cls: 'primary',
                    handler: function(view) {
                        var panel = this.up('form');
                        var form = panel.getForm();

                        if (form.isValid()) {
                            me.fireEvent('process', view);
                        } else {
                            Ext.MessageBox.alert('Invalid Fields', 'Required fields missing');
                        }
                    },
                }],
            }],
        });

        return me.formPanel;
    },

    createMainFieldset: function() {
        var me = this;

        me.mainFieldset = Ext.create('Ext.form.FieldSet', {
            title: '{s name=mask/fieldset/base_properties/title}Basis Einstellungen{/s}',
            defaults: {
                labelWidth: 120,
                anchor: '100%'
            },
            items: [{
                xtype: 'container',
                padding: '0 0 8',
                items: [
                    me.createInfoText(),
                    // Einkaufswelt wählen
                    me.createEmotionSelection(),
                    // Shop
                    me.createShopSelection(),
                    // Position
                    me.createPositionSelection(),
                    // Type (hinzufügen/ entfernen)
                    me.createTypeSelection(),
                ],
            }],
        });

        return me.mainFieldset;
    },

    createFilterFieldset: function() {
        var me = this;

        me.mainFieldset = Ext.create('Ext.form.FieldSet', {
            title: '{s name=mask/fieldset/filter_properties/title}Filter Einstellungen{/s}',
            defaults: {
                labelWidth: 120,
                anchor: '100%'
            },
            items: [{
                xtype: 'container',
                padding: '0 0 8',
                items: [
                    // Kategorie
                    me.createCategoryFilter(),
                ],
            }],
        });

        return me.mainFieldset;
    },

    createCategoryFilter: function() {
        var me = this;

        var categoryStore = Ext.create('Shopware.apps.Article.PremsEmotionCms.store.CategoryPath');
        categoryStore.getProxy().extraParams.parents = true;
        categoryStore.load();

        var categories = Ext.create('Ext.ux.form.field.BoxSelect', {
            fieldLabel: '{s name=mask/fieldset/filter_properties/categories}Kategorien{/s}',
            forceSelection: true,
            queryMode: 'local',
            name: 'categories',
            emptyText: '{s name=mask/please_select}{/s}',
            store: categoryStore,
            valueField: 'id',
            displayField: 'name',
            width: '100%',
            margin: '10 0',
            labelWidth: 120,
        });

        return categories;
    },

    createEmotionSelection: function() {
        var me = this;

        var comboEmotion = Ext.create('Ext.form.ComboBox', {
            name: 'emotionId',
            store: me.emotionStore,
            allowBlank: false,
            forceSelection: true,
            queryMode: 'local',
            valueField: 'id',
            displayField: 'name',
            fieldLabel: '{s namespace="backend/prems_emotion_cms/article/view/grid" name=grid/field_label/emotion}{/s}',
            emptyText: '{s namespace="backend/prems_emotion_cms/article/view/grid" name=grid/empty_text/choose_emotion}{/s}',
            anchor: '100%',
        });

        return comboEmotion;
    },

    createPositionSelection: function() {
        var me = this;

        var comboPosition = Ext.create('Ext.form.ComboBox', {
            name: 'position',
            forceSelection: true,
            allowBlank: false,
            queryMode: 'local',
            valueField: 'id',
            displayField: 'name',
            fieldLabel: '{s namespace="backend/prems_emotion_cms/article/view/grid" name=grid/field_label/position}{/s}',
            emptyText: '{s namespace="backend/prems_emotion_cms/article/view/grid" name=grid/empty_text/shop}{/s}',
            store: Ext.create("Ext.data.Store",
                {
                    fields: ["id", "name"],
                    data:
                        [
                            { id: 0, name: "{s namespace='backend/prems_emotion_cms/article/view/grid' name=grid/select/position_above_article_description}{/s}" },
                            { id: 1, name: "{s namespace='backend/prems_emotion_cms/article/view/grid' name=grid/select/position_below_article_description}{/s}" },
                            { id: 2, name: "{s namespace='backend/prems_emotion_cms/article/view/grid' name=grid/select/position_own_block}{/s}" }
                        ]
                }
            ),
            anchor: '100%'
        });

        return comboPosition;
    },

    createShopSelection: function() {
        var me = this;

        var comboShop = Ext.create('Ext.form.ComboBox', {
            name: 'shopId',
            store: me.shopStore,
            forceSelection: true,
            queryMode: 'local',
            valueField: 'id',
            displayField: 'name',
            allowBlank: false,
            fieldLabel: '{s namespace="backend/prems_emotion_cms/article/view/grid" name=grid/field_label/shop}{/s}',
            emptyText: '{s namespace="backend/prems_emotion_cms/article/view/grid" name=grid/empty_text/shop}{/s}',
            anchor: '100%',
        });

        return comboShop;
    },

    createTypeSelection: function() {
        var me = this;

        var actions = Ext.create('Ext.data.Store', {
            fields: ['value', 'name'],
            data: [{
                value: '0',
                name: '{s name=mask/fieldset/base_properties/emotion_add}{/s}',
            }, {
                value: '1',
                name: '{s name=mask/fieldset/base_properties/emotion_remove}{/s}',
            }],
        });

        return Ext.create('Ext.form.field.ComboBox', {
            allowBlank: false,
            fieldLabel: '{s name=mask/fieldset/base_properties/emotion_action}Aktion{/s}',
            store: actions,
            valueField: 'value',
            displayField: 'name',
            editable: false,
            name: 'type',
            emptyText: '{s namespace="backend/prems_emotion_cms/article/view/grid" name=grid/empty_text/shop}{/s}',
        });
    },

    createInfoText: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            margin: '0 0 20 0',
            html: '<i style="color: grey" >' + '{s name=mask/fieldset/info_text}{/s}' + '</i>',
        });
    },
});

