//{namespace name=backend/stutt_seo_redirects/view/list}

Ext.define('Shopware.apps.StuttSeoRedirects.view.list.Redirect', {
    extend: 'Shopware.grid.Panel',
    alias:  'widget.stutt-seo-redirects-listing-grid',
    cls: 'stutt-seo-redirects-listing-grid',
    region: 'center',

    configure: function() {
        return {
            detailWindow: 'Shopware.apps.StuttSeoRedirects.view.detail.Window',
            columns: {
                active: { width: 60, flex: 0, header: '{s name=active}Aktiviert{/s}' },
                oldUrl: { flex: 1, header: '{s name=old_url}alte URL{/s}' },
                newUrl: { flex: 1, header: '{s name=redirect_target}Weiterleitungs-Ziel{/s}' },
                overrideShopUrl: { width: 140, flex: 0, header: '{s name=override_shop_url_short}Shopware-URL ersetzen{/s}' },
                temporaryRedirect: { width: 120, flex: 0, header: '{s name=only_temporary_short}temporär (302){/s}' },
                externalRedirect: { width: 120, flex: 0, header: '{s name=external_redirect_short}externes Ziel{/s}' },
                shopName: '{s name=subshop}Subshop{/s}',
                gone: { width: 120, flex: 0, header: '{s name=gone}Inhalt entfernt{/s}' }
            }
        };
    },

    createToolbarItems: function() {
        var me = this,
            items = me.callParent(arguments);

        /*{if {acl_is_allowed privilege=import}}*/
        Ext.Array.insert(items, 2,
            me.createToolbarButtons()
        );
        /*{/if}*/

        return items;
    },

    createAddButton: function() {
        /*{if {acl_is_allowed privilege=create}}*/
        return this.callParent(arguments);
        /*{/if}*/
    },

    createDeleteButton: function() {
        /*{if {acl_is_allowed privilege=delete}}*/
        return this.callParent(arguments);
        /*{/if}*/
    },

    createActionColumnItems: function () {
        var me = this, items = [];

        me.fireEvent(me.eventAlias + '-before-create-action-column-items', me, items);

        /*{if {acl_is_allowed privilege=delete}}*/
        if (me.getConfig('deleteColumn')) {
            items.push(me.createDeleteColumn());
        }
        /*{/if}*/

        /*{if {acl_is_allowed privilege=update}}*/
        if (me.getConfig('editColumn')) {
            items.push(me.createEditColumn());
        }
        /*{/if}*/

        me.fireEvent(me.eventAlias + '-after-create-action-column-items', me, items);

        return items;
    },

    createToolbarButtons: function() {
        var me = this;

        me.uploadButton = Ext.create('Ext.button.Button', {
            text: '{s name="csv_import"}CSV-Import{/s}',
            iconCls: 'sprite-arrow-circle-double-135',
            handler: function() {
                me.fireEvent('open-csv-import');
            }
        });

        me.exportButton = Ext.create('Ext.button.Button', {
            text: '{s name="csv_export"}Export{/s}',
            iconCls: 'sprite-drive-download',
            handler: function() {
                me.fireEvent('open-csv-export');
            }
        });

        return [me.uploadButton, me.exportButton];
    }
});
