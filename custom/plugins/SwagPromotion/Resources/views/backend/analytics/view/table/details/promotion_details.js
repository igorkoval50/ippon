/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

/**
 * Analytics Promotion Table
 *
 * @category   Shopware
 * @package    Analytics
 * @copyright  Copyright (c) shopware AG (http://www.shopware.com)
 *
 */
//{namespace name=backend/analytics/view/main}
//{block name="backend/analytics/view/table/promotion/details"}
Ext.define('Shopware.apps.Analytics.view.table.PromotionDetails', {
    extend: 'Shopware.apps.Analytics.view.main.Table',
    alias: 'widget.analytics-table-promotion_details',

    initComponent: function () {
        var me = this;

        me.columns = {
            items: me.getColumns(),
            defaults: {
                flex: 1,
                sortable: false
            }
        };

        me.initStoreIndices('turnover', '{s name=general/turnover}Turnover{/s}: [0]', {
            xtype: 'numbercolumn',
            renderer: me.currencyRenderer
        });

        me.callParent(arguments);
    },

    /**
     * Creates the grid columns
     *
     * @return [array] grid columns
     */
    getColumns: function () {
        var me = this;

        return [
            {
                dataIndex: 'promotion_name',
                text: '{s namespace=backend/swag_promotion/main name=promotionNameTitle}Promotion name{/s}'
            }, {
                dataIndex: 'user_name',
                text: '{s namespace=backend/swag_promotion/main name=promotionUserNameTitle}Username{/s}'
            }, {
                xtype: 'actioncolumn',
                dataIndex: 'order_number',
                text: '{s namespace=backend/swag_promotion/main name=promotionOrderNumberTitle}Ordernumber{/s}',
                renderer: function (val) {
                    return val + ' ';
                },
                items: [
                    {
                        iconCls: 'sprite-pencil',
                        cls: 'editBtn',
                        tooltip: '{s namespace=backend/swag_promotion/main name=promotionShowDetailsToolTip}Show details{/s}',
                        handler: function (view, rowIndex, colIndex, item, event, record) {
                            Shopware.app.Application.addSubApplication({
                                name: 'Shopware.apps.Order',
                                action: 'detail',
                                params: {
                                    orderId: record.get('order_id')
                                }
                            });
                        }
                    }
                ]
            }, {
                xtype: 'numbercolumn',
                dataIndex: 'order_turnover',
                text: '{s name=general/turnover}Turnover{/s}',
                renderer: me.currencyRenderer
            }
        ];
    },

    currencyRenderer: function(value) {
        var me = this;

        return Ext.util.Format.currency(
            value,
            me.store.proxy.reader.jsonData.analyticsCurrency.sign,
            2,
            (me.store.proxy.reader.jsonData.analyticsCurrency.currencyAtEnd == 0)
        );
    },

    orderNumberRenderer: function (value) {
        var me = this;
        return value + ' add';
    }
});
//{/block}