//{namespace name="backend/swag_newsletter/main"}
//{block name="backend/newsletter_manager/view/tabs/overview"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NewsletterManager.view.tabs.Overview-SwagNewsletter', {

    /**
     * Defines an override applied to a class.
     * @string
     */
    override: 'Shopware.apps.NewsletterManager.view.tabs.Overview',

    /**
     * List of classes that have to be loaded before instantiating this class.
     * @array
     */
    requires: ['Shopware.apps.NewsletterManager.view.tabs.Overview'],

    /**
     * Initializes the class override to provide additional functionality
     * like a new full page preview.
     *
     * @public
     * @return void
     */
    initComponent: function () {
        var me = this;
        me.callOverridden(arguments);
    },

    getToolbar: function () {
        var me = this,
            container = me.callOverridden(arguments);

        return container;
    },

    /**
     * Extend action columns, add duplicate field
     *
     * @returns
     */
    getActionColumn: function () {

        var me = this,
        items = me.callParent(arguments);

        /*{if {acl_is_allowed privilege=write}}*/
        items.push({
            iconCls: 'sprite-blue-document-copy',
            action: 'view',
            tooltip: '{s name=action/duplicateNewsletter}Duplicate newsletter{/s}',
            handler: function (view, rowIndex, colIndex, item, opts, record) {
                me.fireEvent('duplicateNewsletter', record);
            }
        });
        /*{/if}*/

        return items;
    },

    /**
     * Creates the grid columns
     * Data indices where chosen in order to match the database scheme for sorting in the PHP backend.
     * Therefore each Column requieres its own renderer in order to display the correct value.
     *
     * @return Array grid columns
     */
    getColumns: function () {
        var me = this,
            defaultColumns = me.callParent(arguments);

        //Represents the new column (PT-1808)
        var finishedDateColumn = {
            header: '{s name=columns/finishedDate}Shipping date{/s}',
            dataIndex: 'mailing.locked',

            renderer: function (value, metaData, record) {
                var finishedDate = record.get('finishedDate');
                if (finishedDate)
                    return Ext.util.Format.date(finishedDate);
                else
                    return '{s name=columns/finishedDate_default}Not finished yet{/s}';
            },
            flex: 1
        };

        //Insert the new column into the default columns
        defaultColumns.splice(1, 0, finishedDateColumn);
        return defaultColumns;
    }
});
//{/block}
