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

namespace SwagNewsletter\Components;

use Doctrine\DBAL\Connection;
use sArticles;
use Shopware_Components_Config;
use sMarketing;
use sSystem;

/**
 * This is a legacy core class that was moved to the newsletter during the shopware 5 development
 *
 * Some cleanup is still needed
 */
class SuggestService implements SuggestServiceInterface
{
    /**
     * @var sSystem
     */
    public $sSystem;

    /**
     * @var sMarketing
     */
    private $sMarketing;

    /**
     * @var sArticles
     */
    private $sArticles;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Shopware_Components_Config
     */
    private $config;

    /**
     * @param DependencyProviderInterface $dependencyProvider
     * @param Connection                  $connection
     * @param Shopware_Components_Config  $config
     */
    public function __construct(
        DependencyProviderInterface $dependencyProvider,
        Connection $connection,
        Shopware_Components_Config $config
    ) {
        $this->connection = $connection;
        $this->config = $config;
        $this->sSystem = $dependencyProvider->getModule('sSystem');
        $this->sArticles = $dependencyProvider->getModule('sArticles');
        $this->sMarketing = $dependencyProvider->getModule('sMarketing');
    }

    /**
     * {@inheritdoc}
     */
    public function getProductSuggestions($id, $userId = 0)
    {
        $id = (int) $id;

        $suggestInfo = $this->getSuggestInfo($id);

        unset($this->sArticles->sCachePromotions, $this->sMarketing->sBlacklist);
        if (!$suggestInfo['value'] || !isset($suggestInfo['description'])) {
            return false;
        }

        return [
            'description' => $suggestInfo['description'],
            'value' => $suggestInfo['value'],
            'data' => $this->getSuggestions($userId, $suggestInfo['value']),
        ];
    }

    /**
     * @param int $userId
     * @param int $limit
     *
     * @return array
     */
    private function getUserSuggestions($userId, $limit)
    {
        $finalRecommendations = [];

        // 1.) Get last viewed articles
        $selectLastAlsoView = [];

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('DISTINCT articleID')
            ->from('s_emarketing_lastarticles')
            ->where('userID = :userId')
            ->orderBy('time', 'DESC')
            ->setMaxResults(':limitation')
            ->setParameters(['userId' => $userId, ':limitation' => $limit]);

        $selectLast = $queryBuilder->execute()->fetchAll();

        $this->config->offsetSet('sMAXCROSSSIMILAR', 1);

        foreach ($selectLast as $lastProduct) {
            $this->sMarketing->sBlacklist[] = $lastProduct['articleID'];
        }

        foreach ($selectLast as $lastProduct) {
            $temp = $this->sMarketing->sGetSimilaryShownArticles(
                $lastProduct['articleID']
            );
            if ($temp[0]['id']) {
                $selectLastAlsoView[]['articleID'] = $temp[0]['id'];
            }
        }

        if (count($selectLastAlsoView)) {
            $selectLast = array_merge($selectLast, $selectLastAlsoView);
        }

        // 2.) Get last bought articles
        $selectLastAlsoBought = [];

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('DISTINCT articleID')
            ->from('s_order_details, s_order')
            ->where('s_order.userID = :userId')
            ->andWhere('s_order_details.orderID = s_order.id')
            ->orderBy('ordertime', 'DESC')
            ->setMaxResults(':limitation')
            ->setParameters(['userId' => $userId, 'limitation' => $limit]);

        $selectLastOrders = $queryBuilder->execute()->fetchAll();

        foreach ($selectLastOrders as $lastProduct) {
            $this->sMarketing->sBlacklist[] = $lastProduct['articleID'];
        }
        foreach ($selectLastOrders as $lastProduct) {
            $temp = $this->sMarketing->sGetAlsoBoughtArticles($lastProduct['articleID']);
            if ($temp[0]['id']) {
                $selectLastAlsoBought[]['articleID'] = $temp[0]['id'];
            }
        }
        if (count($selectLastAlsoBought)) {
            $selectLast = array_merge($selectLast, $selectLastAlsoBought);
        }

        $blacklist = [];

        $countRecommendations = count($selectLast);
        if ($countRecommendations) {
            foreach ($selectLast as $lastProduct) {
                $category = $this->sSystem->_GET['sCategory'] ?: 0;
                $temp = $this->sArticles->sGetPromotionById(
                    'fix',
                    $category,
                    $lastProduct['articleID']
                );
                if ($temp['articleID'] && empty($blacklist[$temp['articleID']])) {
                    $finalRecommendations[] = $temp;
                    $blacklist[$temp['articleID']] = $temp['articleID'];
                }
            }
        }

        return $finalRecommendations;
    }

    /**
     * Returns all live shopping articles which fulfill the given filter conditions
     *
     * @param int $id
     *
     * @return array|null
     */
    private function getSuggestInfo($id)
    {
        $sql = "
            SELECT value, description
            FROM s_campaigns_containers
            WHERE type='ctSuggest'
            AND promotionID=?
            ";

        return $this->connection->executeQuery($sql, [$id])->fetch();
    }

    /**
     * @param int   $leftRecommendations
     * @param array $addedProducts
     *
     * @return array
     */
    private function getGenericSuggestions($leftRecommendations, array $addedProducts)
    {
        $randomize = ['new', 'top'];
        $category = $this->sSystem->_GET['sCategory'] ?: $this->sSystem->sLanguageData[$this->sSystem->sLanguage]['parentID'];
        $result = [];

        while ($leftRecommendations > 0 && $randomize) {
            $promotionType = $randomize[array_rand($randomize)];
            $product = $this->sArticles->sGetPromotionById(
                $promotionType,
                $category,
                ''
            );

            if (!empty($product) && !in_array($product['articleID'], $addedProducts)) {
                --$leftRecommendations;
                $this->sArticles->sCachePromotions[] = $product['articleID'];
                $addedProducts[] = $product['articleID'];
                $result[] = $product;
            } else {
                //if we don't have new/topseller products remove them from the list
                $key = array_search($promotionType, $randomize);
                unset($randomize[$key]);
            }
        }

        return $result;
    }

    /**
     * @param int $userId
     * @param int $number
     *
     * @return array
     */
    private function getSuggestions($userId, $number)
    {
        // Get personalized articles
        $suggestedProducts = [];
        $addedProducts = [];
        if ($userId) {
            $limit = (int) ($number / 2);
            $suggestedProducts = $this->getUserSuggestions($userId, $limit);
            $addedProducts = array_column($suggestedProducts, 'articleID');
        }

        $leftRecommendations = $number - count($suggestedProducts);

        $suggestedProducts = array_merge(
            $suggestedProducts,
            $this->getGenericSuggestions($leftRecommendations, $addedProducts)
        );

        return $suggestedProducts;
    }
}
