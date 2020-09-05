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

namespace SwagCustomProducts\Bundle\ESIndexingBundle;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\ESIndexingBundle\Product\ProductProviderInterface;
use Shopware\Bundle\ESIndexingBundle\Struct\Product;
use Shopware\Bundle\StoreFrontBundle\Struct\Attribute;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

/**
 * This class decorates the ProviderInterface to enrich the loaded Product objects with the attributes
 * necessary for this plugin to work.
 */
class ProductProvider implements ProductProviderInterface
{
    /**
     * @var ProductProviderInterface
     */
    private $coreService;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(ProductProviderInterface $coreService, Connection $connection)
    {
        $this->coreService = $coreService;
        $this->connection = $connection;
    }

    /**
     * @param string[] $numbers
     *
     * @return Product[]
     */
    public function get(Shop $shop, $numbers)
    {
        $products = $this->coreService->get($shop, $numbers);

        $productIds = array_values(array_map(function (Product $product) {
            return $product->getId();
        }, $products));

        $customProductIds = $this->getCustomProductIds($productIds);

        foreach ($products as $product) {
            $product->addAttribute(
                'swag_custom_product',
                new Attribute(['is_custom_product' => in_array((string) $product->getId(), $customProductIds)])
            );
        }

        return $products;
    }

    /**
     * Checks which of the given $productIds are actual custom products and returns only the ids of those which are.
     *
     * @param int[] $productIds
     *
     * @return string[]
     */
    private function getCustomProductIds(array $productIds)
    {
        if (empty($productIds)) {
            return [];
        }

        $statement = $this->connection->executeQuery(
            'SELECT article_id FROM s_plugin_custom_products_template_product_relation WHERE article_id IN (:productIds)',
            [':productIds' => $productIds],
            [':productIds' => Connection::PARAM_INT_ARRAY]
        );

        $result = $statement->fetchAll(\PDO::FETCH_COLUMN);
        if (!$result) {
            return [];
        }

        return $result;
    }
}
