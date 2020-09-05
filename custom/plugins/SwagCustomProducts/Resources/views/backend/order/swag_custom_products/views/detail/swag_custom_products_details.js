// {namespace name="backend/order/main"}

// {block name="backend/order/swag_custom_products/views/detail/swag_custom_products_details"}

Ext.define('Shopware.apps.Order.views.detail.SwagCustomProductsDetails', {
    extend: 'Enlight.app.Window',
    autoShow: true,
    autoScroll: true,
    layout: 'anchor',
    bodyPadding: 25,
    defaults: {
        anchor: '100%',
        labelWidth: 155
    },

    downloadPath: '{url controller=SwagCustomProductsExtensions action=downloadSvg}',

    /** @object Snippets object */
    snippets: {
        title: '{s name="window/title"}Custom Products details{/s}',
        name: '{s name="grid/name"}Name{/s}',
        source: '{s name="grid/source"}Source{/s}',
        size: '{s name="grid/size"}Size{/s}',
        tooltipView: '{s name="grid/tooltipView"}Preview{/s}',
        btnClose: '{s name="btn/close"}Close{/s}'
    },

    /**
     * Initializes the component, sets the title for the window and adds the items.
     *
     * @returns void
     */
    initComponent: function() {
        var me = this;

        me.title = me.snippets.title;
        me.items = me.createFormItems(me.customProductsData);
        me.bbar = me.createActionButtons();

        me.callParent(arguments);
    },

    /**
     * Creates the form elements for the window. The first parameter contains the entire custom products configuration.
     * Each element in the configuration contains a type property which we can use to terminate the type of the field.
     *
     * @param { Array } items - Entire custom products configuration
     * @returns { Array }
     */
    createFormItems: function(items) {
        var me = this;

        items = Ext.Array.map(items, function(item) {
            if (['textfield', 'date', 'time', 'numberfield'].indexOf(item.type) !== -1) {
                return me.createDisplayField(item);
            }

            if (['multiselect', 'checkbox', 'radio', 'select'].indexOf(item.type) !== -1) {
                return me.createMultiField(item);
            }

            if (['textarea'].indexOf(item.type) !== -1) {
                return me.createTextareaField(item);
            }

            if (['wysiwyg'].indexOf(item.type) !== -1) {
                return me.createWysiwygField(item);
            }

            if (['colorselect'].indexOf(item.type) !== -1) {
                return me.createColorField(item);
            }

            if (['imageselect'].indexOf(item.type) !== -1) {
                return me.createImageSelectionField(item);
            }

            if (['fileupload', 'imageupload'].indexOf(item.type) !== -1) {
                return me.createUploadField(item);
            }
        }, me);

        return items;
    },

    /**
     * Helper method which creates a field container definition. The container will have a two column layout when the
     * `items` parameter contains more than 1 item.
     *
     * @param { String } label
     * @param { Array } items
     * @returns { Object }
     */
    createFieldContainer: function(label, items) {
        return {
            xtype: 'fieldcontainer',
            layout: 'column',
            defaults: {
                xtype: 'textfield',
                columnWidth: (items.length > 1) ? 0.5 : 1
            },
            fieldLabel: label,
            items: items
        };
    },

    /**
     * Helper method which creates a grid component including a store with field definition.
     *
     * @param { String } label
     * @param { Array } data
     * @param { Array } columns
     * @returns { Object }
     */
    createGridPanel: function(label, data, columns) {
        var fields = Ext.Array.clean(Ext.Array.map(columns, function(column) {
            return (column.dataIndex) ? column.dataIndex : '';
        }));

        return {
            xtype: 'fieldcontainer',
            fieldLabel: label,
            items: [{
                xtype: 'gridpanel',
                store: Ext.create('Ext.data.Store', {
                    fields: fields,
                    data: data
                }),
                title: label,
                hideHeaders: true,
                columns: columns,
                autoScroll: true,
                height: 100
            }]
        };
    },

    /**
     * Creates a read only textfield a simple input like a text, date string or number.
     *
     * @param { Object } item
     * @returns { Object }
     */
    createDisplayField: function(item) {
        return {
            xtype: 'textfield',
            readOnly: true,
            fieldLabel: item.label,
            value: item.value
        };
    },

    /**
     * Creates a grid for multi value inputs like radio boxes or multiselect
     *
     * @param { Object } item
     * @returns { Object }
     */
    createMultiField: function(item) {
        var columns = [{
            dataIndex: 'label',
            header: this.snippets.name,
            flex: 1
        }];

        return this.createGridPanel(item.label, item.value, columns);
    },

    /**
     * Creates a read only text area for large text inputs.
     *
     * @param { Object } item
     * @returns { Object }
     */
    createTextareaField: function(item) {
        return {
            xtype: 'textareafield',
            labelWidth: 155,
            value: item.value,
            fieldLabel: item.label,
            readOnly: true
        };
    },

    /**
     * Creates a tiny mce editor which renders the user inputs.
     *
     * @param { Object } item
     * @returns { Object }
     */
    createWysiwygField: function(item) {
        return {
            xtype: 'tinymce',
            fieldLabel: item.label,
            labelWidth: 155,
            border: 1,
            value: item.value,
            readOnly: true
        };
    },

    /**
     * Creates a read only color field component.
     *
     * @param { Object } item
     * @returns { Object }
     */
    createColorField: function(item) {
        var items = [];

        Ext.each(item.value, function(value) {
            items.push({
                xtype: 'container',
                layout: 'hbox',
                items: [{
                    xtype: 'textfield',
                    labelWidth: 155,
                    readOnly: true,
                    hideLabel: true,
                    value: value.label,
                    flex: 1
                }, {
                    xtype: 'container',
                    width: 28,
                    height: 28,
                    style: [
                        'border: 2px solid #d6dce1; border-radius: 3px; background-color:',
                        value.value,
                        ';'
                    ].join('')
                }]
            });
        });

        return this.createFieldContainer(item.label, items);
    },

    /**
     * Creates a grid for the image selection.
     *
     * @param { Object } item
     * @returns { Object }
     */
    createImageSelectionField: function(item) {
        var columns = [{
            dataIndex: 'label',
            header: this.snippets.name,
            flex: 1
        }, {
            dataIndex: 'value',
            text: this.snippets.source,
            hidden: true
        }, {
            xtype: 'actioncolumn',
            width: 30,
            items: [{
                iconCls: 'sprite-globe--arrow',
                action: 'view',
                tooltip: this.snippets.tooltipView,
                handler: Ext.bind(this.onOpenHandler, this)
            }]
        }];

        return this.createGridPanel(item.label, item.value, columns);
    },

    /**
     * Creates a grid for both upload fields.
     *
     * @param { Object } item
     * @returns { Object }
     */
    createUploadField: function(item) {
        var me = this,
            items = [],
            columns = [{
                dataIndex: 'label',
                header: this.snippets.name,
                flex: 2
            }, {
                dataIndex: 'size',
                header: this.snippets.size,
                width: 120,
                renderer: Ext.util.Format.fileSize
            }, {
                dataIndex: 'mimeType',
                hidden: true
            }, {
                dataIndex: 'path',
                hidden: true
            }, {
                dataIndex: 'value',
                text: this.snippets.source,
                hidden: true
            }, {
                xtype: 'actioncolumn',
                width: 30,
                renderer: function(data, metaData, store, index) {
                    var parentGrid = this.findParentByType('gridpanel'),
                        rec = parentGrid.getStore().getAt(index);

                    if (me.isSvg(rec)) {
                        this.items[0].iconCls = 'sprite-drive-download';
                    } else {
                        this.items[0].iconCls = 'sprite-globe--arrow';
                    }
                },
                items: [{
                    iconCls: 'sprite-globe--arrow',
                    action: 'view',
                    tooltip: this.snippets.tooltipView,
                    handler: Ext.bind(me.onOpenHandler, me)
                }]
            }];

        Ext.each(item.value, function(value) {
            Ext.each(value.value, function(file) {
                items.push({
                    id: file.id,
                    label: file.name,
                    value: file.path,
                    path: file.realPath,
                    size: file.size,
                    mimeType: file.mimeType
                });
            });
        });

        return this.createGridPanel(item.label, items, columns);
    },

    /**
     * Creates the toolbar including the action buttons for the window.
     *
     * @returns { Ext.toolbar.Toolbar }
     */
    createActionButtons: function() {
        var me = this;

        return Ext.create('Ext.toolbar.Toolbar', {
            docked: 'bottom',
            items: ['->', {
                xtype: 'button',
                text: this.snippets.btnClose,
                cls: 'primary',
                handler: function() {
                    me.destroy();
                }
            }]
        });
    },

    /**
     * Event handler method which will be fired when user clicks the "preview" icon in one of the grids.
     *
     * Opens the value property in a new window / tab (depending on the browser).
     *
     * @param { Ext.grid.Panel } view
     * @param { Ext.data.Store } store
     * @param { Number } rowIdx
     * @param { Number } colIdx
     * @param { Ext.grid.Column } column
     * @param { Ext.data.Model } record
     */
    onOpenHandler: function(view, store, rowIdx, colIdx, column, record) {
        var me = this,
            hasMimeType = record.raw.mimeType === 'image/svg+xml',
            pathAdd = '?';

        if (me.isSvg(record)) {
            pathAdd += hasMimeType ? 'path=' + record.get('path') : 'value=' + record.get('value');
            window.open(me.downloadPath + pathAdd);
        } else {
            window.open(record.get('value'));
        }
    },

    /**
     * @param { Ext.data.Model } record
     * @return { boolean }
     */
    isSvg: function(record) {
        var hasMimeType = record.raw.mimeType === 'image/svg+xml',
            isSvg = record.get('value').match(/(\.svg)$/);

        return hasMimeType || isSvg;
    }
});

// {/block}
