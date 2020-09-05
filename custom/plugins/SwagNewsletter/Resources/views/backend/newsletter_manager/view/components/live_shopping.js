//{namespace name=backend/swag_newsletter/main}
//{block name="backend/newsletter_manager/view/components/live_shopping"}
Ext.define('Shopware.apps.NewsletterManager.view.components.LiveShopping', {
    extend: 'Shopware.apps.NewsletterManager.view.components.Base',
    alias: 'widget.newsletter-components-live-shopping',
    layout: 'anchor',

    snippets: {
        headline: '{s name=liveshopping_title}Title{/s}',
        number: '{s name=liveshopping_article_display}Number of LiveShopping products{/s}',
        add_article: '{s name=addArticle}Add article{/s}',
        article_administration: '{s name=article_administration}Product administration{/s}',
        actions: '{s name=actions}Action(s){/s}',
        name: '{s name=name}Product name{/s}',
        info: '{s name=liveShopping_Info}The number of products is only used if no product is selected{/s}'
    },

    /**
     * Initiliaze the component.
     *
     * @public
     * @return void
     */
    initComponent: function () {
        var me = this;

        me.articleNumberSearch = me.createArticleSearch('articleId', 'name', 'articleOrderNumber');
        me.articleNameSearch = me.createArticleSearch('name', 'articleId', 'articleName');

        me.callParent(arguments);
        me.add(me.createArticleFieldset());
        me.getGridData();
        me.refreshHiddenValue();
    },

    /**
     *
     * @returns { Array }
     */
    createFormElements: function () {
        var me = this,
            items = me.callParent(arguments);

        Ext.each(items, function (field) {
            if (field.name === 'headline') {
                field.fieldLabel = me.snippets.headline;
            } else if (field.name === 'number') {
                field.fieldLabel = me.snippets.number;
                field.supportText = me.snippets.info;

                if (!field.value) {
                    field.value = 2;
                }
            }
        });

        return items;
    },

    /**
     * Creates the fieldset which holds the article administration. The method
     * also creates the article store and registers the drag and drop plugin
     * for the grid.
     *
     * @public
     * @return [object] Ext.form.FieldSet
     */
    createArticleFieldset: function () {
        var me = this;
        me.addArticleButton = Ext.create('Ext.Button', {
            action: 'addArticle',
            anchor: '30%',
            cls: 'primary',
            text: me.snippets.add_article,
            margin: '0 0 20 0',
            handler: function () {
                me.onAddArticleToGrid(me.articleGrid, me.cellEditing);
            }
        });

        me.articleStore = Ext.create('Ext.data.Store', {
            fields: ['position', 'type', 'articleId', 'name']
        });

        me.ddGridPlugin = Ext.create('Ext.grid.plugin.DragDrop');

        me.cellEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            clicksToEdit: 2
        });

        me.setupRowEditingEvents();

        me.articleGrid = Ext.create('Ext.grid.Panel', {
            columns: me.createColumns(),
            autoScroll: true,
            store: me.articleStore,
            height: 200,
            plugins: [me.cellEditing],
            viewConfig: {
                plugins: [me.ddGridPlugin],
                listeners: {
                    scope: me,
                    drop: me.onRepositionArticle
                }
            },
            listeners: {
                scope: me,
                edit: function () {
                    me.refreshHiddenValue();
                }
            }
        });

        return me.articleFieldset = Ext.create('Ext.form.FieldSet', {
            title: me.snippets.article_administration,
            layout: 'anchor',
            defaults: { anchor: '100%' },
            items: [me.addArticleButton, me.articleGrid]
        });
    },

    /**
     * Helper function to register and handle various events regarding the rowediting plugin
     */
    setupRowEditingEvents: function () {
        var me = this;

        //register listener on the before edit event to set the article name and number manually into the row editor.
        me.cellEditing.on('beforeedit', function (editor, e) {
            var columns = editor.editor.items.items;

            columns[1].setValue(e.record.get('type'));
            columns[2].setValue(e.record.get('articleId'));
            columns[3].setValue(e.record.get('name'));
        });

        // hide the search fields when editing is finished
        me.cellEditing.on('edit', function (editor, e) {
            me.articleNumberSearch.getDropDownMenu().hide();
            me.articleNameSearch.getDropDownMenu().hide();

            e.record.set('name', e.newValues.name);
            e.record.set('articleId', e.newValues.articleId);
            e.record.raw.isNew = false;
            me.articleNumberSearch.setValue('');
            me.articleNameSearch.setValue('');
        });

        // throw away the record, if editing is canceled and record was just created
        me.cellEditing.on('canceledit', function (grid, eOpts) {
            var record = eOpts.record,
                store = eOpts.store;

            if (record.raw.isNew) {
                store.remove(record);
            }
            me.articleNumberSearch.getDropDownMenu().hide();
            me.articleNameSearch.getDropDownMenu().hide();
            me.articleNumberSearch.setValue('');
            me.articleNameSearch.setValue('');
            me.refreshHiddenValue();
        });

        // set article's number and name when a article was selected
        me.articleNumberSearch.on('valueselect', function (field, value, hiddenValue, record) {
            var columns = me.cellEditing.editor.items.items;
            columns[2].setValue(record.get('articleId'));
            columns[3].setValue(record.get('name'));
        });
        me.articleNameSearch.on('valueselect', function (field, value, hiddenValue, record) {
            var columns = me.cellEditing.editor.items.items;
            columns[2].setValue(record.get('articleId'));
            columns[3].setValue(record.get('name'));
        });
    },

    /**
     * Helper method which creates the column model
     * for the article administration grid panel.
     *
     * @public
     * @return Array computed columns
     */
    createColumns: function () {
        var me = this, snippets = me.snippets;

        return [
            {
                header: '&#009868;',
                width: 24,
                hideable: false,
                renderer: me.renderSorthandleColumn
            }, {
                dataIndex: 'type',
                hidden: true
            }, {
                dataIndex: 'articleId',
                hidden: true
            }, {
                dataIndex: 'name',
                header: snippets.name,
                flex: 2,
                editor: me.articleNameSearch
            }, {
                xtype: 'actioncolumn',
                header: snippets.actions,
                width: 60,
                items: [
                    {
                        iconCls: 'sprite-minus-circle',
                        action: 'delete-article',
                        scope: me,
                        handler: me.onDeleteArticle
                    }
                ]
            }
        ];
    },

    /**
     * Event listener method which will be triggered when one (or more)
     * article are added to the article list.
     *
     * Creates new models based on the selected articles and
     * assigns them to the article store.
     *
     * @public
     */
    onAddArticleToGrid: function (grid, editor) {
        var me = this, store = me.articleStore;

        editor.cancelEdit();

        var count = store.getCount();
        var model = store.create({
            position: count,
            isNew: true,
            type: 'fix',
            name: '',
            articleId: ''
        });

        store.add(model);
        editor.startEdit(model, 1);

        // We need a defer due to early firing of the event
        Ext.defer(function () {
            me.refreshHiddenValue();
        }, 10);
    },

    /**
     * Event listener method which will be triggered when the user
     * deletes a article from article administration grid panel.
     *
     * Removes the article from the article store.
     *
     * @event click#actioncolumn
     * @param { Ext.grid.Panel } grid
     * @param { integer } rowIndex - Index of the clicked row
     * @param { integer } colIndex - Index of the clicked column
     * @param { object } item - DOM node of the clicked row
     * @param { object } eOpts - additional event parameters
     * @param { object } record - Associated model of the clicked row
     */
    onDeleteArticle: function (grid, rowIndex, colIndex, item, eOpts, record) {
        var me = this;
        var store = grid.getStore();
        store.remove(record);
        me.refreshHiddenValue();
    },

    /**
     * Event listener method which will be fired when the user
     * repositions a article through drag and drop.
     *
     * Sets the new position of the article in the article store
     * and saves the data to an hidden field.
     *
     * @public
     * @event drop
     * @return void
     */
    onRepositionArticle: function () {
        var me = this;

        var i = 0;
        me.articleStore.each(function (item) {
            item.set('position', i);
            i++;
        });
        me.refreshHiddenValue();
    },

    /**
     * Refreshes the mapping field in the model
     * which contains all articles in the grid.
     *
     * @public
     * @return void
     */
    refreshHiddenValue: function () {
        var me = this,
            store = me.articleStore,
            cache = [];

        store.each(function (item) {
            cache.push(item.data);
        });
        var record = me.getSettings('record');
        record.set('mapping', cache);
        var count = store.getCount();
        var numberFields = me.query('numberfield');
        numberFields[0].setValue(count);
        if (count == 0) {
            if (record.data.data[1]) {
                var defField = record.data.data[1].value;
                numberFields[0].setValue(defField);
            } else {
                numberFields[0].setValue(2);
            }
        }
    },

    /**
     * Refactors the mapping field in the global record
     * which contains all article in the grid.
     *
     * Adds all articles to the article administration grid
     * when the user opens the component.
     *
     * @return void
     */
    getGridData: function () {
        var me = this,
            elementStore = me.getSettings('record').get('data'), articleList;

        Ext.each(elementStore, function (element) {
            if (element.key === 'article_data') {
                articleList = element;
                return false;
            }
        });

        if (articleList && articleList.value) {
            Ext.each(articleList.value, function (item) {
                me.articleStore.add(Ext.create('Shopware.apps.NewsletterManager.store.liveArticle', item));
            });
        }
    },

    /**
     * Helper function to setup the article popup search
     * @param returnValue
     * @param hiddenReturnValue
     * @param name
     * @return Shopware.form.field.ArticleSearch
     */
    createArticleSearch: function (returnValue, hiddenReturnValue, name) {
        var me = this;

        me.searchStore = Ext.create('Shopware.apps.NewsletterManager.store.liveArticle');

        me.articleSearch = Ext.create('Shopware.form.field.ArticleSearch', {
            name: name,
            returnValue: returnValue,
            hiddenReturnValue: hiddenReturnValue,
            articleStore: me.searchStore,
            allowBlank: false,
            getValue: function () {
                return this.getSearchField().getValue();
            },
            setValue: function (value) {
                this.getSearchField().setValue(value);
            }
        });

        //fix: to change the drop down store. Can't be defined in the component construct, because it will be overriden.
        me.articleSearch.dropDownStore = me.searchStore;

        //fix: we have to change the store of the drop down menu.
        me.articleSearch.getDropDownMenu().bindStore(me.searchStore);

        //fix: we have to redeclare the data change evnt on the search store.
        me.searchStore.on('datachanged', me.articleSearch.onSearchFinish, me.articleSearch);

        return me.articleSearch;
    },

    /**
     * Renderer for sorthandle-column
     *
     * @return { string }
     */
    renderSorthandleColumn: function () {
        return '<div style="cursor: move;">&#009868;</div>';
    }
});
//{/block}
