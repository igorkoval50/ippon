/**
 * The detail container for the template view.
 */

// {namespace name="backend/swag_custom_products/detail/detail"}
// {block name="backend/swag_custom_products/view/detail/detail"}
Ext.define('Shopware.apps.SwagCustomProducts.view.detail.Detail', {
    extend: 'Shopware.model.Container',

    style: {
        background: '#EEEEF0'
    },

    alias: 'widget.swag-custom-products-detail-detail',

    /**
     * @type { String }
     */
    eventAlias: 'template',

    /**
     * @type { Object }
     */
    snippets: {
        title: '{s name="title"}Configuration{/s}',
        field: {
            internalName: {
                label: '{s name="field/internal_name/label"}Internal name{/s}',
                helpText: '{s name="field/internal_name/help_text"}The internal name will be used only for identification and must be unique.{/s}',
                error: '{s name="field/internal_name/error"}The internal name is already in use.{/s}'
            },
            displayName: {
                label: '{s name="field/display_name/label"}Display name{/s}',
                helpText: '{s name="field/display_name/help_text"}The display name will only appear in the frontend.{/s}'
            },
            description: {
                label: '{s name="field/description/label"}Description{/s}'
            },
            image: {
                label: '{s name="field/image/label"}Image{/s}'
            },
            active: {
                label: '{s name="field/active/label"}Active{/s}'
            },
            stepByStep: {
                label: '{s name="field/step_by_step/label"}Step-By-Step mode{/s}',
                helpText: '{s name="field/step_by_step/help_text"}Activates the step-by-step configurator in the frontend. This is an alternative view for the configurator options. An option becomes a step that leads the user through the configurator.{/s}'
            },
            confirmInput: {
                label: '{s name="field/confirm_input/label"}Force the user to confirm the input.{/s}'
            },
            variantsOnTop: {
                label: '{s name="field/variants_on_top/label"}Place variants above options{/s}'
            }
        }
    },

    /**
     * Override the initComponent to add a title.
     */
    initComponent: function () {
        var me = this;

        me.callParent(arguments);
        me.title = me.snippets.title;
    },

    /**
     * Returns the configuration for this component.
     *
     * @returns { Object }
     */
    configure: function () {
        var me = this;

        me.internalNameValid = true;

        return {
            controller: 'SwagCustomProducts',
            splitFields: false,
            fieldSets: [{
                border: false,
                title: null,
                fields: {
                    internalName: {
                        fieldLabel: me.snippets.field.internalName.label,
                        helpText: me.snippets.field.internalName.helpText,
                        allowBlank: false,
                        listeners: {
                            change: {
                                scope: me,
                                fn: me.validateInternalName,
                                buffer: 200
                            }
                        },
                        validator: function () {
                            if (me.internalNameValid) {
                                return true;
                            }
                            return me.snippets.field.internalName.error;
                        }
                    },
                    displayName: {
                        fieldLabel: me.snippets.field.displayName.label,
                        helpText: me.snippets.field.displayName.helpText,
                        allowBlank: false,
                        translatable: true
                    },
                    description: {
                        fieldLabel: me.snippets.field.description.label,
                        xtype: 'tinymce',
                        width: 200,
                        translatable: true
                    },
                    mediaId: {
                        fieldLabel: me.snippets.field.image.label
                    },
                    active: {
                        fieldLabel: me.snippets.field.active.label
                    },
                    stepByStepConfigurator: {
                        fieldLabel: me.snippets.field.stepByStep.label,
                        helpText: me.snippets.field.stepByStep.helpText
                    },
                    confirmInput: {
                        fieldLabel: me.snippets.field.confirmInput.label
                    },
                    variantsOnTop: {
                        fieldLabel: me.snippets.field.variantsOnTop.label
                    }
                }
            }]
        };
    },

    /**
     * Overrides register events to add custom events.
     */
    registerEvents: function () {
        var me = this;
        me.callParent(arguments);

        me.addEvents(
            /**
             * Event which creates the property pluginConfig in the given scope
             * @param { Object } me - The scope which receives the plugin config.
             *
             */
            me.eventAlias + '-get-plugin-config'
        );
    },

    /**
     * Sends an Ajax request to validate the internal name.
     *
     * @param { Ext.form.field.Text } field
     * @param { String } newValue
     */
    validateInternalName: function (field, newValue) {
        var me = this;

        // Validation request - checks for duplicate internal names
        Ext.Ajax.request({
            url: '{url action="validateInternalNameAjax"}',
            params: {
                value: newValue
            },
            success: function (response) {
                var responseObj = Ext.JSON.decode(response.responseText);

                if (responseObj.success == true || me.record.get('internalName') == newValue) {
                    me.internalNameValid = true;
                    field.isValid();
                    return true;
                } else {
                    me.internalNameValid = false;
                    field.isValid();
                    return false;
                }
            }
        });
    }
});
// {/block}
