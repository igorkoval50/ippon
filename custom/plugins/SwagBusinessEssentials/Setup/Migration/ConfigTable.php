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

use PDO;

class ConfigTable extends Table
{
    /** @var array */
    private $variables;

    /** @var array */
    private $customerGroups;

    /**
     * {@inheritdoc}
     */
    public function getTableName()
    {
        return 's_core_plugins_b2b_tpl_config';
    }

    /**
     * @overwrite
     */
    public function getCreateQuery()
    {
        return 'CREATE TABLE IF NOT EXISTS `s_core_plugins_b2b_tpl_config` (
            `variable_id` int(11) NOT NULL,
            `customergroup_id` int(11) NOT NULL,
            PRIMARY KEY (`variable_id`, `customergroup_id`),
            KEY `IDX_BE_v_id` (`variable_id`),
            KEY `IDX_BE_c_id` (`customergroup_id`),
            CONSTRAINT `FK_BE_b2b_tpl_cfg_TO_b2b_tpl_vars` FOREIGN KEY (`variable_id`) REFERENCES `s_core_plugins_b2b_tpl_variables` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;';
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $sql = $this->getCreateQuery();
        $this->connection->exec($sql);

        return true;
    }

    /**
     * @overwrite
     * {@inheritdoc}
     */
    protected function getData()
    {
        $data = parent::getData();

        return $this->prepareData($data);
    }

    /**
     * @return array
     */
    private function prepareData(array $data)
    {
        $this->initializeVariables();
        $this->initializeCustomerGroups();
        $dataToReturn = [];

        foreach ($data as $row) {
            if (!$row['fieldvalue']) {
                continue;
            }

            $newRow = [
                'variable_id' => $this->variables[$row['fieldkey']],
                'customergroup_id' => $this->customerGroups[$row['customergroup']],
            ];

            $dataToReturn[] = $newRow;
        }

        return $dataToReturn;
    }

    /**
     * Creates a key value pair array of variables for fast access the values.
     */
    private function initializeVariables()
    {
        $result = $this->connection->executeQuery(
            'SELECT variable, id FROM `s_core_plugins_b2b_tpl_variables`'
        )->fetchAll(PDO::FETCH_KEY_PAIR);

        $this->variables = $result;
    }

    /**
     * Creates a key value pair array of customerGroups for fast access the values.
     */
    private function initializeCustomerGroups()
    {
        $result = $this->connection->executeQuery(
            'SELECT groupkey, id FROM `s_core_customergroups`'
        )->fetchAll(PDO::FETCH_KEY_PAIR);

        $this->customerGroups = $result;
    }
}
