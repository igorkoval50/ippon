/**
 * Copyright notice
 *
 * (c) 2009-2017 Net Inventors - Agentur für digitale Medien GmbH
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
 * @subpackage NetiFoundation/Shopware.apps.NetiFoundationExtensions.Helper
 * @author     bmueller
 * @copyright  2017 Net Inventors GmbH
 * @license    proprietary http://www.netinventors.de
 * @version    GIT: $revision
 * @link       http://www.netinventors.de
 */
//{block name="backend/index/view/menu"}
//{$smarty.block.parent}
Ext.define('Shopware.apps.NetiFoundationExtensions.Helper', {
    'singleton': true,
    'getPluginConfig': function (applicationName, key) {
        var app,
            pluginConfig;

        if (applicationName) {
            app = Shopware.app.Application.subApplications.findBy(function (item) {
                return item.id === applicationName
            });
            if (app) {
                if (Ext.isObject(app.pluginConfig)) {
                    pluginConfig = app.pluginConfig;
                    if (!key) {
                        return pluginConfig;
                    } else if (pluginConfig.hasOwnProperty(key)) {
                        return pluginConfig[key];
                    }
                }
            }
        }
    }
});
//{/block}
