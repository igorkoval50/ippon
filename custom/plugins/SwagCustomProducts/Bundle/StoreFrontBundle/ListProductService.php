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

namespace SwagCustomProducts\Bundle\StoreFrontBundle;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Service\ListProductServiceInterface;
use Shopware\Bundle\StoreFrontBundle\Struct;

class ListProductService implements ListProductServiceInterface
{
    /**
     * @var ListProductServiceInterface
     */
    private $decorated;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(ListProductServiceInterface $decorated, Connection $connection)
    {
        $this->decorated = $decorated;
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(array $numbers, Struct\ProductContextInterface $context)
    {
        $products = $this->decorated->getList($numbers, $context);

        $ids = array_map(function (Struct\ListProduct $product) {
            return $product->getId();
        }, $products);

        $hasCustomProducts = $this->hasCustomProducts($ids);

        foreach ($products as $product) {
            if (!in_array($product->getId(), $hasCustomProducts)) {
                continue;
            }
            $product->setAllowBuyInListing(false);
        }

        return $products;
    }

    /**
     * {@inheritdoc}
     */
    public function get($number, Struct\ProductContextInterface $context)
    {
        $list = $this->getList([$number], $context);

        return array_shift($list);
    }

    /**
     * returns ids of products with CustomProducts options as subset of the given product ids
     *
     * @param int[] $ids
     *
     * @return int[]
     */
    private function hasCustomProducts($ids)
    {
        $query = $this->connection->createQueryBuilder();
        $query->select(['mapping.article_id']);
        $query->from('s_plugin_custom_products_template_product_relation', 'mapping');
        $query->innerJoin('mapping', 's_plugin_custom_products_template', 'template', 'template.id = mapping.template_id');
        $query->where('template.active = 1');
        $query->andWhere('mapping.article_id IN (:ids)');
        $query->setParameter(':ids', array_values($ids), Connection::PARAM_INT_ARRAY);

        return $query->execute()->fetchAll(\PDO::FETCH_COLUMN);
    }
}
