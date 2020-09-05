/**
 * global: Ext, Shopware
 */
//{namespace name="plugins/neti_foundation/backend/send_mail"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.sendMail.Form', {
    'extend': 'Ext.form.Panel',
    'alias': 'widget.neti_foundation_extensions_send_mail_form',
    'requires': [],
    'border': null,
    'region': 'center',
    'autoScroll': true,
    'bodyPadding': 10,
    'layout': {
        'type': 'vbox',
        'align': 'stretch'
    },
    'snippets': {
        'fieldset_settings_title': '{s name=fieldset_settings_title}Vorlagen Einstellungen{/s}',
        'label_template': '{s name=label_template}Vorlage{/s}',
        'fieldlabel_frommail': '{s name=fieldlabel_frommail}Absender{/s}',
        'fieldlabel_fromname': '{s name=fieldlabel_fromname}Name{/s}',
        'fieldlabel_subject': '{s name=fieldlabel_subject}Betreff{/s}',
        'fieldlabel_htmlmail': '{s name=fieldlabel_htmlmail}HTML-Mail{/s}',
        'fieldboxlabel_htmlmail': '{s name=fieldboxlabel_htmlmail}Vorlage als HTML eMail versenden{/s}',
        'tab_plaintext': '{s name=tab_plaintext}Plaintext{/s}',
        'tab_html': '{s name=tab_html}HTML{/s}',
        'tab_attachments': '{s name=tab_attachments}Anhänge{/s}'
    },
    'remoteStoreAlias': 'widget.neti_foundation_extensions_send_mail_store_mail',
    'attachmentStoreAlias': 'widget.neti_foundation_extensions_send_mail_store_attachment',
    'templatePrefix': null,
    'initComponent': function () {
        var me = this;

        me.items = me.getFormItems();

        me.callParent(arguments);
    },

    'getFormItems': function () {
        var me = this,
            items = [];

        items.push(me.getTemplateSettingsPanel());
        items.push(me.getEditorTabPanel());

        return items;
    },

    'getTemplateSettingsPanel': function () {
        var me = this;

        return me.templateSettingsPanel || me.createTemplateSettingsPanel();
    },

    'getEditorTabPanel': function () {
        var me = this;

        return me.editorTabPanel || me.createEditorTabPanel();
    },

    'createTemplateSettingsPanel': function () {
        var me = this;

        me.templateSettingsPanel = Ext.create('Ext.form.FieldSet', {
            'title': me.snippets.fieldset_settings_title,
            'defaultType': 'textfield',
            'autoScroll': true,
            'flex': 1,
            'defaults': {
                'labelStyle': 'font-weight: 700; text-align: right;',
                'layout': 'anchor',
                'labelWidth': 130,
                'anchor': '100%'
            },
            'items': me.getTemplateSettingItems()
        });

        return me.templateSettingsPanel;
    },

    'getTemplateSettingItems': function () {
        var me = this,
            items = [],
            remoteData = [],
            localStore,
            remoteStore = me.getRemoteStore(),
            modelFields = remoteStore.model.getFields(),
            templatePrefix = me.getTemplatePrefix();

        localStore = Ext.create('Ext.data.Store', {
            'fields': modelFields,
            'data': []
        });

        me.setLoading(true, true);
        remoteStore.load({
            'callback': function (records) {
                Ext.each(records, function (record) {
                    if (record.raw.data) {
                        Ext.each(record.raw.data, function (data) {
                            if (templatePrefix && 0 === data.name.indexOf(templatePrefix) || !templatePrefix) {
                                remoteData.push(data);
                            }
                        });
                    }
                });
                localStore.add(remoteData);
                me.setLoading(false);
            }
        });

        items.push({
            'fieldLabel': me.snippets.label_template,
            'xtype': 'combobox',
            'name': 'mailTemplateId',
            'allowBlank': false,
            'displayField': 'name',
            'valueField': 'id',
            'queryMode': 'local',
            'store': localStore,
            'multiSelect': false,
            'editable': false,
            'listeners': {
                'select': function (combobox, records) {
                    var record = records[0];

                    me.setLoading(true, true);
                    remoteStore.load({
                        'params': {
                            'id': record.get('id')
                        },
                        'callback': function (records) {
                            var record = records[0],
                                form = me.getForm();

                            me.templateModel = record;
                            me.loadRecord(record);

                            Ext.each(modelFields, function (modelField) {
                                var field = form.findField(modelField.name);
                                if (field && field.isFormField) {
                                    field.enable();
                                }
                            });

                            me.getEditorTabPanel().show();
                            me.setLoading(false);
                        }
                    });
                }
            }
        });
        items.push({
            'fieldLabel': me.snippets.fieldlabel_frommail,
            'disabled': true,
            'name': 'fromMail'
        });
        items.push({
            'fieldLabel': me.snippets.fieldlabel_fromname,
            'disabled': true,
            'name': 'fromName'
        });
        items.push({
            'fieldLabel': me.snippets.fieldlabel_subject,
            'disabled': true,
            'name': 'subject',
            'allowBlank': false
        });
        items.push({
            'xtype': 'checkboxfield',
            'disabled': true,
            'inputValue': true,
            'uncheckedValue': false,
            'name': 'isHtml',
            'fieldLabel': me.snippets.fieldlabel_htmlmail,
            'boxLabel': me.snippets.fieldboxlabel_htmlmail,
            'listeners': {
                /**
                 * Fires when a user-initiated change is detected in the value of the field.
                 */
                'change': function (field, newValue) {
                    me.getEditorTabPanel().getComponent('htmlContentTab').setDisabled(!newValue);
                }
            }
        });

        return items;
    },

    'createEditorTabPanel': function () {
        var me = this;

        me.editorTabPanel = Ext.create('Ext.tab.Panel', {
            'flex': 1,
            'hidden': true,
            'listeners': {
                'scope': me,
                // SW-3564 - Refresh codemirror fields on tab change
                'tabchange': function (tabPanel, tab) {
                    var editorField = tab.editorField;
                    if (editorField && editorField.editor) {
                        editorField.editor.refresh();
                    }

                    if (tab.getXType() === 'neti_foundation_extensions_send_mail_attachments') {
                        me.reloadAttachmentTree();
                    }
                },
                'beforeshow': function (tabPanel) {
                    var tab = tabPanel.getActiveTab(),
                        editorField = tab.editorField;

                    if (editorField && editorField.editor) {
                        // editorField.editor.refresh();
                        editorField.editor.execCommand("startAutocomplete");
                        editorField.editor.completer.detach()
                    }

                }
            },
            'items': me.getEditorItems()
        });

        return me.editorTabPanel;
    },

    'getEditorItems': function () {
        var me = this,
            items = [];

        items.push({
            'xtype': 'neti_foundation_extensions_send_mail_content_editor',
            'itemId': 'contentTab',
            'name': 'contentTab',
            'title': me.snippets.tab_plaintext,
            'templateModel': me.getTemplateModel()
        });
        items.push({
            'xtype': 'neti_foundation_extensions_send_mail_content_editor',
            'isHtml': true,
            'title': me.snippets.tab_html,
            'itemId': 'htmlContentTab',
            'id': 'htmlContentTab',
            'name': 'htmlContentTab',
            'disabled': true,
            'listeners': {
                'showPreview': function (value, isHtml) {
                    me.fireEvent('showPreview', value, isHtml, me.getTemplateModel());
                }
            }
        });
        items.push(me.getAttachmentTree());

        return items;
    },

    'createAttachmentTree': function () {
        var me = this;

        me.attachmentTree = Ext.create('Shopware.apps.NetiFoundationExtensions.sendMail.Attachments', {
            'itemId': 'attachmentsTab',
            'name': 'attachmentsTab',
            'title': me.snippets.tab_attachments,
            'store': me.getAttachmentStore()
        });

        return me.attachmentTree;
    },

    'getRemoteStore': function () {
        var me = this;

        return me.remoteStore || me.createRemoteStore();
    },

    'createRemoteStore': function () {
        var me = this;

        me.remoteStore = Ext.createByAlias(me.remoteStoreAlias);

        return me.remoteStore;
    },

    'getAttachmentStore': function () {
        var me = this;

        return me.attachmentStore || me.createAttachmentStore();
    },

    'createAttachmentStore': function () {
        var me = this;

        me.attachmentStore = Ext.createByAlias(me.attachmentStoreAlias);

        return me.attachmentStore;
    },

    'getTemplateModel': function () {
        var me = this;

        return me.templateModel;
    },

    'getTemplatePrefix': function () {
        var me = this;

        return me.templatePrefix;
    },

    'reloadAttachmentTree': function () {
        var me = this,
            store = me.getAttachmentStore(),
            rootNode = me.getAttachmentTree().getRootNode(),
            mailRecord = me.getTemplateModel();

        store.getProxy().extraParams.mailId = mailRecord.get('id');

        rootNode.removeAll(false);
        store.load();
    },

    'getAttachmentTree': function () {
        var me = this;

        return me.attachmentTree || me.createAttachmentTree();
    }
});
//{/block}
