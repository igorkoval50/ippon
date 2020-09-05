// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/view/private_register/detail"}
Ext.define('Shopware.apps.SwagBusinessEssentials.view.private_register.Detail', {
    extend: 'Shopware.model.Container',
    alias: 'widget.business_essentials-private_register-detail',
    padding: '10px 20px',

    snippets: {
        fieldSets: {
            description: {
                title: '{s name="ConfigurationDescriptionTitle"}{/s}',
                text: '{s name="PrivateRegisterDescription"}{/s}',
                allowRegisterLabel: '{s name="PrivateRegisterEnableText"}{/s}'
            },
            configuration: {
                title: '{s name="ConfigurationMainTitle"}{/s}',
                requireUnlockLabel: '{s name="PrivateRegisterRequireUnlockLabel"}{/s}',
                assignGroupBeforeUnlockLabel: '{s name="PrivateRegisterAssignGroupLabel"}{/s}',
                assignGroupBeforeUnlockEmpty: '{s name="CustomerGroupComboEmpty"}{/s}',
                registerTemplateLabel: '{s name="PrivateRegisterTemplateLabel"}{/s}',
                registerTemplateSupport: '{s name="PrivateRegisterTemplateSupport"}{/s}',
                registerLinkLabel: '{s name="PrivateRegisterLinkLabel"}{/s}',
                registerLinkSupport: '{s name="PrivateRegisterLinkSupport"}{/s}',
                linkEmptyText: '{s name="LinkEmptyText"}{/s}'
            },
            extended: {
                title: '{s name="ConfigurationExtendedTitle"}{/s}',
                emailTemplateAllowLabel: '{s name="PrivateRegisterTemplateAllowLabel"}{/s}',
                emailTemplateDenyLabel: '{s name="PrivateRegisterTemplateDenyLabel"}{/s}',
                emailTemplateAllowEmptyText: '{s name="PrivateRegisterTemplateAllowEmpty"}{/s}',
                emailTemplateDenyEmptyText: '{s name="PrivateRegisterTemplateDenyEmpty"}{/s}'
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
            items: [
                me.createIdField(),
                me.createCustomerGroupField(),
                me.createInfoContainer(),
                me.createAllowRegisterField()
            ]
        };
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
     * Creates the main configuration-fieldset.
     *
     * @returns { Object }
     */
    createConfigurationFieldSet: function() {
        var me = this;

        return {
            xtype: 'fieldset',
            title: me.snippets.fieldSets.configuration.title,
            layout: 'column',
            items: [
                me.createRequireUnlockField(),
                me.createLinkToRegisterField(),
                me.createAssignGroupBeforeUnlockField(),
                me.createRegisterTemplateField()
            ]
        };
    },

    /**
     * Creates a field-set containing the advanced settings. In this case it only contains the mail-templates.
     * @returns { Object }
     */
    createAdvancedFieldSet: function() {
        var me = this;

        return {
            xtype: 'fieldset',
            title: me.snippets.fieldSets.extended.title,
            layout: 'column',
            collapsible: true,
            collapsed: true,
            items: [
                me.createMailFieldAllow(),
                me.createMailFieldDeny()
            ]
        };
    },

    /**
     * Creates a mail-combobox depending on the given parameters.
     *
     * @param { string } fieldName
     * @param { string } label
     * @param { Object } opts
     * @returns { Ext.form.field.ComboBox }
     */
    createMailField: function(fieldName, label, opts) {
        var defaults = {
            store: Ext.create('Shopware.apps.SwagBusinessEssentials.store.Mails'),
            name: fieldName,
            columnWidth: 1,
            fieldLabel: label,
            labelWidth: 180,
            labelPad: 20,
            valueField: 'name',
            displayField: 'name'
        };

        return Ext.create('Ext.form.field.ComboBox', Ext.apply({}, opts, defaults));
    },

    /**
     * Renders the link-field as an anchor-tag.
     *
     * @param { string } value
     * @returns { string }
     */
    renderLink: function(value) {
        var me = this;

        if (!value) {
            return '<span style="color: #475c6a;">' + me.snippets.fieldSets.configuration.linkEmptyText + '</span>';
        }
        return '<a style="color: #009cff;" target="blank" href="' + me.record.get('link') + '">' + value + '</a>';
    },

    /**
     * @returns { Shopware.apps.SwagBusinessEssentials.view.components.InfoContainer }
     */
    createInfoContainer: function() {
        return Ext.create('Shopware.apps.SwagBusinessEssentials.view.components.InfoContainer', {
            html: this.snippets.fieldSets.description.text
        });
    },

    /**
     * @returns { Ext.form.field.Checkbox }
     */
    createAllowRegisterField: function() {
        return Ext.create('Ext.form.field.Checkbox', {
            name: 'allowRegister',
            disabled: true,
            fieldLabel: this.snippets.fieldSets.description.allowRegisterLabel,
            labelWidth: 440,
            inputValue: true,
            uncheckedValue: false,
            margin: '5px 0 0'
        });
    },

    /**
     * @returns { Ext.form.field.Checkbox }
     */
    createRequireUnlockField: function() {
        var me = this;

        return Ext.create('Ext.form.field.Checkbox', {
            name: 'requireUnlock',
            inputValue: true,
            uncheckedValue: false,
            fieldLabel: me.snippets.fieldSets.configuration.requireUnlockLabel,
            columnWidth: 0.35,
            labelWidth: me.fieldDefaults.labelWidth,
            labelStyle: me.fieldDefaults.labelStyle,
            labelPad: me.fieldDefaults.labelPad
        });
    },

    /**
     * @returns { Ext.form.field.Display }
     */
    createLinkToRegisterField: function() {
        var me = this;

        return Ext.create('Ext.form.field.Display', {
            fieldLabel: me.snippets.fieldSets.configuration.registerLinkLabel,
            name: 'displayLink',
            columnWidth: 0.65,
            renderer: Ext.bind(me.renderLink, me),
            supportText: me.snippets.fieldSets.configuration.registerLinkSupport,
            labelWidth: me.fieldDefaults.labelWidth,
            labelStyle: me.fieldDefaults.labelStyle
        });
    },

    /**
     * @returns { Ext.form.field.ComboBox }
     */
    createAssignGroupBeforeUnlockField: function() {
        var me = this;

        return Ext.create('Shopware.form.field.PagingComboBox', {
            name: 'assignGroupBeforeUnlock',
            store: me.createAssignGroupStore(),
            valueField: 'key',
            displayField: 'name',
            columnWidth: 1,
            margin: '20px 0 0',
            fieldLabel: me.snippets.fieldSets.configuration.assignGroupBeforeUnlockLabel,
            emptyText: me.snippets.fieldSets.configuration.assignGroupBeforeUnlockEmpty,
            labelWidth: me.fieldDefaults.labelWidth,
            labelStyle: me.fieldDefaults.labelStyle,
            labelPad: me.fieldDefaults.labelPad,
            listeners: {
                change: Ext.bind(me.onChangeAssignGroupBeforeUnlockField, me)
            }
        });
    },

    /**
     * @returns { Ext.form.field.Text }
     */
    createRegisterTemplateField: function() {
        var me = this;

        return Ext.create('Ext.form.field.Text', {
            fieldLabel: me.snippets.fieldSets.configuration.registerTemplateLabel,
            name: 'registerTemplate',
            labelWidth: me.fieldDefaults.labelWidth,
            labelPad: me.fieldDefaults.labelPad,
            labelStyle: me.fieldDefaults.labelStyle,
            margin: '20px 0 0',
            columnWidth: 1,
            supportText: me.snippets.fieldSets.configuration.registerTemplateSupport
        });
    },

    /**
     * @returns { Ext.form.field.ComboBox }
     */
    createMailFieldAllow: function() {
        var me = this;

        return me.createMailField(
            'emailTemplateAllow',
            me.snippets.fieldSets.extended.emailTemplateAllowLabel,
            { emptyText: me.snippets.fieldSets.extended.emailTemplateAllowEmptyText }
        );
    },

    /**
     * @returns { Ext.form.field.ComboBox }
     */
    createMailFieldDeny: function() {
        var me = this;

        return me.createMailField(
            'emailTemplateDeny',
            me.snippets.fieldSets.extended.emailTemplateDenyLabel,
            {
                margin: '20px 3px 0 0',
                emptyText: me.snippets.fieldSets.extended.emailTemplateDenyEmptyText
            }
        );
    },

    /**
     * @returns { Ext.data.Store }
     */
    createAssignGroupStore: function() {
        return Ext.create('Ext.data.Store', {
            model: 'Shopware.model.Dynamic',
            proxy: {
                type: 'ajax',
                url: '{url controller="SwagBusinessEssentials" action="getCustomerGroups"}',
                reader: Ext.create('Shopware.model.DynamicReader')
            }
        });
    },

    /**
     * @param { Ext.form.field.ComboBox } combo
     * @param { string } newValue
     */
    onChangeAssignGroupBeforeUnlockField: function (combo, newValue) {
        var me = this;

        me.record.set(combo.getName(), newValue);
    }
});
// {/block}
