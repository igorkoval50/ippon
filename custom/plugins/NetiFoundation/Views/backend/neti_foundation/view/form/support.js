//{namespace name="backend/NetiFoundation/support"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundation.view.form.Support', {
    'extend'  :'Ext.form.Panel',
    'requires': [
        'Ext.form.*'
    ],
    'border'    : false,
    'alias'     : 'widget.neti_foundation-form-support',
    'autoScroll': true,
    'bodyPadding': 10,
    /**
     *
     */
    'initComponent': function(config)
    {
        var me = this;

        Ext.apply(me, {
            'url'        : '{url controller=Foundation action=sendSupportMail}',
            'defaultType': 'textfield',
            'defaults'   : {
                'width': 330
            },
            'items': [
                {
                    'name'      : 'plugin',
                    'value'     : me.pluginName,
                    'hidden'    : true
                },
                {
                    'xtype'     : 'combo',
                    'fieldLabel': '{s name="label_type"}Typ*{/s}',
                    'name'      : 'type',
                    'allowBlank': false,
                    'store'     : [
                        ['Support' , '{s name="type_help"}Hilfe{/s}'                     ],
                        ['Fehler'  , '{s name="type_error"}Fehlermeldung{/s}'            ],
                        ['Idee'    , '{s name="type_suggest"}Verbesserungsvorschlag{/s}' ]
                    ]
                },
                {
                    'fieldLabel': '{s name="label_name"}Name*{/s}',
                    'name'      : 'name',
                    'flex'      : 1,
                    'allowBlank': false
                },
                {
                    'fieldLabel': '{s name="label_company"}Firma{/s}',
                    'name'      : 'company',
                    'flex'      : 1
                },
                {
                    'fieldLabel': '{s name="label_email"}E-Mail*{/s}',
                    'vtype'     : 'email',
                    'name'      : 'email',
                    'allowBlank': false,
                    'flex'      : 1
                },
                {
                    'fieldLabel': '{s name="label_phone"}Telefon{/s}',
                    'name'      : 'tel',
                    'flex'      : 1
                },
                {
                    'fieldLabel': '{s name="label_subject"}Betreff*{/s}',
                    'name'      : 'subject',
                    'flex'      : 1,
                    'allowBlank': false
                },
                {
                    'fieldLabel': '{s name="label_message"}Nachricht*{/s}',
                    'xtype'     : 'htmleditor',
                    'name'      : 'message',
                    'anchor'    : '100%',
                    'height'    : 300,
                    'allowBlank': false
                }
            ],
        });

        me.dockedItems = [{
            dock: 'bottom',
            xtype: 'toolbar',
            ui: 'shopware-ui',
            cls: 'shopware-toolbar',
            items: me.createFormButtons()
        }];
        me.callParent(arguments);
    },

    'createFormButtons': function() {
        var me = this,
            items = ['->'];

        items.push({
            'text': '{s name="message_empty_button"}Leeren{/s}',
            'cls': 'secondary',
            'handler': function () {
                Ext.MessageBox.confirm(
                    '{s name="message_empty_header"}Felder leeren?{/s}',
                    '{s name="message_empty_text"}Sind Sie sicher, dass Sie alle Felder leeren möchten?{/s}',
                    confirmClear
                );

                function confirmClear(btn) {
                    if ('yes' === btn) {
                        me.getForm().reset();
                    }
                }
            }
        });
        items.push({
            'text': '{s name="button_send"}Abschicken{/s}',
            'cls':'primary',
            'handler': function () {
                if (me.getForm().isValid()) {
                    me.getForm().submit({
                        'success': function () {
                            me.getForm().reset();
                            Ext.MessageBox.alert(
                                '{s name="message_success_header"}E-Mail erfolgreich versendet!{/s}',
                                '{s name="message_success_text"}Wir werden uns schnellstmöglich mit Ihnen in Verbindung setzen.{/s}'
                            );
                        },
                        'failure': function (me, request) {
                            Ext.MessageBox.alert(
                                '{s name="message_error_header"}Fehler aufgetreten!{/s}',
                                request.result.message
                            );
                        }
                    });
                } else {
                    Ext.MessageBox.alert(
                        '{s name="message_mandatory_header"}Benötigte Felder!{/s}',
                        '{s name="message_mandatory_text"}Bitte füllen Sie alle rot markierten Felder aus!{/s}'
                    );
                }
            }
        });

        return items;
    }
});
//{/block}
