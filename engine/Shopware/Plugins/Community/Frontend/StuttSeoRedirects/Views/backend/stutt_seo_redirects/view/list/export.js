//{namespace name=backend/stutt_seo_redirects/view/list}

Ext.define('Shopware.apps.StuttSeoRedirects.view.list.Export', {
    cls: 'stutt-seo-redirects-export-window',

    alias: 'widget.stutt-seo-redirects-export-window',

    extend: 'Ext.window.Window',
    modal: true,

    header: false,

    layout: {
        type: 'vbox',
        align: 'stretch'
    },

    width: 600,
    height: 390,

    initComponent: function() {
        var me = this;

        me.items = me.createItems();

        me.dockedItems = [ me.createToolbar() ];
        me.callParent(arguments);
    },

    createItems: function() {
        var me = this;

        me.formatDropdown = Ext.create('Ext.form.field.ComboBox', {
            fieldLabel: '{s name="csv_format"}Format{/s}',
            'name': 'csv_format',
            labelWidth: 180,
            queryMode: 'local',
            width: 180,
            listeners: {
                scope: me
            },
            store: Ext.create('Ext.data.Store', {
                fields: [ 'value', 'title' ],
                data: [
                    { value: '2', 'title': '{s name="format2"}2 Spalten (alte URL, neue URL){/s}' },
                    { value: '3', 'title': '{s name="format3"}3 Spalten (aktiviert, alte URL, neue URL){/s}' },
                    { value: '4', 'title': '{s name="format4"}4 Spalten (aktiviert, alte URL, neue URL, temporär){/s}' },
                    { value: '5', 'title': '{s name="format5"}5 Spalten (aktiviert, alte URL, neue URL, Shopware-URL ersetzen, temporär){/s}' },
                    { value: '6', 'title': '{s name="format6"}6 Spalten (aktiviert, alte URL, neue URL, Shopware-URL ersetzen, temporär, externes Ziel){/s}' }
                ]
            }),
            displayField: 'title',
            valueField: 'value'
        });
        me.formatDropdown.setValue('2');

        me.separatorChar = Ext.create('Ext.form.field.Text', {
            fieldLabel: '{s name="separator_char"}Trennzeichen{/s}',
            name: 'separator_char',
            labelWidth: 180,
            allowBlank: false,
            value: ';'
        });

        me.firstLineHasHeadings = Ext.create('Ext.form.field.Checkbox', {
            inputValue: true,
            uncheckedValue: false,
            checked: false,
            labelWidth: 180,
            name: 'first_line_has_headings',
            fieldLabel: '{s name="first_line_has_headings"}erste Zeile enthält Überschriften{/s}'
        });

        me.exportInfo = Ext.create('Ext.form.FieldSet', {
            cls : 'info',
            title: '{s name="upload_info_title"}Tipp{/s}',
            html: '{s name="export_info_desc"}Hier können Sie Ihre bestehenden Weiterleietungen als CSV-Datei exportieren. Die Datei können Sie in Excel öffnen und bearbeiten und über die Importfunktion des Plugins in diesem oder einem anderen Shopware-Shop importieren.{/s}'
        });

        me.form = Ext.create('Ext.form.Panel', {
            items: [ me.exportInfo, me.formatDropdown, me.separatorChar, me.firstLineHasHeadings ],
            bodyPadding: 20,
            border: false,
            url: '{url controller="StuttSeoRedirects" action="export"}',
            flex: 1,
            layout: {
                type: 'vbox',
                align: 'stretch'
            }
        });

        return me.form;
    },

    createToolbar: function() {
        var me = this;

        me.cancelButton = Ext.create('Ext.button.Button', {
            cls: 'secondary',
            text: '{s name="cancel"}Cancel{/s}',
            handler: function() {
                me.destroy();
            }
        });

        me.uploadButton = Ext.create('Ext.button.Button', {
            cls: 'primary',
            text: '{s name="export_csv"}Weiterleitungen exportieren{/s}',
            handler: function() {
                if (!me.form.getForm().isValid()) {
                    return;
                }

                Shopware.app.Application.fireEvent('export-csv', me.form, function(success) {
                    me.destroy();
                    Shopware.app.Application.fireEvent('reload-local-listing');
                });
            }
        });

        return Ext.create('Ext.toolbar.Toolbar', {
            dock: 'bottom',
            items: [me.cancelButton, '->', me.uploadButton ]
        });
    }
});