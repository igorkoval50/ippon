// {namespace name="backend/swag_custom_products/components/grid"}
// {block name="backend/swag_custom_products/components/valueGrid"}
Ext.define('Shopware.apps.SwagCustomProducts.view.components.ValueGrid', {
    extend: 'Ext.container.Container',

    border: false,
    anchor: '100%',
    layout: 'fit',

    snippets: {
        columns: {
            name: '{s name="valuegrid/columns/name"}Name{/s}',
            value: '{s name="valuegrid/columns/value"}Value{/s}',
            isDefaultValue: '{s name="valuegrid/columns/isDefaultValue"}Default value{/s}',
            surcharge: '{s name="valuegrid/columns/surcharge"}Surcharge{/s}',
            isOnceSurcharge: '{s name="valuegrid/columns/isOnceSurcharge"}Once surcharge{/s}',
            hintDragDrop: '{s name=valuegrid/columns/hintDragDrop}You can move rows via Drag & Drop{/s}'
        },
        title: '{s name="valuegrid/title"}Selectable values{/s}',
        addButton: '{s name="addbutton/text"}Add{/s}'
    },

    useColorPicker: false,
    useImagePicker: false,

    /**
     * Init the component
     */
    initComponent: function () {
        var me = this;

        me.type = me.type || '';

        me.items = [
            me.createGrid()
        ];

        me.callParent(arguments);
    },

    /**
     * @returns { Ext.grid.Panel|* }
     */
    createGrid: function () {
        var me = this;

        me.grid = Ext.create('Ext.grid.Panel', {
            title: me.snippets.title,
            store: me.store,
            viewConfig: me.createDragAndDrop(),
            columns: me.createColumns(),
            dockedItems: me.createDockedItems(),
            plugins: me.createPlugins(),
            autoScroll: true,
            height: 229
        });

        return me.grid;
    },

    /**
     * Create the necessary plugins for the grid.
     *
     * @returns { Ext.grid.Plugin[] }
     */
    createPlugins: function () {
        var me = this,
            items = [];

        me.rowEditor = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToEdit: 2
        });

        items.push(me.rowEditor);

        return items;
    },

    /**
     * @returns
     * { { plugins: { ptype: string, dragText: string }, listeners: { drop:
     *              { fn: Shopware.apps.Advisor.view.details.Questions.onDrop,
     *                  scope: Shopware.apps.Advisor.view.details.Questions
     *           } } } }
     */
    createDragAndDrop: function () {
        var me = this;

        return {
            plugins: {
                ptype: 'gridviewdragdrop',
                dragText: '{s name="ddt_text_drag_and_drop"}{/s}',
                dragGroup: 'valueDD',
                dropGroup: 'valueDD'
            },
            listeners: {
                drop: {
                    fn: me.onDrop,
                    scope: me
                }
            }
        };
    },

    /**
     * iterate over all questions and set the new index as order
     */
    onDrop: function () {
        var me = this;

        me.grid.getStore().each(function (value, index) {
            value.set('position', index);
        });
    },

    /**
     * @returns { *[] }
     */
    createColumns: function () {
        var me = this,
            extraColumn = me.createExtraColumn(),
            columns = [
                {
                    header: '&#009868',
                    width: 24,
                    hideable: false,
                    sortable: false,
                    menuDisabled: true,
                    renderer: Ext.bind(me.renderSortHandleColumn, me),
                    editor: false
                }, {
                    text: me.snippets.columns.name,
                    dataIndex: 'name',
                    editor: me.getNameEditor(),
                    flex: 3,
                    sortable: false
                }, {
                    text: me.snippets.columns.isDefaultValue,
                    dataIndex: 'isDefaultValue',
                    renderer: Ext.bind(me.booleanColumnRenderer, me),
                    editor: false,
                    flex: 1,
                    sortable: false
                }, {
                    text: me.snippets.columns.isOnceSurcharge,
                    dataIndex: 'isOnceSurcharge',
                    renderer: Ext.bind(me.booleanColumnRenderer, me),
                    editor: false,
                    flex: 1,
                    sortable: false
                }, me.createActionColumn()
            ];

        if (extraColumn) {
            columns = Ext.Array.insert(columns, 2, extraColumn);
        }

        return columns;
    },

    /**
     * This is the editor for the nameField. This contains the validator function in
     * the property validateValue.
     *
     * @returns { Shopware.apps.SwagCustomProducts.view.components.RowTextEditor }
     */
    getNameEditor: function () {
        return Ext.create('Shopware.apps.SwagCustomProducts.view.components.RowTextEditor');
    },

    /**
     * @returns { * }
     */
    createExtraColumn: function () {
        var me = this,
            column = {
                text: me.snippets.columns.value,
                dataIndex: 'value',
                flex: 2
            };

        if (me.useColorPicker) {
            column.renderer = Ext.bind(me.colorColumnRenderer, me);
            return [column];
        }

        if (me.useImagePicker) {
            column.renderer = Ext.bind(me.imageColumnRenderer, me);
            return [column];
        }

        return false;
    },

    /**
     * @param { string } value
     * @returns { string }
     */
    colorColumnRenderer: function (value) {
        return [
            '<div style="padding: 2px">',
            '<div style="float:left; width: 15px; height: 15px; background: ',
            value,
            ';"></div>',
            '<div style="float:left; padding: 1px 0 0 4px">',
            value,
            '</div>'
        ].join('');
    },

    /**
     * @param { string } value
     * @returns { string }
     */
    imageColumnRenderer: function (value) {
        return [
            '<div style="padding: 2px">',
            '<img height="40" src="',
            value,
            '"></div>'
        ].join('');
    },

    /**
     * @returns { * }
     */
    createActionColumn: function () {
        var me = this,
            items = me.createActionColumnItems();

        return {
            xtype: 'actioncolumn',
            width: items.length * 30,
            items: items,
            hideable: false,
            sortable: false,
            menuDisabled: true
        };
    },

    /**
     * @returns { *[] }
     */
    createActionColumnItems: function () {
        var me = this;

        return [
            me.createDeleteActionColumn(),
            me.createEditActionColumn()
        ];
    },

    /**
     * @returns { * }
     */
    createDeleteActionColumn: function () {
        var me = this;

        return {
            iconCls: 'sprite-minus-circle-frame',
            action: 'delete',
            handler: function (view, rowIndex, colIndex, item, opts, record) {
                me.removeRecord(record);
            }
        };
    },

    /**
     * @returns { * }
     */
    createEditActionColumn: function () {
        var me = this;

        return {
            iconCls: 'sprite-pencil',
            action: 'edit',
            handler: function (view, rowIndex, colIndex, item, opts, record) {
                me.editRecord(record);
            }
        };
    },

    /**
     * @returns { *[] }
     */
    createDockedItems: function () {
        var me = this,
            topBar;

        topBar = Ext.create('Ext.toolbar.Toolbar', {
            layout: 'vbox',
            dock: 'top',
            ui: 'shopware-ui',
            items: [
                me.createAddButton()
            ]
        });

        return [topBar];
    },

    /**
     * @returns { Ext.button.Button | * }
     */
    createAddButton: function () {
        var me = this;

        me.addButton = Ext.create('Ext.button.Button', {
            text: me.snippets.addButton,
            iconCls: 'sprite-plus-circle-frame',
            handler: Ext.bind(me.addButtonHandler, me)
        });

        return me.addButton;
    },

    addButtonHandler: function () {
        var me = this,
            record = Ext.create('Shopware.apps.SwagCustomProducts.model.Value', {
                position: me.grid.getStore().getCount(),
                isOnceSurcharge: false,
                surcharge: 0.00
            });

        me.editRecord(record);
    },

    /**
     * Renderer for ddIndicatorColumn
     *
     * @param { string } value
     * @param { * } metadata
     * @returns { string }
     */
    renderSortHandleColumn: function (value, metadata) {
        var me = this;

        metadata.tdAttr = Ext.String.format('data-qtip="[0]"', me.snippets.columns.hintDragDrop);

        return '<div style="cursor: n-resize;">&#009868;</div>';
    },

    /**
     * @param { boolean } value
     * @returns { string }
     */
    booleanColumnRenderer: function (value) {
        var checked = 'sprite-ui-check-box-uncheck';
        if (value === true) {
            checked = 'sprite-ui-check-box';
        }
        return '<span style="display:block; margin: 0 auto; height:16px; width:16px;" class="' + checked + '"></span>';
    },

    /**
     * @param { Shopware.apps.SwagCustomProducts.model.Value } record
     */
    removeRecord: function (record) {
        var me = this;

        me.grid.getStore().remove(record);
    },

    /**
     * @param { Shopware.apps.SwagCustomProducts.model.Value } record
     */
    editRecord: function (record) {
        var me = this,
            window = Ext.create('Shopware.apps.SwagCustomProducts.view.values.Window', {
                parent: me.parent,
                useColorPicker: me.useColorPicker,
                useImagePicker: me.useImagePicker,
                record: record,
                valueGrid: me.grid,
                optionStore: me.up('window').optionStore,
                optionType: me.type
            });

        window.show();
    }
});
// {/block}
