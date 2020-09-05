/**
 * Copyright notice
 *
 * (c) 2009-2016 Net Inventors - Agentur für digitale Medien GmbH
 * All rights reserved
 *
 * This script is part of the Spirit-Project.
 * The Spirit-Project is property of the Net Inventors GmbH and
 * may not be used in projects not related to the Net Inventors
 * without explicit permission by the authors.
 *
 * PHP version 5
 *
 * @package    NetiFoundation
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundationExtensions.AttributeData
 * @author     bmueller
 * @copyright  2016 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.AttributeData', {
    'extend': 'Ext.Base',
    'singleton': true,

    'loadData': function (foreignKey, table, callback) {
        var me = this;

        if(foreignKey && table) {
            Ext.Ajax.request({
                'url': '{url controller=AttributeData action=loadData}',
                'params': {
                    '_foreignKey': foreignKey,
                    '_table': table
                },
                'success': function (responseData, request) {
                    var response = Ext.JSON.decode(responseData.responseText);

                    if (Ext.isFunction(callback)) {
                        callback.call(me, response.data);
                    }
                }
            });
        }
    },

    'saveData': function (foreignKey, table, attributes, callback) {
        var me = this,
            params = {
                '_foreignKey': foreignKey,
                '_table': table
            };

        if(foreignKey && table) {
            Ext.Object.each(attributes, function (key, value) {
                if (0 > key.indexOf('__attribute_')) {
                    key = '__attribute_' + key;
                }
                params[key] = value;
            });

            Ext.Ajax.request({
                'method': 'POST',
                'url': '{url controller=AttributeData action=saveData}',
                'params': params,
                'success': function(response) {
                    var responseJson = Ext.JSON.decode(response.responseText);

                    if (Ext.isFunction(callback)) {
                        callback.call(me, responseJson);
                    }
                }
            });
        }
    }
});
//{/block}
