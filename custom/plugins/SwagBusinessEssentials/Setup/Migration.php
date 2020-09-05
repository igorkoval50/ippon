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

namespace SwagBusinessEssentials\Setup;

use Doctrine\DBAL\Connection;
use Enlight_Event_EventManager as EventManager;
use Exception;
use SwagBusinessEssentials\Setup\Migration\TableFactory;
use SwagBusinessEssentials\Setup\Migration\TableInterface;

class Migration
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * Migration constructor.
     */
    public function __construct(Connection $connection, EventManager $eventManager)
    {
        $this->connection = $connection;
        $this->eventManager = $eventManager;
    }

    /**
     * Start the migration process
     *
     * @throws Exception
     */
    public function migrate()
    {
        /** @var TableInterface[] $tables */
        $tables = (new TableFactory($this->connection, $this->eventManager))->factory();

        $migratedTables = [];

        try {
            foreach ($tables as $table) {
                $migratedTables[] = $table;
                $table->rename();
                $table->create();
                $table->migrate();
            }
        } catch (Exception $ex) {
            foreach ($migratedTables as $table) {
                $table->revert();
            }

            throw $ex;
        }
    }
}
