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

namespace SwagProductAdvisor\Bootstrap;

use Doctrine\DBAL\Connection;

class Updater
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function update(string $currentVersion): void
    {
        if (version_compare($currentVersion, '3.4.0', '<')) {
            $this->updateToVersion340();
        }
    }

    private function updateToVersion340()
    {
        $sql = "ALTER TABLE `s_plugin_product_advisor_question`
                ADD `show_all_properties` TINYINT NOT NULL DEFAULT '0';";

        $this->connection->exec($sql);
    }
}
