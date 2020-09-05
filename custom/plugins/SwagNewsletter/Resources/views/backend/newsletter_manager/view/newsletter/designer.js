//{namespace name="backend/swag_newsletter/main"}

/**
 * Shopware UI - The emotion designer adapted for the newsletter module
 */
//{block name="backend/newsletter_manager/view/newsletter/designer"}
Ext.define('Shopware.apps.NewsletterManager.view.newsletter.Designer', {
    extend: 'Ext.panel.Panel',
    title: '{s name=title/designer_tab}Designer{/s}',
    alias: 'widget.newsletter-designer',
    layout: 'column',
    autoScroll: true,
    bodyPadding: 20,
    defaults: {
        bodyBorder: 0
    },
    border: false,

    /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
    initComponent: function () {
        var me = this;

        me.dataviewStore = me.createDataViewStore();
        me.dataView = me.createGridView();
        me.addEvents(
            /**
             * @param me
             * @param record
             * @param component
             * @param fields
             * @param newsletterRecord
             */
            'openSettingsWindow',

            /**
             * Fired when the user changes adds a new newsletter element
             * @param field
             */
            'formChanged'
        );
        me.items = [me.dataView];

        me.callParent();
    },

    /**
     * Creates a store holding the config and the elements
     * @return
     */
    createDataViewStore: function () {
        var me = this,
            settings = { cols: 1, rows: 40 },
            elements = me.newsletterRecord.getElements();

        if (elements instanceof Ext.data.Store && elements.data.length > 0) {
            elements = elements.data.items;
        } else {
            elements = [];
        }

        return Ext.create('Ext.data.Store', {
            fields: [
                'settings', 'elements'
            ],
            data: [
                {
                    settings: settings,
                    elements: elements
                }
            ]
        });
    },

    /**
     * creates the grid view
     */
    createGridView: function () {
        var me = this, dataview;

        me.dataViewTemplate = me.createGridTemplate(me.getId());

        dataview = Ext.create('Ext.view.View', {
            store: me.dataviewStore,
            columnWidth: 1,
            cls: 'x-newsletter-grid-outer-container',
            tpl: me.dataViewTemplate,
            style: 'position: absolute; top: 15px; left: 15px; overflow-y: hidden',
            listeners: {
                scope: me,
                afterrender: me.addGridEvents,
                refresh: me.createDragZoneForEachElement
            }
        });

        return dataview;
    },

    /**
     * Creates the grid view template
     * @param id
     * @return Ext.XTemplate
     */
    createGridTemplate: function (id) {

        return new Ext.XTemplate(
            '{literal}<tpl for=".">',
                '<div class="x-newsletter-grid-inner-container listing-{settings.cols}col">',

                    // Underlying gridsystem - e.g. first layer
                    '<div class="x-newsletter-grid-first-layer">',
                        '{[this.createRows(values.settings)]}',
                    '</div>',

                    // Actual layer which contains the elements
                    '<div class="x-newsletter-grid-second-layer" style="height:{[this.getGridHeight(values.settings)]}px">',
                        '{[this.createGridElements(values)]}',
                    '</div>',
                '</div>',
            '</tpl>{/literal}',
            {
                /**
                 * Property which holds the id of the parent element to
                 * fly through the DOM to get the base width of a single column.
                 * @integer
                 */
                parentId: id,

                /**
                 * Helper method which returns the height of the complete grid.
                 *
                 * @private
                 * @param [object] settings - Grid Settings
                 * @return [integer] total height of the grid (in pixels)
                 */
                getGridHeight: function (settings) {
                    return settings.rows * 45;
                },

                /**
                 * Helper method which creates the rows in the grid.
                 *
                 * @private
                 * @param [object] settings - Grid Settings
                 * @return [string] HTML string of the generated rows
                 */
                createRows: function (settings) {
                    var me = this, rows = '';

                    for (var i = 1; i <= settings.rows; i++) {
                        if (i === settings.rows) {
                            rows += '<div class="row row-last">' + me.createColumns(settings.cols) + '</div>';
                        } else {
                            rows += '<div class="row">' + me.createColumns(settings.cols) + '</div>';
                        }
                    }
                    return rows;
                },

                /**
                 * Helper method which creates the columns in the grid.
                 *
                 * @private
                 * @param settings
                 * @return [string] HTML string of the generated columns
                 */
                createColumns: function (cols) {
                    var columns = '';

                    for (var i = 1; i <= cols; i++) {
                        if (i === cols) {
                            columns += '<div class="col col-1x1 col-last"></div>';
                        } else {
                            columns += '<div class="col col-1x1"></div>';
                        }
                    }
                    columns += '<div class="x-clear"></div>';

                    return columns;
                },

                createGridElements: function (values) {
                    var elements = '',
                        els = values.elements,
                        baseElement = Ext.get(this.parentId),
                        baseWidth = (baseElement.getWidth() - 40) / values.settings.cols,
                        dh = new Ext.dom.Helper;

                    Ext.each(els, function (element) {
                        var width = (element.get('endCol') - element.get('startCol')) + 1,
                            height = (element.get('endRow') - element.get('startRow')) + 1,
                            isNew = '';

                        if (element.get('isNew') === true) {
                            isNew = ' *';
                        }

                        var baseCls = 'col-' + width + 'x' + height,
                            component = element.getComponent().first();

                        height = height * 45 + 'px';

                        var specs = {
                            cls: baseCls + ' x-newsletter-element ' + (component.get('cls').length ? ' ' + component.get('cls') : ''),
                            tag: 'div',
                            style: {
                                top: (element.get('startRow') - 1) * 45 + 'px',
                                left: (element.get('startCol') - 1) * baseWidth + 'px',
                                height: height,
                                'line-height': height
                            },
                            children: [
                                { tag: 'div', cls: 'x-newsletter-element-handle' },
                                { tag: 'div', cls: 'x-newsletter-element-inner', html: component.get('name') + isNew },
                                { tag: 'div', cls: 'x-newsletter-element-delete', 'data-emotionId': element.internalId }
                            ]
                        };
                        elements += dh.createHtml(specs);
                    });

                    return elements;
                }
            }
        );
    },

    /**
     * Adds additional events to the dataview
     *
     * @event afterrender
     * @private
     * @param [object] view - Ext.view.View
     * @return voud
     */
    addGridEvents: function () {
        var me = this;

        /**
         * Patching the height of the emotion outer container
         * which fix the emotion designer d'n'd functionality temporarily.
         */
        Ext.defer(function () {
            var height = me.dataViewTemplate.getGridHeight(me.dataviewStore.getAt(0).data.settings);
            me.dataView.getEl().setHeight(height);
        }, 200, me);

        me.dataView.getEl().on({
            'click': {
                delegate: '.x-newsletter-element-delete',
                fn: me.onDeleteElement,
                scope: me
            },
            'dblclick': {
                delegate: '.x-newsletter-element',
                fn: me.onOpenSettingsWindow,
                scope: me
            }
        });

        me.createDropZone(me);
    },

    /**
     * Event listener method which deletes an element from the grid.
     *
     * @event click
     * @param [object] event - Ext.EventObjImpl
     * @param [object] el - DOM object of the clicked element
     */
    onDeleteElement: function (event, el) {
        var me = this,
            element = Ext.get(el),
            id = element.getAttribute('data-emotionId'),
            store = me.dataviewStore.getAt(0).get('elements');

        Ext.each(store, function (record) {
            if (record.internalId == id) {
                Ext.Array.remove(store, record);
                return false;
            }
        });
        element.parent().destroy();

        // force revalidation of newsletter elements and the settings form
        me.fireEvent('formChanged');
    },

    createDropZone: function (view) {
        var me = this, scrollState;

        var proxyElement;
        me.dropZone = new Ext.dd.DropZone(view.dataView.getEl(), {
            ddGroup: 'emotion-dd',

            getTargetFromEvent: function (e) {
                return e.getTarget(view.rowSelector);
            },

            // While over a target node, return the default drop allowed class which
            // places a "tick" icon into the drag proxy.
            onNodeOver: function (target, dd, e, data) {
                var stage = view.dataView.getEl(),
                    x = e.getX(),
                    y = e.getY(),
                    id = me.getId(),
                    colHeight = 44,
                    colWidth = (Ext.get(id).getWidth() - 40) / me.dataviewStore.getAt(0).data.settings.cols,
                    startCol, startRow, record = data.draggedRecord,
                    entry = me.dataviewStore.getAt(0), elements = entry.get('elements');

                x = x - stage.getX();
                y = y - stage.getY();

                // The element isn't in the drop area, so return "false"
                if (y < 0 || x < 0) {
                    Ext.get(target).addCls('x-newsletter-collision');
                    return Ext.dd.DropZone.prototype.dropNotAllowed;
                }

                // Get the start and end points
                startRow = Math.floor(y / colHeight) + 1;
                startCol = Math.floor(x / colWidth) + 1;

                // Create preview element
                if (record.get('startRow') && record.get('endRow') && record.get('startCol') && record.get('endCol')) {

                    // Create the preview element for existing elements on the stage
                    var colSpan = (record.get('endCol') - record.get('startCol')),
                        rowSpan = (record.get('endRow') - record.get('startRow')),
                        width = colSpan + 1,
                        height = rowSpan + 1;

                    this.createPreviewElement(Math.floor(width * colWidth), Math.floor(height * colHeight), startCol - 1, startRow - 1, colWidth);
                } else {

                    // Create the preview element for newly added elements
                    var endRow = startRow,
                        endCol = startCol,
                        rowSpan = 1,
                        colSpan = 1,
                        width = colSpan * colWidth,
                        height = Math.floor(rowSpan * colHeight);

                    this.createPreviewElement(width, height, startCol - 1, startRow - 1, colWidth);
                }
            },

            /**
             * Helper method which creates an proxy preview element to give the
             * user a visually response for it's drag action.
             *
             * @public
             * @param [integer] width - Width of the element
             * @param [integer] height - Height of the element
             * @param [integer] left - Left offset of the element
             * @param [integer] top - Top offset of the element
             * @param [integer] colWidth - calculated column width
             */
            createPreviewElement: function (width, height, left, top, colWidth) {
                var firstLayer = view.dataView.getEl().down('.x-newsletter-grid-first-layer');

                if (proxyElement) {
                    proxyElement.remove();
                    proxyElement = null;
                }

                proxyElement = document.createElement('div');
                proxyElement = Ext.get(proxyElement);
                proxyElement.addCls(Ext.baseCSSPrefix + 'shopware-proxy-state-element');
                proxyElement.setStyle({
                    width: width + 'px',
                    height: height + 'px',
                    left: Math.floor(left * colWidth) + 'px',
                    top: Math.floor(top * 45) + 'px'
                });

                proxyElement.appendTo(firstLayer);
            },

            // On node drop we can interrogate the target to find the underlying
            // application object that is the real target of the dragged data.
            onNodeDrop: function (target, dd, e, data) {
                var stage = view.dataView.getEl(),
                    x = e.getX(),
                    y = e.getY(),
                    id = me.getId(),
                    colHeight = 44,
                    colWidth = (Ext.get(id).getWidth() - 40) / me.dataviewStore.getAt(0).data.settings.cols,
                    startCol, startRow, record = data.draggedRecord, endRow, endCol,
                    entry = me.dataviewStore.getAt(0), elements = entry.get('elements');

                if (record.get('cls') == "newsletter-suggest-element") {
                    // Ext.each will return true, if no element returned false
                    var found = Ext.each(elements, function (element) {
                        if (record.get('id') == element.get('componentId')) {
                            Shopware.Notification.createGrowlMessage("{s name=premiumNewsletter/Error}Error{/s}",
                                "{s name=premiumNewsletter/onlyOneSuggestContainerAllowed}Its not possible to have more than one suggest container per newsletter{/s}",
                                "Premium Newsletter"
                            );
                            return false;
                        }
                    });

                    if (found !== true) {
                        // remove the preview element
                        if (proxyElement) {
                            proxyElement.remove();
                            proxyElement = null;
                        }
                        return false;
                    }
                }

                x = x - stage.getX();
                y = y - stage.getY();

                // The element isn't in the drop area, so return "false"
                if (y < 0 || x < 0) {
                    return false;
                }

                // Get the start and end points
                startRow = Math.floor(y / colHeight) + 1;
                startCol = Math.floor(x / colWidth) + 1;
                endRow = startRow;
                endCol = startCol;

                /**
                 * The record comes from the element librarys
                 * Set startRow, endRow, startCol and endCol for collision-detection
                 */
                if (record.$className !== 'Shopware.apps.NewsletterManager.model.NewsletterElement') {
                    var elEndRow = startRow;

                    record.set({
                        startRow: startRow,
                        endRow: elEndRow,
                        startCol: startCol,
                        endCol: startCol
                    });
                }

                if (!this.isDroppableElement(record, startRow, startCol, true)) {
                    return false;
                }

                if (proxyElement) {
                    proxyElement.remove();
                    proxyElement = null;
                }

                /**
                 * The record comes from the element librarys
                 */
                if (record.$className !== 'Shopware.apps.NewsletterManager.model.NewsletterElement') {
                    // Create new record in the the dataview store

                    var elEndRow = startRow;

                    var model = Ext.create('Shopware.apps.NewsletterManager.model.NewsletterElement', {
                        componentId: record.get('id'),
                        id: '',
                        data: {},
                        // Mark the element as being newly created from element library
                        isNew: true,
                        name: record.get('name'),
                        startRow: startRow,
                        endRow: elEndRow,
                        startCol: startCol,
                        endCol: startCol
                    });

                    model.getComponent().add(record);
                    elements.push(model);
                    me.fireEvent('formChanged');

                    /**
                     * The record is an element on the stage and just need to get new row and col properties
                     */
                } else {
                    var cols = (record.data.endCol - record.data.startCol),
                        rows = (record.data.endRow - record.data.startRow);

                    record.set({
                        startRow: startRow,
                        endRow: startRow + rows,
                        startCol: startCol,
                        endCol: startCol + cols
                    });
                }

                me.dataView.refresh();

                // Remove class from the sourceEl element
                Ext.get(data.sourceEl).removeCls('dragged');

                return true;
            },

            isDroppableElement: function (record, startRow, startCol, returnBoolean) {
                var rowHeight = (record.get('endRow') - record.get('startRow')),
                    colWidth = (record.get('endCol') - record.get('startCol')),
                    endRow = startRow + rowHeight,
                    endCol = startCol + colWidth,
                    result = true;

                for (var r = startRow; endRow >= r; r++) {
                    for (var c = startCol; endCol >= c; c++) {
                        if (!this.isCellAvailable(r, c, record.internalId)) {
                            result = false;
                        }
                    }
                }

                if (result) {
                    return (returnBoolean) ? result : Ext.dd.DropZone.prototype.dropAllowed;
                }
                return (returnBoolean) ? result : Ext.dd.DropZone.prototype.dropNotAllowed;
            },

            isCellAvailable: function (row, col, id) {
                var entry = me.dataviewStore.getAt(0), elements = entry.get('elements'),
                    maxCols = me.dataviewStore.getAt(0).data.settings.cols,
                    maxRows = me.dataviewStore.getAt(0).data.settings.rows;

                if (row > maxRows || col > maxCols) {
                    return false;
                }

                var error = false;
                Ext.each(elements, function (item) {
                    if (item.internalId == id) {
                        return true;
                    }

                    if (row >= item.get('startRow') && row <= item.get('endRow')) {
                        if (col >= item.get('startCol') && col <= item.get('endCol')) {
                            error = true;
                        }
                    }

                    if (col >= item.get('startCol') && col <= item.get('endCol')) {
                        if (row >= item.get('startRow') && row <= item.get('endRow')) {
                            error = true;
                        }
                    }

                    if (error) {
                        return !error;
                    }
                });
                return !error;
            }
        });
    },

    createDragZoneForEachElement: function () {
        var me = this,
            view = me.dataView.getEl(),
            elements = view.query('.x-newsletter-element'),
            id = me.getId(),
            dataViewData = me.dataviewStore.getAt(0).data.settings,
            cellHeight = 45,
            cellWidth = (Ext.get(id).getWidth() - 40) / dataViewData.cols;

        Ext.each(elements, function (item) {
            var element = Ext.get(item);

            // The element has already a drag zone
            if (element.hasCls('x-draggable')) {
                return false;
            }

            element.dragZone = new Ext.dd.DragZone(element, {
                ddGroup: 'emotion-dd',

                /**
                 * Checks if the element is not in the resize mode. If it is in it, the
                 * dd functionality will be disabled.
                 *
                 * @param [object] data - drag and drop data from getDragData
                 * @return [boolean]
                 */
                onBeforeDrag: function (data) {
                    return !Ext.get(data.sourceEl).hasCls('x-resizable-over');
                },

                getDragData: function () {
                    var sourceEl = item, d;

                    var id = element.child('.x-newsletter-element-delete').getAttribute('data-emotionId'),
                        records = me.dataviewStore.getAt(0).get('elements'), record;

                    var proxy = element.dragZone.proxy;
                    if (!proxy.getEl().hasCls(Ext.baseCSSPrefix + 'shopware-dd-proxy')) {
                        proxy.getEl().addCls(Ext.baseCSSPrefix + 'shopware-dd-proxy')
                    }

                    Ext.each(records, function (item) {
                        if (item.internalId == id) {
                            record = item;
                            return false;
                        }

                    });
                    if (sourceEl) {
                        d = sourceEl.cloneNode(true);
                        d.id = Ext.id();

                        return {
                            ddel: d,
                            sourceEl: sourceEl,
                            repairXY: Ext.fly(sourceEl).getXY(),
                            sourceStore: me.dataviewStore,
                            draggedRecord: record
                        }
                    }
                },

                getRepairXY: function () {
                    return this.dragData.repairXY;
                }
            });

            // Add class which indicates if the element has already a drag zone
            element.addCls('x-draggable');
        });
    },

    /**
     * Called after a element was double clicked
     * @param event
     * @param el
     */
    onOpenSettingsWindow: function (event, el) {
        var me = this,
            element = Ext.get(el),
            id = element.child('.x-newsletter-element-delete').getAttribute('data-emotionId'),
            store = me.dataviewStore.getAt(0).get('elements'),
            record;

        Ext.each(store, function (item) {
            if (item.internalId == id) {
                record = item;
            }
        });
        var component = record.getComponent().first(),
            fields = component.getComponentFields();

        me.fireEvent('openSettingsWindow', me, record, component, fields, me.emotion);
    },

    onBeforeResize: function (resizer, width, height, event) {
        var element = resizer.el,
            me = this,
            id = element.child('.x-newsletter-element-delete').getAttribute('data-emotionId'),
            store = me.dataviewStore.getAt(0).get('elements'),
            record;

        Ext.each(store, function (item) {
            if (item.internalId == id) {
                record = item;
            }
        });

        record.data.initialStartCol = record.get('startCol');
        record.data.initialEndCol = record.get('endCol');
        record.data.initialStartRow = record.get('startRow');
        record.data.initialEndRow = record.get('endRow');
        record.data.isDroppable = false;
        record.data.needsReset = true;

        return true;
    },

    onResizeDrag: function (resizer, width, height, event) {
        var element = resizer.el,
            me = this,
            id = element.child('.x-newsletter-element-delete').getAttribute('data-emotionId'),
            store = me.dataviewStore.getAt(0).get('elements'),
            dataViewData = me.dataviewStore.getAt(0).data.settings,
            cellHeight = 45,
            cellWidth = (Ext.get(me.getId()).getWidth() - 40) / dataViewData.cols,
            colSpan = width / cellWidth,
            rowSpan = height / cellHeight,
            baseCls, record;

        Ext.each(store, function (item) {
            if (item.internalId == id) {
                record = item;
            }
        });

        colSpan = Math.round(colSpan);
        rowSpan = Math.round(rowSpan);

        var component = record.getComponent().first();
        if (component.get('xType') == 'emotion-components-article') {
            if (rowSpan == 1) {
                rowSpan = rowSpan + 1;
            }
        }
        record.set({
            endCol: colSpan + record.get('startCol') - 1,
            endRow: rowSpan + record.get('startRow') - 1
        });

        var component = record.getComponent().first();
        baseCls = 'col-' + colSpan + 'x' + rowSpan;

        element.set({
            'cls': baseCls + ' x-newsletter-element ' + (component.get('cls').length ? ' ' + component.get('cls') : ''),
            'style': {
                'line-height': height + 'px'
            }
        });

        record.data.isDroppable = me.dropZone.isDroppableElement(record, record.get('startRow'), record.get('startCol'), true);
    },

    /**
     * Saves the new size of the element on the stage
     *
     * @public
     * @param resizer
     * @param width
     * @param height
     */
    onResize: function (resizer, width, height) {
        var element = resizer.el,
            me = this,
            view = me.dataView.getEl(),
            id = element.child('.x-newsletter-element-delete').getAttribute('data-emotionId'),
            store = me.dataviewStore.getAt(0).get('elements'),
            dataViewData = me.dataviewStore.getAt(0).data.settings,
            cellHeight = 45,
            cellWidth = (Ext.get(me.getId()).getWidth() - 40) / dataViewData.cols,
            colSpan = width / cellWidth,
            rowSpan = height / cellHeight,
            record, baseCls;

        Ext.each(store, function (item) {
            if (item.internalId == id) {
                record = item;
            }
        });

        var component = record.getComponent().first();
        if (component.get('xType') == 'emotion-components-article') {
            if (rowSpan == 1) {
                rowSpan = rowSpan + 1;
            }

            record.set({
                endRow: rowSpan + record.get('startRow') - 1
            });
            resizer.target.setSize((record.get('endCol') - record.get('startCol') + 1) * cellWidth, rowSpan * cellHeight);
            element.set({
                style: {
                    'line-height': rowSpan * cellHeight + 'px'
                }
            })
        }

        if (!record.data.isDroppable && record.data.needsReset) {
            record.set({
                endRow: record.data.initialEndRow,
                endCol: record.data.initialEndCol
            });
            record.data.needsReset = false;
            resizer.resizeTo((record.get('endCol') - record.get('startCol') + 1) * cellWidth, (record.get('endRow') - record.get('startRow') + 1) * cellHeight);
        }
    }
});
//{/block}