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

namespace SwagBusinessEssentials\Components;

use Doctrine\DBAL\Connection as DbalConnection;

class ConfigHelper implements ConfigHelperInterface
{
    /**
     * @var DbalConnection
     */
    private $connection;

    /**
     * @var array
     */
    private $registerConfig = [];

    /**
     * @var array
     */
    private $shoppingConfig = [];

    public function __construct(DbalConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($table, $configName, $customerGroup)
    {
        if ($table === ConfigHelperInterface::PRIVATE_SHOPPING_TABLE) {
            return $this->getShoppingConfig($configName, $customerGroup);
        }

        return $this->getRegisterConfig($configName, $customerGroup);
    }

    /**
     * Reads a config from the private shopping config.
     * If the config for the given customer group was not yet cached, it will be cached in this process.
     *
     * @param string $configName
     * @param string $customerGroup
     *
     * @return string|bool
     */
    private function getShoppingConfig($configName, $customerGroup)
    {
        if (!isset($this->shoppingConfig[$customerGroup])) {
            $success = $this->readShoppingConfig($customerGroup);

            if (!$success) {
                return false;
            }
        }

        return $this->shoppingConfig[$customerGroup][$configName];
    }

    /**
     * Reads a config from the private register config.
     * If the config for the given customer group was not yet cached, it will be cached in this process.
     *
     * @param string $configName
     * @param string $customerGroup
     *
     * @return string|bool
     */
    private function getRegisterConfig($configName, $customerGroup)
    {
        if (!isset($this->registerConfig[$customerGroup])) {
            $success = $this->readRegisterConfig($customerGroup);

            if (!$success) {
                return false;
            }
        }

        return $this->registerConfig[$customerGroup][$configName];
    }

    /**
     * Reads the config for private shopping from the database and caches it into 'shoppingConfig'.
     * Returns true if there was at least a single entry to be loaded, false otherwise.
     *
     * @param string $customerGroup
     *
     * @return bool
     */
    private function readShoppingConfig($customerGroup)
    {
        $data = $this->getData(ConfigHelperInterface::PRIVATE_SHOPPING_TABLE, $customerGroup);

        if (empty($data)) {
            return false;
        }

        $this->shoppingConfig[$customerGroup] = $data;

        return true;
    }

    /**
     * Reads the config for private register from the database and caches it into 'shoppingConfig'.
     * Returns true if there was at least a single entry to be loaded, false otherwise.
     *
     * @param string $customerGroup
     *
     * @return bool
     */
    private function readRegisterConfig($customerGroup)
    {
        $data = $this->getData(ConfigHelperInterface::PRIVATE_REGISTER_TABLE, $customerGroup);

        if (!$data) {
            return false;
        }

        $this->registerConfig[$customerGroup] = $data;

        return true;
    }

    /**
     * Reads the config from $table for the given customer group from the database.
     *
     * @param string $table
     * @param string $customerGroup
     *
     * @return array|bool
     */
    private function getData($table, $customerGroup)
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select('configs.*')
            ->from($table, 'configs')
            ->where('configs.customergroup = :customerGroup')
            ->setParameter(':customerGroup', $customerGroup)
            ->execute()
            ->fetch();
    }
}
