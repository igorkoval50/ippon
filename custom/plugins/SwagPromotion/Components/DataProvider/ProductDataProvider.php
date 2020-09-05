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

namespace SwagPromotion\Components\DataProvider;

use Enlight_Components_Db_Adapter_Pdo_Mysql as PdoConnection;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use SwagPromotion\Components\MetaData\FieldInfo;

/**
 * Holds context data for all products in basket.
 */
class ProductDataProvider implements DataProvider
{
    /**
     * @var PdoConnection
     */
    private $db;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    public function __construct(PdoConnection $db, ContextServiceInterface $contextService)
    {
        $this->db = $db;
        $this->contextService = $contextService;
    }

    /**
     * Get product information
     *
     * {@inheritdoc}
     */
    public function get($context = null)
    {
        if (empty($context)) {
            return [];
        }

        return $this->getProductContext($context);
    }

    /**
     * Read out product data
     *
     * @return array
     */
    private function getProductContext(array $orderNumbers)
    {
        $products = $this->getBaseProducts($orderNumbers);
        $categories = $this->getProductCategories(array_column($products, 'articleID'));
        $basketAttributes = $this->getBasketAttributes();

        foreach ($products as $idx => $product) {
            if (isset($basketAttributes[$product['basketItemId']])) {
                foreach ($basketAttributes[$product['basketItemId']] as $key => $value) {
                    $products[$idx]['basketAttribute::' . $key] = $value;
                }
            }

            if (isset($categories[$product['articleID']])) {
                $products[$idx]['categories'] = $categories[$product['articleID']];
            }
        }

        return $products;
    }

    /**
     * Enrich the product data with basket attributes, if possible
     *
     * @return array
     */
    private function getBasketAttributes()
    {
        $sql = <<<SQL
SELECT basket.id, basket.articleID, attributes.*
FROM s_order_basket basket
LEFT JOIN s_order_basket_attributes attributes ON attributes.basketID = basket.id
WHERE basket.sessionID = :session
AND basket.modus = 0;
SQL;

        return $this->db->fetchAll($sql, ['session' => Shopware()->Session()->get('sessionId')], \PDO::FETCH_GROUP | \PDO::FETCH_UNIQUE);
    }

    /**
     * Return the base product information
     *
     * @return array
     */
    private function getBaseProducts(array $orderNumbers)
    {
        $questionMarks = implode(', ', array_fill(0, count($orderNumbers), '?'));

        $info = new FieldInfo();
        $info = array_keys($info->get()['product']);

        $mapping = [
            'product' => 'articles',
            'detail' => 'details',
            'productAttribute' => 'attributes',
            'price' => 'prices',
            'supplier' => 'supplier',
        ];

        $fields = implode(
            ', ',
            array_map(
                function ($field) use ($mapping) {
                    list($type, $rest) = explode('::', $field, 2);

                    $table = $mapping[$type];

                    return "{$table}.{$rest} as \"{$field}\"";
                },
                // sort out categories
                array_filter(
                    $info,
                    function ($field) {
                        return strpos($field, 'categories') !== 0;
                    }
                )
            )
        );

        $sql = "SELECT details.ordernumber, {$fields}, basket.id as basketItemId, basket.quantity, basket.price, basket.netprice, basket.tax_rate, basket.articlename as basketItemName, details.articleID

        FROM s_articles_details details

        LEFT JOIN s_order_basket basket
        ON basket.ordernumber = details.ordernumber
        AND basket.sessionID = ?
        AND modus = 0

        LEFT JOIN s_articles_attributes attributes
        ON attributes.articledetailsID = details.id

        LEFT JOIN s_articles articles
        ON articles.id = details.articleID

        LEFT JOIN s_articles_prices prices
        ON prices.articledetailsID = details.id

        LEFT JOIN s_articles_supplier supplier
        ON supplier.id = articles.supplierID

        WHERE details.ordernumber IN ({$questionMarks})";

        $data = $this->db->fetchAssoc(
            $sql,
            array_merge([Shopware()->Session()->get('sessionId')], array_keys($orderNumbers))
        );

        $data = $this->applyPseudoPriceForCustomerGroup($data);

        foreach ($data as $orderNumber => $product) {
            $data[$orderNumber]['quantity'] = $orderNumbers[$orderNumber];
            $data[$orderNumber]['price::price'] = (float) $data[$orderNumber]['price::price'];
            $data[$orderNumber]['price'] = (float) $data[$orderNumber]['price'];
            $data[$orderNumber]['netprice'] = (float) $data[$orderNumber]['netprice'];
        }

        return $data;
    }

    /**
     * Return categories for the given articleIds
     *
     * @return array
     */
    private function getProductCategories(array $articleIds)
    {
        if (empty($articleIds)) {
            return [];
        }

        $articleIds = implode(', ', $articleIds);
        $sql = <<<SQL
SELECT ro.articleID, attributes.*, categories.*
FROM s_articles_categories_ro ro

INNER JOIN s_categories categories
ON categories.id = ro.categoryID

LEFT JOIN s_categories_attributes attributes
ON attributes.categoryID = ro.categoryID

WHERE articleID IN ({$articleIds})
SQL;

        return $this->db->fetchAll($sql, [], \PDO::FETCH_GROUP);
    }

    private function applyPseudoPriceForCustomerGroup(array $products): array
    {
        $customerGroupKey = $this->contextService->getShopContext()->getCurrentCustomerGroup()->getKey();
        $productIds = $this->getProductIds($products);

        if (empty($productIds)) {
            return $products;
        }

        foreach ($this->getPseudoPrices($productIds, $customerGroupKey) as $productId => $pseudoPrice) {
            $products[$productIds[$productId]]['price::pseudoprice'] = $pseudoPrice;
        }

        return $products;
    }

    private function getProductIds(array $products): array
    {
        $productIds = [];
        foreach ($products as $product) {
            $productIds[$product['product::id']] = $product['ordernumber'];
        }

        return $productIds;
    }

    private function getPseudoPrices(array $productIds, string $customerGroupKey): array
    {
        $questionMarks = implode(', ', array_fill(0, count($productIds), '?'));

        $sql = "SELECT articleID, `pseudoprice`
                FROM `s_articles_prices`
                WHERE articleID IN ({$questionMarks})
                  AND pricegroup = ?";

        $pseudoPrices = $this->db->fetchPairs(
            $sql,
            array_merge(array_keys($productIds), [$customerGroupKey])
        );

        return $pseudoPrices;
    }
}
