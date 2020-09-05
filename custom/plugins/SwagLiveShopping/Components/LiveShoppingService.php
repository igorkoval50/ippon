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

namespace SwagLiveShopping\Components;

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Struct\Customer\Group;

class LiveShoppingService implements LiveShoppingServiceInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var PriceServiceInterface
     */
    private $priceService;

    public function __construct(Connection $connection, PriceServiceInterface $priceService)
    {
        $this->connection = $connection;
        $this->priceService = $priceService;
    }

    public function getLiveShoppingList(array $liveShoppingIds, Group $customerGroup): array
    {
        $liveShoppingList = $this->getLiveShoppingListData($liveShoppingIds);
        $liveShoppingPrices = $this->getLiveShoppingPrices($liveShoppingIds, $customerGroup->getId());
        $liveShoppingTaxes = $this->getLiveShoppingTax(array_column($liveShoppingList, 'article_id'));

        $isTaxInput = $this->priceService->getIsTaxInput($customerGroup->getId());

        foreach ($liveShoppingList as $liveShoppingId => &$liveShopping) {
            $liveShopping = array_merge_recursive($liveShopping, $liveShoppingPrices[$liveShoppingId]);
            $liveShopping = array_merge_recursive($liveShopping, $liveShoppingTaxes[$liveShopping['article_id']]);

            $liveShopping = $this->priceService->applyLiveShoppingPrice($liveShopping, $customerGroup, $isTaxInput);
        }

        return $liveShoppingList;
    }

    private function getLiveShoppingListData(array $liveShoppingIds): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $liveShoppingList = $queryBuilder->select(['id', 'liveProduct.*'])
            ->from('s_articles_lives', 'liveProduct')
            ->where('id IN (:liveShoppingIds)')
            ->setParameter('liveShoppingIds', $liveShoppingIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_GROUP);

        return $this->removeUnusedArray($liveShoppingList);
    }

    private function getLiveShoppingPrices(array $liveShoppingIds, int $customerGroupId): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $livePrices = $queryBuilder->select(['prices.live_shopping_id', 'prices.id as priceId', 'prices.price', 'prices.endprice'])
            ->from('s_articles_live_prices', 'prices')
            ->where('prices.live_shopping_id IN (:liveShoppingIds)')
            ->andWhere('prices.customer_group_id LIKE :customerGroupId')
            ->setParameter('liveShoppingIds', $liveShoppingIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('customerGroupId', $customerGroupId)
            ->execute()
            ->fetchAll(\PDO::FETCH_GROUP);

        return $this->removeUnusedArray($livePrices);
    }

    private function getLiveShoppingTax(array $productIds): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $taxes = $queryBuilder->select(['product.id', 'tax.tax'])
            ->from('s_articles', 'product')
            ->join('product', 's_core_tax', 'tax', 'product.taxID = tax.id')
            ->where('product.id in (:productIds)')
            ->setParameter('productIds', $productIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_GROUP);

        return $this->removeUnusedArray($taxes);
    }

    private function removeUnusedArray(array $data): array
    {
        $result = [];
        foreach ($data as $index => $datum) {
            $result[$index] = array_shift($datum);
        }

        return $result;
    }
}
