// {namespace name='backend/swag_business_essentials/view/main'}
// {block name="backend/swag_business_essentials/view/components/params_field"}
Ext.define('Shopware.apps.SwagBusinessEssentials.view.components.ParamsField', {
    extend: 'Ext.form.FieldContainer',
    alias: 'widget.business_essentials-params_field',

    mixins: {
        field: 'Ext.form.field.Field'
    },

    layout: 'fit',

    disabled: false,

    /**
     * Creates the trigger-field, the main element of this field-container.
     */
    initComponent: function() {
        var me = this;

        me.items = [
            me.createTrigger()
        ];

        me.callParent(arguments);
    },

    /**
     * @param { bool } disabled
     */
    setDisabled: function (disabled) {
        var element = this.down('#btn-' + this.id);

        if (disabled) {
            element.addCls('x-btn-disabled');
        } else {
            element.removeCls('x-btn-disabled');
        }

        this.disabled = disabled;

        this.callParent(arguments);
    },

    /**
     * Creates the trigger-field with a custom trigger markup.
     *
     * @returns { Ext.form.field.Trigger }
     */
    createTrigger: function() {
        var me = this;

        me.triggerField = Ext.create('Ext.form.field.Trigger', {
            itemId: 'btn-' + me.id,
            readOnly: true,
            triggerNoEditCls: 'business--no-edit',
            getTriggerMarkup: Ext.bind(me.getTriggerMarkup, me),
            listeners: {
                render: function() {
                    var button = Ext.get(me.id + '-browseButtonWrap'),
                        triggerButton = Ext.get(me.triggerId);

                    button.on('click', function() {
                        if (!me.disabled) {
                            me.onClickTriggerBtn(button);
                        }
                    });

                    triggerButton.on('mouseenter', function() {
                        if (!me.disabled) {
                            triggerButton.addCls('x-btn-over');
                        }
                    });

                    triggerButton.on('mouseleave', function() {
                        if (!me.disabled) {
                            triggerButton.removeCls('x-btn-over');
                        }
                    });
                }
            }
        });

        return me.triggerField;
    },

    /**
     * Creates the params-window when the trigger-button gets clicked.
     *
     * @see { Shopware.apps.SwagBusinessEssentials.view.components.ParamsWindow }
     */
    onClickTriggerBtn: function () {
        Ext.create('Shopware.apps.SwagBusinessEssentials.view.components.ParamsWindow', {
            data: this.value,
            field: this
        }).show();
    },

    /**
     * Creates a custom trigger-button markup.
     *
     * @returns { string }
     */
    getTriggerMarkup: function() {
        var me = this,
            result,
            btnId = Ext.id(),
            btn = Ext.create('Ext.button.Button', {
                preventDefault: false,
                cls: Ext.baseCSSPrefix + 'business-essentials-trigger-btn small primary',
                style: '',
                text: '{s name="PrivateShoppingChooseParam"}Choose{/s}',
                width: '70px',
                name: 'triggerButton',
                id: 'browse-button--' + btnId
            }),
            btnCfg = btn.getRenderTree();

        me.triggerId = 'browse-button--' + btnId;
        result = '<td id="' + me.id + '-browseButtonWrap" style="width: 70px;">' + Ext.DomHelper.markup(btnCfg) + '</td>';

        btn.destroy();
        return result;
    },

    /**
     * Properly sets the value and triggers the change-method to render the text-field.
     *
     * @param { object } value
     * @returns { Shopware.apps.SwagBusinessEssentials.view.components.ParamsField }
     */
    setValue: function(value) {
        var me = this;
        me.value = value;
        me.onChangeValue();
        me.checkChange();
        return me;
    },

    /**
     * Is triggered when the value of this field changes.
     * It renders the value into the text-field of the trigger-field as a query-string.
     * Additionally, if the value is empty, the internal store is reset.
     */
    onChangeValue: function() {
        var me = this,
            paramString = '?';

        if (!me.value || me.value.length <= 0) {
            me.triggerField.setValue('');
            return;
        }

        Ext.each(me.value, function (item) {
            paramString += item.key + '=' + item.value + '&';
        });

        paramString = paramString.slice(0, -1);

        me.triggerField.setValue(paramString);
    }
});
// {/block}
