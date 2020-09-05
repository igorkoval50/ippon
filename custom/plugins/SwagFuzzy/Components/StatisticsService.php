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

namespace SwagFuzzy\Components;

use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class StatisticsService implements StatisticsServiceInterface
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function applyPeriodSearchCount(array $searchResult, DateTimeInterface $from, DateTimeInterface $to): array
    {
        $resultByShopTermAndIndex = $this->getShopIdSearchTermAndIndex($searchResult);

        foreach ($resultByShopTermAndIndex as $shopId => $searchTerms) {
            $queryBuilder = $this->getQuery(array_keys($searchTerms), $from, $to, $shopId);
            $termCountResult = $queryBuilder
                ->execute()
                ->fetchAll(\PDO::FETCH_ASSOC);

            $searchResult = $this->applyCountsToSearchResult($searchTerms, $searchResult, $termCountResult);
        }

        foreach ($searchResult as &$result) {
            if (!isset($result['currentCount'])) {
                $result['currentCount'] = 0;
            }
        }

        return $searchResult;
    }

    protected function getQuery(array $searchTerms, DateTimeInterface $from, DateTimeInterface $to, int $shopId): QueryBuilder
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select(['COUNT(id) AS count', 'searchterm as term'])
            ->from('s_statistics_search')
            ->where('searchterm IN (:terms)')
            ->andWhere('datum BETWEEN :from AND :to')
            ->andWhere('shop_id = :shopId')
            ->groupBy('searchterm')
            ->setParameter('terms', $searchTerms, Connection::PARAM_STR_ARRAY)
            ->setParameter('from', $from->format(self::DATE_FORMAT))
            ->setParameter('to', $to->format(self::DATE_FORMAT))
            ->setParameter('shopId', $shopId);

        return $queryBuilder;
    }

    private function getShopIdSearchTermAndIndex(array $searchResult): array
    {
        $resultByShopTermAndIndex = [];
        foreach ($searchResult as $index => $resultItem) {
            $resultByShopTermAndIndex[$resultItem['shopId']][$resultItem['searchTerm']] = $index;
        }

        return $resultByShopTermAndIndex;
    }

    private function applyCountsToSearchResult(array $searchTerms, array $searchResult, array $termCountResult): array
    {
        foreach ($termCountResult as &$itemResult) {
            $searchResult[$searchTerms[$itemResult['term']]]['currentCount'] = $itemResult['count'];
        }

        return $searchResult;
    }
}
