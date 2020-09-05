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

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Shopware SwagFuzzy Plugin - SwagFuzzy Backend Controller
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Shopware_Controllers_Backend_SwagFuzzy extends Shopware_Controllers_Backend_ExtJs
{
    /**
     * returns the names of all columns for the given table
     */
    public function getTableColumnsAction()
    {
        $tableName = $this->Request()->getParam('tableName');
        $tables = $this->getTables();
        $tableExists = null;
        $data = [];

        foreach ($tables as $table) {
            $tableExists = array_search($tableName, $table);
            if ($tableExists) {
                break;
            }
        }

        if ($tableExists) {
            //get columns of table
            $sql = 'SHOW COLUMNS FROM ' . $tableName;
            /** @var \Doctrine\DBAL\Connection $connection */
            $connection = $this->get('dbal_connection');
            $columns = $connection->fetchAll($sql);

            foreach ($columns as $column) {
                $data[] = [
                    'columnName' => $column['Field'],
                ];
            }

            $this->View()->assign(
                [
                    'success' => true,
                    'data' => $data,
                ]
            );
        } else {
            $this->View()->assign(
                [
                    'success' => false,
                    'message' => 'Passed table does not exist in s_search_tables',
                ]
            );
        }
    }

    /**
     * gets all tables saved in `s_search_tables`
     *
     * @return mixed
     */
    private function getTables()
    {
        /** @var QueryBuilder $builder */
        $builder = $this->get('dbal_connection')->createQueryBuilder();
        $builder->select('sst.id AS tableId', 'sst.table AS tableName')
            ->from('s_search_tables', 'sst');

        return $builder->execute()->fetchAll();
    }
}
