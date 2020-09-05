/**
 * Displays a grid which holds all options for a template.
 */

// {namespace name="backend/swag_custom_products/detail/option_list"}
// {block name="backend/swag_custom_products/view/detail/option_list"}
Ext.define('Shopware.apps.SwagCustomProducts.view.detail.OptionList', {
    extend: 'Shopware.grid.Panel',

    alias: 'widget.swag-custom-products-option-list',

    snippets: {
        title: '{s name="title"}Options{/s}',
        header: {
            name: '{s name="header/name"}Name{/s}',
            typeId: '{s name="header/type_id"}Type{/s}',
            position: '{s name="header/position"}Position{/s}'
        },
        hintDragAndDrop: '{s name="hint_dragdrop"}{/s}'
    },

    /**
     * Overwrite the searchEvent because we need to prevent a dirtyStore on save
     *
     * @overwrite
     * @param field
     * @param value
     */
    searchEvent: function (field, value) {
        var me = this;

        me.store.filter([
            { property: 'name', value: new RegExp('.*' + value + '.*', 'i') }
        ]);

        me.store.clearFilter(true);
    },

    /**
     * Overrides the initComponent to add the title and load the option store.
     */
    initComponent: function () {
        var me = this;

        me.viewConfig = me.createDragAndDrop();
        me.title = me.snippets.title;
        me.callParent(arguments);
    },

    /**
     * @returns { Object }
     */
    configure: function () {
        var me = this;

        return {
            detailWindow: 'Shopware.apps.SwagCustomProducts.view.option.Window',
            pagingbar: false,
            rowEditing: true,
            columns: {
                name: {
                    header: me.snippets.header.name,
                    editor: me.getNameEditor(),
                    sortable: false
                },
                type: {
                    header: me.snippets.header.typeId,
                    renderer: me.onRenderTypeColumn,
                    editor: false,
                    sortable: false
                }
            }
        };
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
     * @param value
     * @returns { * }
     */
    onRenderTypeColumn: function (value) {
        var typeTransLater = Ext.create('Shopware.apps.SwagCustomProducts.view.components.TypeTranslator');

        return typeTransLater.getTranslation(value);
    },

    /**
     * @overwrite
     *
     * @returns { Ext.button.Button }
     */
    createAddButton: function () {
        var me = this,
            button = me.callParent(arguments);

        button.handler = Ext.bind(me.editAddButton, me);

        return button;
    },

    editAddButton: function () {
        var me = this,
            optionRecord;

        optionRecord = Ext.create('Shopware.apps.SwagCustomProducts.model.Option', {
            position: me.getStore().getCount()
        });

        Ext.create('Shopware.apps.SwagCustomProducts.view.option.Window', {
            record: optionRecord,
            templateRecord: me.templateRecord,
            optionStore: me.getStore()
        }).show();
    },

    /**
     * @overwrite
     */
    createEditColumn: function () {
        var me = this,
            editButton = me.callParent(arguments);

        editButton.handler = Ext.bind(me.editButtonHandler, me);

        return editButton;
    },

    /**
     * @param view
     * @param rowIndex
     * @param colIndex
     * @param item
     * @param opts
     * @param record
     */
    editButtonHandler: function(view, rowIndex, colIndex, item, opts, record) {
        var me = this;

        Ext.create('Shopware.apps.SwagCustomProducts.view.option.Window', {
            record: record,
            templateRecord: me.templateRecord,
            optionStore: me.getStore()
        }).show();
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
                dragGroup: 'optionDD',
                dropGroup: 'optionDD',
                onViewRender: me.onRenderDragAndDropView
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
     * Updates all position fields to the current index.
     */
    onDrop: function () {
        var me = this;

        me.getStore().each(function (option, index) {
            option.set('position', index);
        });
    },

    /**
     * @returns { * }
     */
    createColumns: function () {
        var me = this,
            columns = me.callParent(arguments),
            ddIndicatorColumn = {
                header: '&#009868',
                width: 24,
                renderer: me.renderSortHandleColumn,
                hideable: false,
                sortable: false,
                menuDisabled: true
            };

        columns = Ext.Array.insert(columns, 0, [ddIndicatorColumn]);

        return columns;
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

        metadata.tdAttr = Ext.String.format('data-qtip="[0]"', me.snippets.hintDragAndDrop);

        return '<div style="cursor: n-resize;">&#009868;</div>';
    },

    /**
     * Use own selection-model to fix an issue with the drag'n'drop-plugin and selection-model
     * @overwrite
     */
    createSelectionModel: function () {
        var me = this,
            selModel;

        selModel = Ext.create('Shopware.apps.SwagCustomProducts.view.components.OptionsSelectionModel', {
            listeners: {
                selectionchange: function (selModel, selection) {
                    return me.fireEvent(me.eventAlias + '-selection-changed', me, selModel, selection);
                }
            }
        });

        me.fireEvent(me.eventAlias + '-selection-model-created', me, selModel);

        return selModel;
    },

    /**
     * Fixture for the drag and drop plugin due to using it with the selection model.
     *
     * @see http://stackoverflow.com/questions/18478266/extjs-drag-and-drop-single-item-on-grid-with-checkbox-model
     * @param view
     */
    onRenderDragAndDropView: function (view) {
        var me = this,
            scrollEl;

        if (me.enableDrag) {
            if (me.containerScroll) {
                scrollEl = view.getEl();
            }

            me.dragZone = new Ext.view.DragZone({
                view: view,
                ddGroup: me.dragGroup || me.ddGroup,
                dragText: me.dragText,
                containerScroll: me.containerScroll,
                scrollEl: scrollEl,
                // Remember if the row was selected originally or not
                onBeforeDrag: function(data) {
                    var view = data.view,
                        selectionModel = view.getSelectionModel(),
                        record = view.getRecord(data.item);

                    if (!selectionModel.isSelected(record)) {
                        data.rowSelected = false;
                    }
                    return true;
                },

                onInitDrag: function(x, y) {
                    var me = this,
                        data = me.dragData,
                        view = data.view,
                        selectionModel = view.getSelectionModel(),
                        record = view.getRecord(data.item);

                    // Deselect dragged record
                    if (selectionModel.isSelected(record) && data.rowSelected == false) {
                        selectionModel.deselect(record, true);
                    }

                    // Add the record for the drag and drop selection
                    data.records = [record];
                    me.ddel.update(me.getDragText());
                    me.proxy.update(me.ddel.dom);
                    me.onStartDrag(x, y);
                    return true;
                }
            });
        }

        if (me.enableDrop) {
            me.dropZone = new Ext.grid.ViewDropZone({
                view: view,
                ddGroup: me.dropGroup || me.ddGroup,
                // Change the selection at the end of this method
                handleNodeDrop: function(data, record, position) {
                    var view = this.view,
                        store = view.getStore(),
                        index, records, i, len;

                    if (data.copy) {
                        records = data.records;
                        data.records = [];

                        for (i = 0, len = records.length; i < len; i++) {
                            data.records.push(records[i].copy());
                        }
                    } else {
                        data.view.store.remove(data.records, data.view === view);
                    }

                    if (record && position) {
                        index = store.indexOf(record);
                        if (position !== 'before') {
                            index++;
                        }
                        store.insert(index, data.records);
                    } else {
                        store.add(data.records);
                    }

                    if (view != data.view) {
                        view.getSelectionModel().select(data.records);
                    }
                }
            });
        }
    }
});
// {/block}
