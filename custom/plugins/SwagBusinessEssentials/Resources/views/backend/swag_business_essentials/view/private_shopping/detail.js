// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/view/private_shopping/detail"}
Ext.define('Shopware.apps.SwagBusinessEssentials.view.private_shopping.Detail', {
    extend: 'Shopware.model.Container',
    alias: 'widget.business_essentials-private_shopping-detail',
    padding: '10px 20px',

    snippets: {
        helpText: {
            privateShoppingLoginRedirectHelpText: '{s name="PrivateShoppingLoginRedirectHelpText"}{/s}',
            privateShoppingRegisterRedirectHelpText: '{s name="PrivateShoppingRegisterRedirectHelpText"}{/s}'
        },
        fieldSets: {
            description: {
                infoText: '{s name="PrivateShoppingInfoText"}{/s}',
                title: '{s name="ConfigurationDescriptionTitle"}{/s}',
                activateLogin: '{s name="PrivateShoppingActivateLoginLabel"}{/s}',
                warningText: '{s name="PrivateShoppingWarning"}{/s}'
            },
            configuration: {
                title: '{s name="ConfigurationMainTitle"}{/s}',
                showRegistration: '{s name="PrivateShoppingOfferRegisterLabel"}{/s}',
                usedRegisterConfig: '{s name="PrivateShoppingRegisterConfigLabel"}{/s}',
                unlockRegister: '{s name="PrivateShoppingUnlockLabel"}{/s}'
            },
            template: {
                title: '{s name="ConfigurationTemplateTitle"}{/s}',
                loginThemeEmpty: '{s name="PrivateShoppingLoginThemeEmpty"}{/s}',
                loginThemeLabel: '{s name="PrivateShoppingLoginThemeLabel"}{/s}',
                loginTemplateLabel: '{s name="PrivateShoppingLoginTemplateLabel"}{/s}',
                loginTemplateSupport: '{s name="PrivateShoppingLoginTemplateSupport"}{/s}'
            },
            advanced: {
                title: '{s name="ConfigurationExtendedTitle"}{/s}',
                redirectLoginLabel: '{s name="PrivateShoppingRedirectLoginLabel"}{/s}',
                redirectRegisterLabel: '{s name="PrivateShoppingRedirectRegisterLabel"}{/s}',
                whiteListTitle: '{s name="PrivateShoppingWhiteListTitle"}{/s}',
                whiteListInfo: '{s name="PrivateShoppingWhiteListInfo"}{/s}',
                whiteListComboLabel: '{s name="PrivateShoppingWhiteListComboLabel"}{/s}',
                whiteListGridControllerHeader: '{s name="PrivateShoppingWhiteListGridControllerHeader"}{/s}',
                whiteListGridNameHeader: '{s name="PrivateShoppingWhiteListGridNameHeader"}{/s}',
                whiteListAddBtnText: '{s name="PrivateShoppingWhiteListAddText"}{/s}'
            }
        }
    },

    fieldDefaults: {
        labelWidth: 180,
        labelStyle: 'margin: 0',
        labelPad: 20
    },

    configure: function() {
        var me = this;
        return {
            fieldSets: [
                Ext.bind(me.createDescriptionFieldSet, me),
                Ext.bind(me.createConfigurationFieldSet, me),
                Ext.bind(me.createTemplateFieldSet, me),
                Ext.bind(me.createAdvancedFieldSet, me)
            ]
        };
    },

    /**
     * Creates the description-fieldset with info-text and a boolean-field to enable the private-register function.
     *
     * @returns { Object }
     */
    createDescriptionFieldSet: function() {
        var me = this;

        return {
            xtype: 'fieldset',
            title: me.snippets.fieldSets.description.title,
            name: 'description',
            items: [
                me.createWarningCt(),
                me.createIdField(),
                me.createCustomerGroupField(),
                me.createInfoContainer(),
                me.createActivateLoginField()
            ]
        };
    },

    /**
     * Creates a warning container to explain why some settings get disabled sometimes.
     */
    createWarningCt: function() {
        return Ext.create('Ext.container.Container', {
            name: 'warningCt',
            hidden: true,
            items: [
                Shopware.Notification.createBlockMessage(this.snippets.fieldSets.description.warningText, 'notice')
            ]
        });
    },

    /**
     * Creates a hidden id field to properly load the id into the form.
     *
     * @returns { Ext.form.field.Hidden }
     */
    createIdField: function() {
        return Ext.create('Ext.form.field.Hidden', {
            name: 'id'
        });
    },

    /**
     * Creates a hidden customerGroup field to properly load the customerGroup into the form.
     *
     * @returns { Ext.form.field.Hidden }
     */
    createCustomerGroupField: function() {
        return Ext.create('Ext.form.field.Hidden', {
            name: 'customerGroup'
        });
    },

    /**
     * Creates the configuration field-set.
     *
     * @returns { Object }
     */
    createConfigurationFieldSet: function() {
        var me = this;

        return {
            xtype: 'fieldset',
            name: 'configuration',
            title: me.snippets.fieldSets.configuration.title,
            layout: 'column',
            items: [
                me.createRegisterLinkField(),
                me.createRegisterGroupField(),
                me.createUnlockAfterRegisterField()
            ]
        };
    },

    /**
     * Creates the template field-set.
     *
     * @returns { Object }
     */
    createTemplateFieldSet: function() {
        var me = this;

        return {
            xtype: 'fieldset',
            title: me.snippets.fieldSets.template.title,
            layout: 'column',
            items: [
                me.createTemplateAfterLoginField(),
                me.createTemplateLoginField()
            ]
        };
    },

    /**
     * Creates the advanced field-set.
     *
     * @returns { Object }
     */
    createAdvancedFieldSet: function() {
        var me = this;

        return {
            itemId: 'advancedFieldSet',
            disabled: true,
            xtype: 'fieldset',
            title: me.snippets.fieldSets.advanced.title,
            layout: 'column',
            collapsible: true,
            collapsed: true,
            items: [
                me.createRedirectLoginField(),
                me.createRedirectLoginParamsField(),
                me.createRedirectRegistrationField(),
                me.createRedirectRegistrationParamsField(),
                me.createWhiteListFieldSet()
            ]
        };
    },

    /**
     * @returns { Shopware.apps.SwagBusinessEssentials.view.components.InfoContainer }
     */
    createInfoContainer: function() {
        return Ext.create('Shopware.apps.SwagBusinessEssentials.view.components.InfoContainer', {
            html: this.snippets.fieldSets.description.infoText
        });
    },

    /**
     * @returns { Ext.form.field.Checkbox }
     */
    createActivateLoginField: function() {
        return Ext.create('Ext.form.field.Checkbox', {
            name: 'activateLogin',
            fieldLabel: this.snippets.fieldSets.description.activateLogin,
            labelWidth: 265,
            inputValue: true,
            uncheckedValue: false,
            margin: '5px 0 0',
            toggleEnable: true,
            listeners: {
                change: Ext.bind(this.onActivateLoginFieldChange, this)
            }
        });
    },

    /**
     * @param { Ext.form.field.Checkbox } element
     * @param { bool } value
     */
    onActivateLoginFieldChange: function (element, value) {
        var me = this,
            registerParamsField = me.down('#register_params_field'),
            loginParamsField = me.down('#login_params_field'),
            whitelistContainer = me.down('#whiteListContainer'),
            advancedFieldSet = me.down('#advancedFieldSet');

        advancedFieldSet.setDisabled(!value);
        loginParamsField.setDisabled(!value);
        registerParamsField.setDisabled(!value);
        whitelistContainer.setDisabled(!value);

        if (value) {
            advancedFieldSet.expand();
        } else {
            advancedFieldSet.collapse();
        }
    },

    /**
     * @returns { Ext.form.field.Checkbox }
     */
    createRegisterLinkField: function() {
        var me = this;

        return Ext.create('Ext.form.field.Checkbox', {
            fieldLabel: me.snippets.fieldSets.configuration.showRegistration,
            name: 'registerLink',
            inputValue: true,
            uncheckedValue: false,
            labelWidth: me.fieldDefaults.labelWidth,
            labelStyle: me.fieldDefaults.labelStyle,
            labelPad: me.fieldDefaults.labelPad,
            columnWidth: 0.35,
            toggleEnable: true
        });
    },

    /**
     * @returns { Ext.form.field.ComboBox }
     */
    createRegisterGroupField: function() {
        var me = this,
            offerRegistrationField;

        return Ext.create('Shopware.form.field.PagingComboBox', {
            name: 'registerGroup',
            store: Ext.create('Shopware.apps.SwagBusinessEssentials.store.RegisterTemplates').load(),
            displayField: 'name',
            valueField: 'key',
            fieldLabel: me.snippets.fieldSets.configuration.usedRegisterConfig,
            labelWidth: me.fieldDefaults.labelWidth,
            labelPad: me.fieldDefaults.labelPad,
            columnWidth: 0.65,
            queryCaching: false,
            toggleEnable: true,
            validator: function(value) {
                offerRegistrationField = me.down('checkbox[name=registerLink]');

                if (!offerRegistrationField.getValue()) {
                    return true;
                }

                return !!value;
            }
        });
    },

    /**
     * @returns { Ext.form.field.Checkbox }
     */
    createUnlockAfterRegisterField: function() {
        var me = this;

        return Ext.create('Ext.form.field.Checkbox', {
            fieldLabel: me.snippets.fieldSets.configuration.unlockRegister,
            name: 'unlockAfterRegister',
            inputValue: true,
            uncheckedValue: false,
            labelWidth: me.fieldDefaults.labelWidth,
            labelStyle: me.fieldDefaults.labelStyle,
            labelPad: me.fieldDefaults.labelPad,
            margin: '15px 0',
            columnWidth: 1,
            toggleEnable: true
        });
    },

    /**
     * @returns { Ext.form.field.ComboBox }
     */
    createTemplateAfterLoginField: function() {
        var me = this;

        return Ext.create('Ext.form.field.ComboBox', {
            emptyText: me.snippets.fieldSets.template.loginThemeEmpty,
            name: 'templateAfterLogin',
            store: Ext.create('Shopware.apps.Base.store.Template').load(),
            forceSelection: true,
            displayField: 'template',
            valueField: 'id',
            fieldLabel: me.snippets.fieldSets.template.loginThemeLabel,
            columnWidth: 1,
            labelWidth: me.fieldDefaults.labelWidth,
            labelPad: me.fieldDefaults.labelPad,
            labelStyle: me.fieldDefaults.labelStyle,
            // Enable removing the value
            beforeBlur: function() {
                var value = this.getRawValue();
                if (value === '') {
                    this.lastSelection = [];
                }
                this.doQueryTask.cancel();
                this.assertValue();
            }
        });
    },

    /**
     * @returns { Ext.form.field.Text }
     */
    createTemplateLoginField: function() {
        var me = this;

        return Ext.create('Ext.form.field.Text', {
            name: 'templateLogin',
            emptyText: 'pslogin.tpl',
            fieldLabel: me.snippets.fieldSets.template.loginTemplateLabel,
            labelWidth: me.fieldDefaults.labelWidth,
            labelPad: me.fieldDefaults.labelPad,
            labelStyle: me.fieldDefaults.labelStyle,
            supportText: me.snippets.fieldSets.template.loginTemplateSupport,
            margin: '15px 3px 0 0',
            columnWidth: 1,
            toggleEnable: true
        });
    },

    /**
     * @returns { Ext.form.field.Text }
     */
    createRedirectLoginField: function() {
        var me = this;

        return Ext.create('Ext.form.field.Text', {
            name: 'loginControllerAction',
            emptyText: 'account/index',
            fieldLabel: me.snippets.fieldSets.advanced.redirectLoginLabel,
            margin: '0 20px 0 0',
            labelWidth: me.fieldDefaults.labelWidth,
            labelStyle: me.fieldDefaults.labelStyle,
            labelPad: me.fieldDefaults.labelPad,
            helpText: me.snippets.helpText.privateShoppingLoginRedirectHelpText,
            layout: 'anchor',
            columnWidth: 0.5
        });
    },

    /**
     * @returns { Shopware.apps.SwagBusinessEssentials.view.components.ParamsField }
     */
    createRedirectLoginParamsField: function() {
        var me = this;

        return Ext.create('Shopware.apps.SwagBusinessEssentials.view.components.ParamsField', {
            name: 'loginParams',
            disabled: true,
            columnWidth: 0.5,
            labelWidth: me.fieldDefaults.labelWidth,
            labelStyle: me.fieldDefaults.labelStyle,
            labelPad: me.fieldDefaults.labelPad,
            fieldLabel: '{s name="PrivateShoppingLoginParamsLabel"}Additional parameters for the (login-)redirection{/s}',
            itemId: 'login_params_field'
        });
    },

    /**
     * @returns { Shopware.apps.SwagBusinessEssentials.view.components.ParamsField }
     */
    createRedirectRegistrationParamsField: function() {
        var me = this;

        return Ext.create('Shopware.apps.SwagBusinessEssentials.view.components.ParamsField', {
            name: 'registerParams',
            disabled: true,
            columnWidth: 0.5,
            labelWidth: me.fieldDefaults.labelWidth,
            labelStyle: me.fieldDefaults.labelStyle,
            labelPad: me.fieldDefaults.labelPad,
            fieldLabel: '{s name="PrivateShoppingRegisterParamsLabel"}Additional parameters for the (register-)redirection{/s}',
            itemId: 'register_params_field',
            style: {
                marginTop: '15px'
            }
        });
    },

    /**
     * @returns { Ext.form.FieldSet }
     */
    createWhiteListFieldSet: function() {
        var me = this;

        return Ext.create('Ext.form.FieldSet', {
            title: me.snippets.fieldSets.advanced.whiteListTitle,
            columnWidth: 1,
            height: 190,
            margin: '15px 0 0',
            layout: 'hbox',
            items: [
                me.createWhiteListLeftCt(),
                me.createWhiteListRightCt()
            ]
        });
    },

    /**
     * @returns { Ext.form.field.Text }
     */
    createRedirectRegistrationField: function() {
        var me = this;

        return Ext.create('Ext.form.field.Text', {
            name: 'registerControllerAction',
            emptyText: 'account/index',
            fieldLabel: me.snippets.fieldSets.advanced.redirectRegisterLabel,
            labelWidth: me.fieldDefaults.labelWidth,
            labelStyle: me.fieldDefaults.labelStyle,
            labelPad: me.fieldDefaults.labelPad,
            helpText: me.snippets.helpText.privateShoppingRegisterRedirectHelpText,
            layout: 'anchor',
            margin: '15px 20px 0 0',
            columnWidth: 0.5
        });
    },

    /**
     * @returns { Ext.container.Container }
     */
    createWhiteListLeftCt: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            layout: 'anchor',
            margin: '0 20px 0 0',
            flex: 1,
            items: [
                me.createWhiteListInfoCt(),
                me.createWhiteListCombo(),
                me.createAddControllerBtn()
            ]
        });
    },

    /**
     * @returns { Ext.container.Container }
     */
    createWhiteListRightCt: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            itemId: 'whiteListContainer',
            disabled: true,
            flex: 1,
            layout: 'fit',
            height: '100%',
            items: [
                me.createWhiteListGrid()
            ]
        });
    },

    /**
     * @returns { Ext.container.Container }
     */
    createWhiteListInfoCt: function() {
        var me = this;

        return Ext.create('Ext.container.Container', {
            html: me.snippets.fieldSets.advanced.whiteListInfo,
            style: {
                fontStyle: 'italic',
                marginRight: '15px'
            }
        });
    },

    /**
     * Creates the combo-box which contains all frontend controllers.
     * Additionally enables the "add button" on selecting an entry.
     *
     * @returns { Ext.form.field.ComboBox }
     */
    createWhiteListCombo: function() {
        var me = this;

        return Ext.create('Ext.form.field.ComboBox', {
            anchor: '100%',
            margin: '20px 0 0',
            itemId: 'controllerCombo',
            fieldLabel: me.snippets.fieldSets.advanced.whiteListComboLabel,
            store: Ext.create('Shopware.apps.SwagBusinessEssentials.store.Controllers'),
            displayField: 'name',
            valueField: 'key',
            listeners: {
                change: Ext.bind(me.onSelectCombo, me)
            },
            toggleEnable: true
        });
    },

    /**
     * @returns { Shopware.apps.SwagBusinessEssentials.view.components.GridField }
     */
    createWhiteListGrid: function() {
        var me = this;

        return Ext.create('Shopware.apps.SwagBusinessEssentials.view.components.GridField', {
            title: me.snippets.fieldSets.advanced.whiteListComboLabel,
            store: Ext.create('Ext.data.Store', { model: 'Shopware.apps.SwagBusinessEssentials.model.Controllers' }),
            columns: [
                {
                    header: me.snippets.fieldSets.advanced.whiteListGridControllerHeader,
                    dataIndex: 'key',
                    flex: 2
                }, {
                    header: me.snippets.fieldSets.advanced.whiteListGridNameHeader,
                    dataIndex: 'name',
                    flex: 4
                }, {
                    xtype: 'actioncolumn',
                    flex: 1,
                    items: [
                        this.createDeleteControllerButton()
                    ]
                }
            ],
            toggleEnable: true
        });
    },

    /**
     * Creates the "add" button to add a controller into the grid-field.
     * Additionally handles adding the currently selected controller from the dropdown into the grid.
     *
     * @returns { Ext.button.Button }
     */
    createAddControllerBtn: function() {
        var me = this;

        return Ext.create('Ext.button.Button', {
            text: me.snippets.fieldSets.advanced.whiteListAddBtnText,
            itemId: 'addControllerBtn',
            cls: 'primary',
            disabled: true,
            style: {
                float: 'right',
                marginTop: '10px'
            },
            handler: Ext.bind(me.addController, me)
        });
    },

    /**
     * Creates the "delete" button for the action column of the grid-field.
     * Additionally handles its own action and removes a record from the grid store.
     *
     * @returns { Ext.button.Button }
     */
    createDeleteControllerButton: function() {
        var me = this;

        return Ext.create('Ext.button.Button', {
            iconCls: 'sprite-minus-circle-frame',
            handler: Ext.bind(me.deleteController, me)
        });
    },

    /**
     * Helper method to enable the "add"-button when selecting an entry
     *
     * @param { Ext.form.field.ComboBox } combo
     */
    onSelectCombo: function(combo) {
        var btn,
            val = combo.getValue();

        btn = combo.up('form').down('#addControllerBtn');
        btn.setDisabled(!val);
    },

    /**
     * Helper method to add a controller selected in the controller whitelist to the grid.
     *
     * @param { Ext.button.Button } btn
     */
    addController: function(btn) {
        var form, gridField, comboBox, selectedRecord;

        form = btn.up('form');
        gridField = form.down('#grid_field');
        comboBox = form.down('#controllerCombo');

        selectedRecord = comboBox.findRecordByValue(comboBox.getValue());

        if (!selectedRecord) {
            selectedRecord = Ext.create('Shopware.apps.SwagBusinessEssentials.model.Controllers', {
                name: '',
                key: comboBox.getValue()
            });
        }

        gridField.addRecord(selectedRecord);
        comboBox.reset();
        btn.setDisabled(true);
    },

    /**
     * Delete a controller from the grid.
     *
     * @param { Ext.grid.View } gridView
     * @param { int } rowIndex
     * @param { int } colIndex
     * @param { Ext.button.Button } btn
     * @param { Event } event
     * @param { Ext.data.Model } record
     */
    deleteController: function(gridView, rowIndex, colIndex, btn, event, record) {
        var form, gridField;

        form = gridView.up('form');
        gridField = form.down('#grid_field');

        gridField.store.remove(record);
    }
});
// {/block}
