<?php
/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   Shopware
 * @author     hrombach
 */

namespace NetiFoundation\Service;

use NetiFoundation\Struct\NewArticleData;

class Article implements ArticleInterface
{
    /**
     * @var \Shopware_Components_Config
     */
    private $swConfig;

    /**
     * @var \Enlight_Components_Db_Adapter_Pdo_Mysql
     */
    private $db;

    /**
     * Article constructor.
     *
     * @param \Shopware_Components_Config              $swConfig
     * @param \Enlight_Components_Db_Adapter_Pdo_Mysql $db
     */
    public function __construct(\Shopware_Components_Config $swConfig, \Enlight_Components_Db_Adapter_Pdo_Mysql $db)
    {
        $this->swConfig = $swConfig;
        $this->db       = $db;
    }

    /**
     * @param string|null $prefix
     *
     * @return NewArticleData
     */
    public function getNewArticleData($prefix = null)
    {
        if (!$prefix) {
            $prefix = $this->swConfig->get('backendAutoOrderNumberPrefix');
        }
        $sql    = <<<'SQL'
SELECT number FROM s_order_number WHERE name = 'articleordernumber'
SQL;
        $number = $this->db->fetchOne($sql);

        if (!empty($number)) {
            do {
                ++$number;

                $sql = <<<'SQL'
SELECT id FROM s_articles_details WHERE ordernumber LIKE ?
SQL;
                $hit = $this->db->fetchOne($sql, $prefix . $number);
            } while ($hit);
        }

        return new NewArticleData([
            'number'     => $prefix . $number,
            'autoNumber' => $number,
        ]);
    }
}