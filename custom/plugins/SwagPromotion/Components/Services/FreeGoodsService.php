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

namespace SwagPromotion\Components\Services;

use Doctrine\DBAL\Connection;
use SwagPromotion\Components\Promotion\ProductStacker\ProductStackRegistry;
use SwagPromotion\Struct\Promotion;

class FreeGoodsService implements FreeGoodsServiceInterface
{
    /**
     * @var Connection
     */
    private $dbConnection;

    /**
     * @var ProductStackRegistry
     */
    private $stackerRegistry;

    /**
     * @var DependencyProvider
     */
    private $dependencyProvider;

    public function __construct(Connection $dbConnection, ProductStackRegistry $productStackRegistry, DependencyProvider $dependencyProvider)
    {
        $this->dbConnection = $dbConnection;
        $this->stackerRegistry = $productStackRegistry;
        $this->dependencyProvider = $dependencyProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function addArticleAsFreeGood($orderNumber, $promotionId, $quantity = 1)
    {
        $basket = $this->dependencyProvider->getModules()->Basket();
        $session = $this->dependencyProvider->getSession();

        $session->offsetSet('freeGoodsOrderNumber', $orderNumber);
        $basketId = $basket->sAddArticle($orderNumber, $quantity);
        $session->offsetUnset('freeGoodsOrderNumber');

        $sql = 'SELECT swag_is_free_good_by_promotion_id
                FROM s_order_basket_attributes
                WHERE basketID = :basketId;';
        $promotionIds = $this->dbConnection->executeQuery($sql, ['basketId' => $basketId])->fetchColumn();
        $promotionIds = unserialize($promotionIds);

        if ($promotionIds) {
            $promotionIds[] = $promotionId;
        } else {
            $promotionIds = [$promotionId];
        }

        $promotionIds = serialize($promotionIds);

        $sql = 'UPDATE s_order_basket_attributes
                SET swag_is_free_good_by_promotion_id = :promotionIds
                WHERE basketID = :basketId;';
        $this->dbConnection->executeQuery($sql, ['promotionIds' => $promotionIds, 'basketId' => $basketId]);

        $basket->sRefreshBasket();
    }

    /**
     * {@inheritdoc}
     */
    public function updateFreeGoodsItem($basketId, $quantity)
    {
        $sql = 'SELECT swag_is_free_good_by_promotion_id
                FROM s_order_basket_attributes
                WHERE basketID = :basketId;';
        $promotionIds = $this->dbConnection->executeQuery($sql, ['basketId' => $basketId])->fetchColumn();

        $promotionIds = unserialize($promotionIds);

        // no promotion IDs are set so do nothing
        if (!$promotionIds) {
            return false;
        }

        $amountPromotionIds = count($promotionIds);

        //quantity is higher than the amount of promotion IDs so there is no problem
        if ($quantity >= $amountPromotionIds) {
            return false;
        }

        // delete last promotion ID until amount of promotion IDs matches quantity
        $diff = $amountPromotionIds - $quantity;
        for ($i = 0; $i < $diff; ++$i) {
            array_pop($promotionIds);
        }

        $this->dbConnection->update(
            's_order_basket_attributes',
            [
                'swag_is_free_good_by_promotion_id' => $promotionIds ? serialize($promotionIds) : null,
            ],
            [
                'basketID' => $basketId,
            ]
        );

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clearFreeGoodsFromBasket($basketItems, array $freeGoods, $promotionId)
    {
        foreach ($basketItems as $basketItem) {
            if (!in_array($basketItem['articleID'], $freeGoods)) {
                continue;
            }

            $basketId = $basketItem['id'];

            $sql = 'SELECT swag_is_free_good_by_promotion_id
                    FROM s_order_basket_attributes
                    WHERE basketID = :basketId;';
            $promotionIds = $this->dbConnection->executeQuery($sql, ['basketId' => $basketId])->fetchColumn();

            $promotionIds = unserialize($promotionIds);

            if (!$promotionIds) {
                continue;
            }

            $promotionIds = array_diff($promotionIds, [$promotionId]);

            $this->dbConnection->update(
                's_order_basket_attributes',
                [
                    'swag_is_free_good_by_promotion_id' => $promotionIds ? serialize($promotionIds) : null,
                ],
                [
                    'basketID' => $basketId,
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAchievedStack(Promotion $promotion, array $matches)
    {
        $stacker = $this->stackerRegistry->getStacker($promotion->stackMode);
        $result = $stacker->getStack(
            $matches,
            $promotion->step,
            $promotion->maxQuantity,
            $promotion->chunkMode
        );

        if ($result) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function applyInfo(array $promotionIds, array $list)
    {
        $infos = $this->dbConnection->createQueryBuilder()
            ->select(['promotion_id', 'info'])
            ->from('s_plugin_promotion_info')
            ->where('promotion_id IN (:ids)')
            ->setParameter('ids', $promotionIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_KEY_PAIR);

        foreach ($list['data'] as &$promo) {
            if (isset($infos[$promo['id']])) {
                $promo['backendInfo'] = $infos[$promo['id']];
            }
        }

        return $list;
    }

    public function checkLineItem(array $lineItem, int $promotionId, array $freeGoodProductIds): bool
    {
        // only items that have been explicitly placed as "freeGoods" will get a discount
        if (empty($lineItem['isFreeGoodByPromotionId'])) {
            return false;
        }

        if (!in_array($lineItem['articleID'], $freeGoodProductIds)) {
            return false;
        }

        $promotionIds = unserialize($lineItem['isFreeGoodByPromotionId']);
        if (!in_array($promotionId, $promotionIds)) {
            return false;
        }

        return true;
    }
}
