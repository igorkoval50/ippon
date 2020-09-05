/**
 * global: Ext, Shopware
 */
//{namespace name="plugins/neti_foundation/backend/send_mail"}
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.sendMail.Attachments', {
    'extend': 'Ext.tree.Panel',
    'alias': 'widget.neti_foundation_extensions_send_mail_attachments',
    'rootVisible': false,
    'sortableColumns': false,
    'useArrows': true,

    /**
     * Configure the root node of the tree panel. This is necessary
     * due to the fact that the ExtJS 4.0.7 build fires the load
     * event to often if no root node is configured.
     *
     * @object
     */
    'root': {
        'text': 'Mail',
        'expanded': true
    },

    'displayField': 'filename',

    /**
     * Initializes the component and builds up the main interface
     *
     * @public
     * @return void
     */
    'initComponent': function () {
        var me = this;

        me.columns = me.getColumns();
        me.dockedItems = [me.getToolbar()];

        me.callParent(arguments);
    },

    /**
     * Defines additional events which will be
     * fired from the component
     *
     * @return void
     */
    'registerEvents': function () {
        this.addEvents(
            'onDeleteSingle'
        );
    },

    /**
     * Creates the grid columns
     *
     * @return [array] grid columns
     */
    'getColumns': function () {
        var columns = [
            {
                'xtype': 'treecolumn',
                'dataIndex': 'filename',
                'flex': 1,
                'hideable': false,
                'text': '{s name=label_filename}Dateiname{/s}'
            },
            {
                'dataIndex': 'size',
                'hideable': false,
                'text': '{s name=label_filesize}Dateigröße{/s}'
            }
        ];

        return columns;
    },

    /**
     * Creates the toolbar.
     *
     * @return [object] generated Ext.toolbar.Toolbar
     */
    'getToolbar': function () {
        var buttons = [];

        return {
            'xtype': 'toolbar',
            'dock': 'top',
            'items': buttons
        };
    }
});
//{/block}
