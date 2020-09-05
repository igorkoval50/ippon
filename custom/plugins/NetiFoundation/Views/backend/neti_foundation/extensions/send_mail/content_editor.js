/**
 * global: Ext, Shopware
 */
//{namespace name="plugins/neti_foundation/backend/send_mail"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.sendMail.ContentEditor', {
    'extend': 'Ext.Panel',
    'alias': 'widget.neti_foundation_extensions_send_mail_content_editor',
    'bodyPadding': 10,
    'templateModel': null,

    'layout': 'fit',

    'isHtml': false,

    /**
     * Defines additional events which will be fired
     *
     * @return void
     */
    'registerEvents':function () {
        this.addEvents(
            /**
             * Event will be fired when the user clicks the show preview button
             *
             * @event showPreview
             * @param [string] content of the textarea
             * @param [boolean]
             */
            'showPreview',

            /**
             * Event will be fired when the user clicks the send testmail button
             *
             * @event sendTestMail
             * @param [string] content of the textarea
             * @param [boolean]
             */
            'sendTestMail'
        );
    },

    /**
     * Initializes the component and builds up the main interface
     *
     * @public
     * @return void
     */
    'initComponent': function() {
        var me = this;

        me.items = me.getItems();
        me.dockedItems = [ me.getToolbar() ];

        me.callParent(arguments);
    },

    /**
     * Creates items shown in form panel
     *
     * @return array
     */
    'getItems': function() {
        var me = this;

        me.editorField = null;

        if (this.isHtml) {
            me.editorField= Ext.create('Shopware.form.field.CodeMirror', {
                'xtype': 'codemirrorfield',
                'mode': {
                    'name': 'smarty',
                    'baseMode': 'text/html'
                },
                'name': 'contentHtml',
                'translationLabel': '{s name=codemirrorHtml_translationLabel}HTML-Text{/s}',
                'translatable': true, // Indicates that this field is translatable
                'completers': me.getCompletion()
            });
        } else {
            me.editorField = Ext.create('Shopware.form.field.CodeMirror', {
                'xtype': 'codemirrorfield',
                'mode': 'smarty',
                'name': 'content',
                'translationLabel': '{s name=codemirror_translationLabel}Plaintext{/s}',
                'translatable': true, // Indicates that this field is translatable
                'completers': me.getCompletion()
            });
            me.editorField.name = 'content';
            me.editorField.translationLabel = 'content';
        }

        me.editorField.on('editorready', function(editorField, editor) {
            var scroller, size;

            if(!editor || !editor.hasOwnProperty('display')) {
                return false;
            }

            scroller = editor.display.scroller;
            size = editorField.getSize();
            editor.setSize('100%', size.height);
            Ext.get(scroller).setSize(size);
        });

        me.on('resize', function(cmp, width, height) {
            var editorField = me.editorField,
                editor = editorField.editor,
                scroller;

            if(!editor || !editor.hasOwnProperty('display')) {
                return false;
            }

            scroller = editor.display.scroller;

            width -= me.bodyPadding * 2;
            // We need to remove the bodyPadding, the padding on the field itself and the scrollbars
            height -= me.bodyPadding * 5;

            editor.setSize(width, height);
            Ext.get(scroller).setSize({ width: width, height: height });
        });

        return me.editorField;
    },

    /**
     * Creates the toolbar.
     *
     * @return [object] generated Ext.toolbar.Toolbar
     */
    'getToolbar': function() {
        var me = this;

        return {
            'xtype': 'toolbar',
            'dock': 'top',
            'items': [
                {
                    'xtype': 'button',
                    'hidden': true,
                    'text': '{s name=button_preview}Vorschau anzeigen{/s}',
                    'action': 'preview',
                    'listeners': {
                        'click': function() {
                            me.fireEvent('showPreview', me.editorField.getValue(), me.isHtml);
                        }
                    }
                }
            ]
        };
    },

    /**
     * @return { array }
     */
    getCompletion: function () {
        var me = this,
            Range = ace.require('ace/range').Range;

        var smartyCompleter = {
            getCompletions: function(editor, session, pos, prefix, callback) {
                var record = me.up('form').getRecord(),
                    range = new Range(0, 0, pos.row, pos.column);

                if (prefix.length === 0 || !Ext.isDefined(record)) { callback(null, []); return }
                Ext.Ajax.request({
                    url: '{url controller=Mail action=getMailVariables}',
                    params: {
                        prefix: prefix,
                        mailId: record.get('id'),
                        smartyCode: editor.getSession().getTextRange(range)
                    },
                    success: function(response){
                        var text = JSON.parse(response.responseText);

                        callback(null, text.data.map(function(ea) {
                            return {
                                caption: ea.word,
                                value: ea.word,
                                meta: ea.value
                            }
                        }));
                    }
                });
            }
        };

        return [smartyCompleter];
    }
});
//{/block}
