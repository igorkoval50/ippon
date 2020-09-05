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

namespace SwagPromotion\Models\Repository;

use Doctrine\DBAL\Connection;
use SwagPromotion\Components\InfoInterface;
use SwagPromotion\Models\Hydrator;

class PromotionRepository implements Repository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Hydrator
     */
    private $hydrator;

    public function __construct(Connection $connection, Hydrator $promotionHydrator)
    {
        $this->connection = $connection;
        $this->hydrator = $promotionHydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function getActivePromotions(
        $customerGroupId = null,
        $shopId = null,
        array $voucherIds = []
    ) {
        return $this->hydrator->hydrate(
            $this->filterPromotions(
                $this->fetchPromotions($shopId),
                $customerGroupId,
                $shopId,
                $voucherIds
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotionCounts($customerId)
    {
        $previousPromotionCounts = [];
        if ($customerId) {
            $previousPromotionCounts = $this->connection->createQueryBuilder()
                ->select(['promotion_id', 'COUNT(promotion_id)'])
                ->from('s_plugin_promotion_customer_count')
                ->where(' customer_id = :customerId')
                ->groupBy('promotion_id')
                ->setParameter('customerId', $customerId)
                ->execute()
                ->fetchAll(\PDO::FETCH_KEY_PAIR);
        }

        return $previousPromotionCounts;
    }

    /**
     * @param string|null $customerGroupId
     * @param int|null    $shopId
     *
     * @return array
     */
    private function filterPromotions(
        array $promotions,
        $customerGroupId = null,
        $shopId = null,
        array $voucherIds = []
    ) {
        return array_filter(
            $promotions,
            function ($promotion) use ($customerGroupId, $shopId, $voucherIds) {
                if (!empty($promotion['voucher_id']) && !in_array($promotion['voucher_id'], $voucherIds)) {
                    return false;
                }

                if ($customerGroupId && !empty($promotion['customer_groups'])
                    && !in_array($customerGroupId, $promotion['customer_groups'])
                ) {
                    return false;
                }

                if ($shopId && !empty($promotion['shops']) && !in_array($shopId, $promotion['shops'])) {
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * @param int $shopId
     *
     * @return array
     */
    private function fetchPromotions($shopId)
    {
        $promotions = $this->getPromotions();

        if (!$promotions) {
            return [];
        }

        $promotionIds = array_keys($promotions);

        $customerGroups = $this->getCustomerGroupIds($promotionIds);
        $this->populateWith($promotions, $customerGroups, 'customer_groups');

        $shops = $this->getShopIds($promotionIds);
        $this->populateWith($promotions, $shops, 'shops');

        $doNotAllowLater = $this->getDoNotAllowLaterPromotions($promotionIds);
        $this->populateWith($promotions, $doNotAllowLater, 'do_not_allow_later');

        $doNotRunAfter = $this->getDoNotRunAfterPromotions($promotionIds);
        $this->populateWith($promotions, $doNotRunAfter, 'do_not_run_after');

        $freeGoods = $this->getFreeGoodsPromotions($promotionIds);
        $this->populateWith($promotions, $freeGoods, 'free_goods');

        $promotions = $this->removeFreeGoodPromotionsWithoutFreeGoods($promotions);

        // if no translation required: return
        if (!$shopId) {
            return $promotions;
        }

        return $this->populatePromotionsWithTranslations($promotions, $promotionIds, $shopId);
    }

    /**
     * @param string $key
     */
    private function populateWith(array &$promotions, array $add, $key)
    {
        foreach ($add as $promotionId => $ids) {
            $promotions[$promotionId][$key] = $ids;
        }
    }

    /**
     * @return array
     */
    private function getPromotions()
    {
        // The date filter was formerly performed in "filterPromotions" (so in the PHP stack)
        // but can also be performed quickly via SQL
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder->select(['promotion.id AS ak, promotion.*'])
            ->from('s_plugin_promotion', 'promotion')
            ->where('active = 1')
            ->andWhere('(DATE_ADD(NOW(),INTERVAL 1 SECOND) > valid_from OR valid_from IS NULL)')
            ->andWhere('(DATE_SUB(NOW(),INTERVAL 1 SECOND) < valid_to OR valid_to IS NULL)')
            ->addOrderBy('exclusive', 'DESC')
            ->addOrderBy('priority', 'DESC')
            ->execute()
            ->fetchAll(\PDO::FETCH_UNIQUE);
    }

    /**
     * @return array
     */
    private function getCustomerGroupIds(array $promotionIds)
    {
        $result = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('s_plugin_promotion_customer_group')
            ->where('promotionID IN (:promotionIds)')
            ->setParameter('promotionIds', $promotionIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll();

        if (!$result) {
            return [];
        }

        return $this->groupBy($result, 'promotionID', 'groupID');
    }

    /**
     * @return array
     */
    private function getShopIds(array $promotionIds)
    {
        $result = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('s_plugin_promotion_shop')
            ->where('promotionID IN (:promotionIds)')
            ->setParameter('promotionIds', $promotionIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll();

        if (!$result) {
            return [];
        }

        return $this->groupBy($result, 'promotionID', 'shopID');
    }

    /**
     * @return array
     */
    private function getDoNotAllowLaterPromotions(array $promotionIds)
    {
        $result = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('s_plugin_promotion_do_not_allow_later')
            ->where('promotionID IN (:promotionIds)')
            ->setParameter('promotionIds', $promotionIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll();

        if (!$result) {
            return [];
        }

        return $this->groupBy($result, 'promotionID', 'doNotAllowLaterID');
    }

    /**
     * @return array
     */
    private function getDoNotRunAfterPromotions(array $promotionIds)
    {
        $result = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('s_plugin_promotion_do_not_run_after')
            ->where('promotionID IN (:promotionIds)')
            ->setParameter('promotionIds', $promotionIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll();

        if (!$result) {
            return [];
        }

        return $this->groupBy($result, 'promotionID', 'doNotRunAfterID');
    }

    /**
     * @return array
     */
    private function getFreeGoodsPromotions(array $promotionIds)
    {
        $result = $this->connection->createQueryBuilder()
            ->select('fg.*')
            ->from('s_plugin_promotion_free_goods', 'fg')
            ->where('promotionID IN (:promotionIds)')
            ->join('fg',
                's_articles_details',
                'ad',
                'ad.articleID = fg.articleID AND ((ad.laststock = 0) OR (ad.laststock = 1 AND ad.instock > 0))'
            )
            ->setParameter('promotionIds', $promotionIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll();

        if (!$result) {
            return [];
        }

        return $this->groupBy($result, 'promotionID', 'articleID');
    }

    /**
     * @param string      $by
     * @param string|null $valueField
     *
     * @return array
     */
    private function groupBy(array $array, $by, $valueField = null)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (!in_array($value, $result[$by])) {
                $result[$value[$by]][] = $valueField ? $value[$valueField] : $value;
            }
        }

        return $result;
    }

    /**
     * @param int $shopId
     *
     * @return array
     */
    private function getTranslations(array $promotionIds, $shopId)
    {
        return $this->connection->createQueryBuilder()
            ->select(['objectkey', 'objectdata'])
            ->from('s_core_translations')
            ->where('objectkey IN (:promotionIds)')
            ->andWhere('objectlanguage = :shopId')
            ->andWhere('objecttype = "swag-promotion-settings"')
            ->setParameter('promotionIds', $promotionIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('shopId', $shopId)
            ->execute()
            ->fetchAll();
    }

    /**
     * Populate the promotions with translations - if translations are available
     *
     * @param int $shopId
     *
     * @return array
     */
    private function populatePromotionsWithTranslations(array $promotions, array $promotionIds, $shopId)
    {
        $translations = $this->getTranslations($promotionIds, $shopId);

        foreach ($translations as $translation) {
            $promotionId = $translation['objectkey'];
            $translation = unserialize($translation['objectdata']);

            if (!$translation) {
                continue;
            }

            if (isset($translation['badgeText'])) {
                $promotions[$promotionId]['badge_text'] = $translation['badgeText'];
            }

            if (isset($translation['freeGoodsBadgeText'])) {
                $promotions[$promotionId]['free_goods_badge_text'] = $translation['freeGoodsBadgeText'];
            }

            if (isset($translation['name'])) {
                $promotions[$promotionId]['name'] = $translation['name'];
            }

            if (isset($translation['description'])) {
                $promotions[$promotionId]['description'] = $translation['description'];
            }

            if (isset($translation['detailDescription'])) {
                $promotions[$promotionId]['detail_description'] = $translation['detailDescription'];
            }
        }

        return $promotions;
    }

    /**
     * @param array $promotions
     *
     * @return array
     */
    private function removeFreeGoodPromotionsWithoutFreeGoods($promotions)
    {
        foreach ($promotions as $promotionId => $promotionData) {
            if ($promotionData['type'] !== 'product.freegoods') {
                continue;
            }

            if (!isset($promotionData['free_goods']) || empty($promotionData['free_goods'])) {
                unset($promotions[$promotionId]);
                $this->deactivatePromotion($promotionId);
                $this->setInfoForPromotion($promotionId, InfoInterface::NO_FREE_GOODS_LEFT);
            }
        }

        return $promotions;
    }

    /**
     * @param int $promotionId
     */
    private function deactivatePromotion($promotionId)
    {
        $this->connection->createQueryBuilder()
            ->update('s_plugin_promotion', 'p')
            ->set('p.active', 0)
            ->where('p.id = :promotionId')
            ->setParameter('promotionId', $promotionId)
            ->execute();
    }

    /**
     * @param int    $promotionId
     * @param string $info
     */
    private function setInfoForPromotion($promotionId, $info)
    {
        $this->connection->createQueryBuilder()
            ->insert('s_plugin_promotion_info')
            ->setValue('promotion_id', ':promotionId')
            ->setValue('info', ':info')
            ->setParameter('promotionId', $promotionId)
            ->setParameter('info', $info)
            ->execute();
    }
}
