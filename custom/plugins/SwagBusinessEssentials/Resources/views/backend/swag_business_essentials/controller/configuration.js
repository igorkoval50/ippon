// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/controller/configuration"}
Ext.define('Shopware.apps.SwagBusinessEssentials.controller.Configuration', {
    extend: 'Enlight.app.Controller',

    snippets: {
        growl: {
            title: '{s name="ConfigurationGrowlTitle"}{/s}',
            message: '{s name="ConfigurationGrowlMessage"}{/s}',
            caller: '{s name="MainTitle"}{/s}'
        }
    },

    refs: [
        { ref: 'customerGroupCombo', selector: 'combobox[name=customerGroupCombo]' },
        { ref: 'configurationTabPanel', selector: 'tabpanel[name=configurationTabPanel]' },
        { ref: 'warningContainer', selector: 'container[name=warningCt]' }
    ],

    init: function() {
        var me = this;

        me.control({
            'businessEssentials-list-window': {
                saveForm: me.onSave,
                configurationTabChange: me.onChangeTab,
                onSelectGroupCombo: me.onSelect
            }
        });
    },

    /**
     * This is called when saving either a private-register- or a private-shopping-form.
     * It sets the customer-group from the customer-group--dropdown into the record.
     *
     * @param { Ext.form.Panel } formCt
     */
    onSave: function(formCt) {
        var me = this,
            basicForm = formCt.getForm(),
            detailRecord = basicForm.getRecord(),
            customerGroupCombo,
            record,
            fieldValues = basicForm.getFieldValues();

        basicForm.updateRecord(detailRecord);

        // Reset to default values, if the corresponding field is empty. This avoids Shopware to jump back to the old value.
        detailRecord.set('registerGroup', fieldValues['registerGroup'] ? fieldValues['registerGroup'] : '');
        detailRecord.set('templateAfterLogin', fieldValues['templateAfterLogin'] ? fieldValues['templateAfterLogin'] : 0);
        detailRecord.set('emailTemplateDeny', fieldValues['emailTemplateDeny'] ? fieldValues['emailTemplateDeny'] : '');
        detailRecord.set('emailTemplateAllow', fieldValues['emailTemplateAllow'] ? fieldValues['emailTemplateAllow'] : '');

        me.updateAssociations(formCt, detailRecord);

        formCt.setLoading(true);

        if (!basicForm.isValid()) {
            formCt.setLoading(false);
            return;
        }

        detailRecord.save({
            success: function() {
                formCt.setLoading(false);
                customerGroupCombo = me.getCustomerGroupCombo();
                record = customerGroupCombo.findRecord('id', customerGroupCombo.getValue());

                me.loadTabRecord(formCt, record, detailRecord.$className);

                Shopware.Notification.createGrowlMessage(
                    me.snippets.growl.title,
                    me.snippets.growl.message,
                    me.snippets.growl.caller
                );
            }
        });
    },

    /**
     * This is called when the user selects an entry from the customer-group combobox.
     * It loads a configuration-record depending on the selected customer-group and active tab.
     *
     * @param { Ext.form.field.ComboBox } combo
     * @param { Array } records
     */
    onSelect: function(combo, records) {
        var me = this,
            activeTab, container, form;

        if (~~(records[0].get('isMain'))) {
            me.disablePrivateRegisterTab();
            me.enablePrivateShoppingFields();
        } else {
            me.enablePrivateRegisterTab();
            me.disablePrivateShoppingFields();
        }

        activeTab = me.getConfigurationTabPanel().getActiveTab();

        form = activeTab.child('form');
        container = form.child('container');

        form.setDisabled(records.length <= 0);
        if (records.length > 0) {
            me.loadTabRecord(form, records[0], container.record.$className);
        }
    },

    /**
     * This is called whenever the tab in the configuration tab-panel is changed.
     * It triggers loading a new record, if an entry in the customer-group combobox was also selected before.
     *
     * @param { Ext.tab.Panel } tabPanel
     * @param { Ext.tab.Tab } newCard
     */
    onChangeTab: function(tabPanel, newCard) {
        var me = this,
            form, container, record, configurationGroupCombo;

        if (!me.groupComboHasValue()) {
            return;
        }

        configurationGroupCombo = me.getCustomerGroupCombo();

        record = configurationGroupCombo.findRecord('id', configurationGroupCombo.getValue());
        form = newCard.child('form');
        container = form.child('container');

        form.setDisabled(false);
        me.loadTabRecord(form, record, container.record.$className);
    },

    /**
     * Checks if the customer-group combobox has a value selected.
     *
     * @returns { boolean }
     */
    groupComboHasValue: function() {
        var me = this;

        return !!me.getCustomerGroupCombo().getValue();
    },

    /**
     * Loads the first empty-record into a configuration-tab.
     *
     * @param { Ext.form.Panel } form
     * @param { Shopware.apps.Base.model.CustomerGroup } customerGroup
     * @param { string } modelName
     */
    loadTabRecord: function(form, customerGroup, modelName) {
        var record = Ext.create(modelName, {
            customerGroup: customerGroup.get('key')
        });

        record.reload({
            params: {
                customerGroup: customerGroup.get('key')
            },
            callback: function(detailRecord) {
                if (!detailRecord) {
                    detailRecord = record;
                }
                detailRecord.set('customerGroup', customerGroup.get('key'));

                // Customer group H has "requireunlock" set on default. If no id is set, we need to set it to true.
                if (detailRecord.get('customerGroup') === 'H' && !detailRecord.get('id')) {
                    detailRecord.set('requireUnlock', true);
                }

                form.child('container').record = detailRecord;
                form.loadRecord(detailRecord);
            }
        });
    },

    /**
     * Enables the private-register tab.
     */
    enablePrivateRegisterTab: function() {
        var me = this,
            tabPanel = me.getConfigurationTabPanel(),
            privateRegisterTab = tabPanel.child('container[name=privateRegisterTab]');

        privateRegisterTab.setDisabled(false);
    },

    /**
     * Disabled the private-register tab and automatically switches to the private-shopping tab.
     */
    disablePrivateRegisterTab: function() {
        var me = this,
            tabPanel = me.getConfigurationTabPanel(),
            privateShoppingTab = tabPanel.child('container[name=privateShoppingTab]'),
            privateRegisterTab = tabPanel.child('container[name=privateRegisterTab]');

        privateRegisterTab.setDisabled(true);
        tabPanel.setActiveTab(privateShoppingTab);
    },

    /**
     * Updates the given associations.
     *
     * @param { Ext.form.Panel } form
     * @param { Ext.data.Model } detailRecord
     */
    updateAssociations: function(form, detailRecord) {
        var me = this,
            gridField = form.down('#grid_field'),
            loginParamsField = form.down('#login_params_field'),
            registerParamsField = form.down('#register_params_field');

        if (gridField) {
            detailRecord['getWhiteListedControllersStore'] = gridField.getStore();
        }

        if (loginParamsField) {
            detailRecord['getLoginParamsStore'] = me.createStoreFromValues(loginParamsField.getValue());
        }

        if (registerParamsField) {
            detailRecord['getRegisterParamsStore'] = me.createStoreFromValues(registerParamsField.getValue());
        }
    },

    /**
     * Creates a store from the given values.
     *
     * @param { Object[] } values
     * @returns { Ext.data.Store }
     */
    createStoreFromValues: function(values) {
        return Ext.create('Ext.data.Store', {
            data: values || [],
            model: 'Shopware.apps.SwagBusinessEssentials.model.Params'
        });
    },

    /**
     * Enables all fields with the property "toggleEnable = true"
     */
    enablePrivateShoppingFields: function() {
        var me = this;

        me.toggleEnableOnFields(true);
        me.getWarningContainer().hide();
    },

    /**
     * Disables all fields with the property "toggleEnable = true"
     */
    disablePrivateShoppingFields: function() {
        var me = this;

        me.toggleEnableOnFields(false);
        me.getWarningContainer().show();
    },

    /**
     * Toggles the enabled state of the fields with the property "toggleEnable = true" due to the given parameter.
     *
     * @param { boolean } enable
     */
    toggleEnableOnFields: function(enable) {
        var me = this,
            fields = me.getPrivateShoppingToggleList();

        Ext.each(fields, function(item) {
            item.setDisabled(!enable);
        });
    },

    /**
     * Returns all fields with the property "toggleEnable = true".
     *
     * @returns { Ext.form.field.Base[] }
     */
    getPrivateShoppingToggleList: function() {
        var me = this,
            tabPanel = me.getConfigurationTabPanel(),
            privateShoppingTab = tabPanel.child('container[name=privateShoppingTab]');

        return privateShoppingTab.query('*[toggleEnable=true]');
    }
});
// {/block}
