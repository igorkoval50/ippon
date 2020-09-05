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

/**
 * Shopware SwagFuzzy Plugin - SwagFuzzySearchTables Backend Controller
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Shopware_Controllers_Backend_SwagFuzzySearchTables extends Shopware_Controllers_Backend_Application
{
    /**
     * @var string
     */
    protected $model = SwagFuzzy\Models\SearchTables::class;

    /**
     * @var string
     */
    protected $alias = 'searchTables';

    /**
     * Saves the data to the search tables
     *
     * @param $data
     *
     * @return array
     */
    public function save($data)
    {
        /** @var \Doctrine\Dbal\Connection $connection */
        $connection = $this->get('dbal_connection');

        $sql = 'SHOW TABLES LIKE :tableName';

        try {
            $test = $connection->fetchAll($sql, ['tableName' => $data['table']]);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }

        if (empty($test)) {
            return ['success' => false, 'error' => 'Table "' . $data['table'] . '" does not exist!'];
        }

        $data = $this->convertData($data);

        return parent::save($data);
    }

    /**
     * Converts several rows to null if the value is an empty string
     *
     * @param $data
     *
     * @return mixed
     */
    private function convertData($data)
    {
        $blackList = [
            'referenceTable',
            'foreignKey',
            'additionalCondition',
        ];

        foreach ($data as $key => &$value) {
            if (in_array($key, $blackList)) {
                $value = trim($value);
                if ($value === '') {
                    $value = null;
                }
            }
        }

        return $data;
    }
}
