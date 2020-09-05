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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Enlight_Event_EventManager as EventManager;

class TableFactory
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EventManager
     */
    private $eventManager;

    public function __construct(Connection $connection, EventManager $eventManager)
    {
        $this->connection = $connection;
        $this->eventManager = $eventManager;
    }

    /**
     * Creates, collects and returns all registered tables as a TableInterface array.
     *
     * @return TableInterface[]
     */
    public function factory()
    {
        $tales = [
            new SettingTable($this->connection),
            new PrivateTable($this->connection),
            new VariablesTable($this->connection),
            new ConfigTable($this->connection),
        ];

        $collection = new ArrayCollection([]);

        $this->eventManager->collect('SwagBusinessEssentials_Collect_Tables', $collection);

        return array_merge($collection->toArray(), $tales);
    }
}
