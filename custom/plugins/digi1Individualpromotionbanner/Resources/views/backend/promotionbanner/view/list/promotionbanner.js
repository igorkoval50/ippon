Ext.define('Shopware.apps.Promotionbanner.view.list.Promotionbanner', {
    extend: 'Shopware.grid.Panel',
    alias:  'widget.promotionbanner-listing-grid',
    region: 'center',

    configure: function() {
        return {
            detailWindow: 'Shopware.apps.Promotionbanner.view.detail.Window',
            addButton: true,	
            editColumn: true,
            columns: {
		        active: { header: '{s name=PromotionbannerListActive}Aktiv{/s}', width: 40, flex: 0},
                label: { header: '{s name=PromotionbannerListLabel}Interne Bezeichnung{/s}'},
                position: { header: '{s name=PromotionbannerListPosition}Position{/s}', width: 95, flex: 0},
                showinallshops: { header: '{s name=PromotionbannerListShowinallshops}In allen Shops anzeigen{/s}', width: 130, flex: 0},
		        shop_id: { header: '{s name=PromotionbannerListShopId}Shop{/s}' },
                displaydatefrom: { header: '{s name=PromotionbannerListDisplaydatefrom}Anzeigen von{/s}', type: 'date', format: 'Y-m-d'},
                displaydateto: { header: '{s name=PromotionbannerListDisplaydateto}Anzeigen bis{/s}', type: 'date', format: 'Y-m-d'}
            }
        };
    },
    
    createToolbarItems: function() {
        var me = this, items = me.callParent(arguments);

        items = Ext.Array.insert(items, 2,
            [ me.createToolbarButton() ]
        );

        return items;
    },

    createToolbarButton: function() {
        var me = this;
        return Ext.create('Ext.button.Button', {
            iconCls: 'sprite-duplicate-grid',
            text: '{s name=PromotionbannerListToolbarButtonCopy}Markierte Einträge kopieren{/s}',
            handler: function() {
                me.fireEvent('copy-promotionbanner', me);
            }
        });
    }
});