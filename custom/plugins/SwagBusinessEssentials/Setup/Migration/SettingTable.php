<?php
/**
 * Shopware Premium Plugins
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this plugin can be used under
 * a proprietary license as set forth in our Terms and Conditions,
 * section 2.1.2.2 (Conditions of Usage).
 *
 * The text of our proprietary license additionally can be found at and
 * in the LICENSE file you have received along with this plugin.
 *
 * This plugin is distributed in the hope that it will be useful,
 * with LIMITED WARRANTY AND LIABILITY as set forth in our
 * Terms and Conditions, sections 9 (Warranty) and 10 (Liability).
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the plugin does not imply a trademark license.
 * Therefore any rights, title and interest in our trademarks
 * remain entirely with us.
 */

namespace SwagBusinessEssentials\Setup\Migration;

class SettingTable extends Table
{
    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        return 's_core_plugins_b2b_cgsettings';
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `s_core_plugins_b2b_cgsettings` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `customergroup` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
              `allowregister` tinyint(1) NOT NULL,
              `requireunlock` tinyint(1) NOT NULL,
              `assigngroupbeforeunlock` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
              `registertemplate` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `emailtemplatedeny` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `emailtemplateallow` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';

        $this->connection->exec($sql);

        return true;
    }
}
