// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/view/main/window"}
Ext.define('Shopware.apps.SwagBusinessEssentials.view.main.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.businessEssentials-list-window',
    height: 700,
    title: '{s name="MainTitle"}{/s}',

    // Necessary to properly display the template-variables
    cls: Ext.baseCSSPrefix + 'article-detail-window',

    snippets: {
        tabTitles: {
            requestManagement: '{s name="RequestManagementTab"}{/s}',
            configuration: '{s name="ConfigurationTab"}{/s}',
            privateRegister: '{s name="PrivateRegisterTab"}{/s}',
            privateShopping: '{s name="PrivateShoppingTab"}{/s}'
        },
        fieldSets: {
            templateVariables: {
                infoTitle: '{s name="TplInfoTitle"}{/s}',
                infoText: '{s name="TplInfoText"}{/s}',
                infoWarning: '{s name="TplInfoWarning"}{/s}'
            }
        },
        customerGroupCombo: {
            emptyText: '{s name="CustomerGroupComboEmpty"}{/s}',
            infoText: '{s name="CustomerGroupComboInfo"}{/s}'
        },
        saveButtonText: '{s name="SaveButtonText"}{/s}',
        controllers: {
            custom: '{s name="ControllerNameCustom"}{/s}',
            forms: '{s name="ControllerNameForms"}{/s}'
        }
    },

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.SwagBusinessEssentials.view.list.BusinessEssentials',
            listingStore: 'Shopware.apps.SwagBusinessEssentials.store.BusinessEssentials'
        };
    },

    registerEvents: function() {
        var me = this;

        me.callParent(arguments);

        me.addEvents(
            /**
             * This is fired once the user selects an entry from the customer-group combobox.
             *
             * @param { Ext.form.field.ComboBox } combo
             * @param { Array } records
             */
            'onSelectGroupCombo',

            /**
             * This is fired when the user changes the tab in the configuration tab-panel.
             *
             * @param { Ext.tab.Panel } tabPanel
             * @param { Ext.tab.Tab } newCard
             */
            'configurationTabChange',

            /**
             * This is fired when the user saves a configuration-form.
             *
             * @param { Ext.form.Panel }
             */
            'saveForm'
        );
    },

    /**
     * Creates the main-tabpanel and its sibling-containers.
     *
     * @returns { Ext.tab.Panel[] }
     */
    createItems: function() {
        var me = this;

        me.tabPanel = Ext.create('Ext.tab.Panel', {
            region: 'center',
            items: [
                me.createRequestManagementTab(),
                me.createConfigurationTab(),
                me.createTemplateVariableTab()
            ]
        });

        return [ me.tabPanel ];
    },

    /**
     * Creates the "customer-group applications"-tab.
     * It will additionally fill the tab with the grid and the filter-panel.
     *
     * @returns { Ext.container.Container }
     */
    createRequestManagementTab: function() {
        var me = this,
            items = [],
            grid = me.createGridPanel();

        grid.region = 'center';
        me.filterPanel = Ext.create('Shopware.apps.SwagBusinessEssentials.view.list.FilterPanel', {
            grid: grid,
            region: 'west',
            listingWindow: me
        });

        items.push(grid);
        items.push(me.filterPanel);

        return Ext.create('Ext.container.Container', {
            title: me.snippets.tabTitles.requestManagement,
            items: items,
            layout: 'border'
        });
    },

    /**
     * Creates the "configuration"-tab and the child tab-panel.
     *
     * @returns { Ext.tab.Panel }
     */
    createConfigurationTab: function() {
        var me = this,
            container;

        container = Ext.create('Ext.panel.Panel', {
            title: me.snippets.tabTitles.configuration,
            bodyStyle: 'background-color: #f0f2f4;',
            layout: 'fit',
            dockedItems: [ me.createToolbar() ],
            items: [ me.createConfigurationTabPanel() ]
        });

        return container;
    },

    /**
     * Creates the "private-register"-tab, which contains the related detail-form.
     *
     * @returns { Ext.container.Container }
     */
    createPrivateRegister: function() {
        var me = this,
            form, container;

        container = Ext.create('Shopware.apps.SwagBusinessEssentials.view.private_register.Detail', {
            record: Ext.create('Shopware.apps.SwagBusinessEssentials.model.PrivateRegister')
        });

        form = Ext.create('Ext.form.Panel', {
            disabled: true,
            flex: 1,
            border: false,
            overflowY: 'scroll',
            overflowX: 'hidden',
            dockedItems: [
                me.createConfigurationToolbar()
            ],
            loadRecord: function(record) {
                var assignGroupCombo = this.down('combobox[name=assignGroupBeforeUnlock]'),
                    basicForm = this.getForm(),
                    allowRegister = me.down('checkbox[name=allowRegister]');

                assignGroupCombo.store.getProxy().extraParams = {
                    customerGroup: record.get('customerGroup')
                };

                assignGroupCombo.store.load({
                    callback: function() {
                        basicForm.loadRecord(record);
                        if (record.get('customerGroup') === 'H') {
                            allowRegister.setValue(true);
                            allowRegister.setDisabled(true);
                            allowRegister.setFieldLabel('{s name="PrivateRegisterEnableTextH"}{/s}');
                        } else {
                            allowRegister.setDisabled(false);
                            allowRegister.setFieldLabel('{s name="PrivateRegisterEnableText"}{/s}');
                        }
                    }
                });
            }
        });

        form.add(container);

        return Ext.create('Ext.container.Container', {
            title: me.snippets.tabTitles.privateRegister,
            name: 'privateRegisterTab',
            layout: 'fit',
            items: [ form ]
        });
    },

    /**
     * Creates the "private-shopping"-tab, which contains the related detail-form.
     *
     * @returns { Ext.container.Container }
     */
    createPrivateShopping: function() {
        var me = this,
            container, form;

        container = Ext.create('Shopware.apps.SwagBusinessEssentials.view.private_shopping.Detail', {
            record: Ext.create('Shopware.apps.SwagBusinessEssentials.model.PrivateShopping')
        });

        form = Ext.create('Ext.form.Panel', {
            disabled: true,
            flex: 1,
            border: false,
            overflowY: 'scroll',
            overflowX: 'hidden',
            dockedItems: [
                me.createConfigurationToolbar()
            ],
            loadRecord: function(record) {
                if (record.get('id')) {
                    this.getForm().loadRecord(record);
                    return;
                }

                record.set('whiteListedControllers', [{
                    key: 'custom',
                    name: me.snippets.controllers.custom
                }, {
                    key: 'forms',
                    name: me.snippets.controllers.forms
                }]);

                this.getForm().loadRecord(record);
            }
        });

        form.add(container);

        return Ext.create('Ext.container.Container', {
            title: me.snippets.tabTitles.privateShopping,
            name: 'privateShoppingTab',
            layout: 'fit',
            items: [ form ]
        });
    },

    /**
     * Creates the "template variables"-tab and its necessary components, e.g. a grid.
     *
     * @returns { Ext.container.Container }
     */
    createTemplateVariableTab: function() {
        var me = this;

        me.templateVariableGrid = Ext.create('Shopware.apps.SwagBusinessEssentials.view.template_variables.Grid', {
            store: me.createTemplateVariableStore(),
            region: 'center'
        });

        return Ext.create('Ext.container.Container', {
            title: me.templateVariableGrid.title,
            padding: 10,
            flex: 1,
            layout: 'border',
            items: [
                {
                    xtype: 'fieldset',
                    html: me.getTemplateInfoHtml(),
                    region: 'north',
                    title: '<strong> ' + me.snippets.fieldSets.templateVariables.infoTitle + '</strong>'
                },
                me.templateVariableGrid
            ]
        });
    },

    /**
     * Creates the template-variables store.
     *
     * @returns { Shopware.apps.SwagBusinessEssentials.store.TemplateVariables }
     */
    createTemplateVariableStore: function() {
        var me = this;
        me.templateVariableStore = Ext.create('Shopware.apps.SwagBusinessEssentials.store.TemplateVariables').load();
        return me.templateVariableStore;
    },

    /**
     * Returns the info- and warning-html for the template-variables tab.
     *
     * @returns { Ext.XTemplate }
     */
    getTemplateInfoHtml: function() {
        var me = this;

        return new Ext.XTemplate('<i>',
            me.snippets.fieldSets.templateVariables.infoText,
            '</i>',
            '<br /><br />',
            '<div style="background-color: #EFBC11; padding: 15px 0; color: #FEFEFE; font-weight: 700; font-size: 14px; text-align: center; text-shadow: 0 0 5px rgba(0, 0, 0, 0.3);">',
                me.snippets.fieldSets.templateVariables.infoWarning,
            '</div>'
        );
    },

    /**
     * Creates the toolbar containing the customer-group combobox.
     *
     * @returns { Ext.toolbar.Toolbar }
     */
    createToolbar: function() {
        var me = this;

        return Ext.create('Ext.toolbar.Toolbar', {
            ui: 'shopware-ui',
            padding: '6px 10px',
            layout: 'hbox',
            items: me.createToolbarItems()
        });
    },

    /**
     * Creates the items for the toolbar.
     * In this case a customer-group combobox and an info-text are created.
     *
     * @returns { Array }
     */
    createToolbarItems: function() {
        var me = this;

        return [
            me.createGroupCombo(),
            me.createToolbarInfoCt()
        ];
    },

    /**
     * Creates the customer-group combobox itself.
     * A base-story is used for loading the data.
     * Additionally we add a custom-listener to load a record into the open form.
     *
     * @returns { Ext.form.field.ComboBox }
     */
    createGroupCombo: function() {
        var me = this;

        me.configurationGroupCombo = Ext.create('Shopware.form.field.PagingComboBox', {
            store: me.createCustomerGroupsComboStore(),
            name: 'customerGroupCombo',
            width: 200,
            valueField: 'id',
            displayField: 'name',
            emptyText: me.snippets.customerGroupCombo.emptyText,
            flex: 1,
            listeners: {
                select: function(combo, records) {
                    me.fireEvent('onSelectGroupCombo', combo, records);
                }
            }
        });

        return me.configurationGroupCombo;
    },

    /**
     * Creates the info-container for the toolbar.
     *
     * @returns { Ext.container.Container }
     */
    createToolbarInfoCt: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            style: {
                color: '#61677f',
                fontStyle: 'italic'
            },
            margin: '0 0 0 10px',
            html: me.snippets.customerGroupCombo.infoText,
            flex: 4
        });
    },

    /**
     * Creates the tab-panel inside of the configuration-tab.
     *
     * @returns { Ext.tab.Panel }
     */
    createConfigurationTabPanel: function() {
        var me = this;

        me.configurationTabPanel = Ext.create('Ext.tab.Panel', {
            name: 'configurationTabPanel',
            deferredRender: true,
            flex: 1,
            padding: 10,
            plain: true,
            items: [
                me.createPrivateRegister(),
                me.createPrivateShopping()
            ],
            listeners: {
                tabchange: function(tabPanel, newCard) {
                    me.fireEvent('configurationTabChange', tabPanel, newCard);
                }
            }
        });

        return me.configurationTabPanel;
    },

    /**
     * Creates the save-button for the configuration-tabs.
     *
     * @returns { Ext.button.Button }
     */
    createConfigurationSaveButton: function() {
        var me = this;

        return Ext.create('Ext.button.Button', {
            text: me.snippets.saveButtonText,
            cls: 'primary',
            handler: function(button) {
                me.fireEvent('saveForm', button.up('form'));
            }
        });
    },

    /**
     * Creates the bottom toolbar for the configuration-forms.
     *
     * @returns { Ext.toolbar.Toolbar }
     */
    createConfigurationToolbar: function() {
        return Ext.create('Ext.toolbar.Toolbar', {
            dock: 'bottom',
            items: [
                '->',
                this.createConfigurationSaveButton()
            ]
        });
    },

    /**
     * Creates a store which reads all customer group data.
     *
     * @returns { Ext.data.Store }
     */
    createCustomerGroupsComboStore: function() {
        return Ext.create('Ext.data.Store', {
            model: 'Shopware.model.Dynamic',
            proxy: {
                type: 'ajax',
                url: '{url controller="SwagBusinessEssentials" action="getCustomerGroups"}',
                reader: Ext.create('Shopware.model.DynamicReader')
            }
        });
    }
});
// {/block}
