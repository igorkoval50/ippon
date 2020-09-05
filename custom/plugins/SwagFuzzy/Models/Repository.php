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

namespace SwagFuzzy\Models;

use DateTimeInterface;
use Doctrine\DBAL\Connection;
use Shopware\Components\Model\DBAL\Result;
use Shopware\Components\Model\ModelRepository;

/**
 * Shopware SwagFuzzy Plugin - Repository
 *
 * @category Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class Repository extends ModelRepository
{
    private const DATE_FORMAT_TEMPLATE = 'Y-m-d H:i:s';

    /**
     * returns the searches with no results.
     *
     * @param int    $offset
     * @param int    $limit
     * @param array  $sort
     * @param string $searchTerm
     * @param array  $shopIds
     *
     * @return Result
     */
    public function getSearchesWithoutResults(
        $offset,
        $limit,
        $sort = [],
        $searchTerm,
        $shopIds = [],
        Connection $connection,
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null
    ) {
        $builder = $connection->createQueryBuilder();

        $builder->select(
            [
                'search.searchTerm',
                'search.lastSearchDate',
                'search.searchesCount',
                'shops.name as shop',
                'shops.id as shopId',
            ])
            ->from('s_plugin_swag_fuzzy_statistics', 'search')
            ->join('search', 's_core_shops', 'shops', 'shops.id = search.shopId')
            ->where('search.resultsCount = 0')
            ->groupBy('search.searchTerm')
            ->addGroupBy('shops.id')
            ->orderBy('shops.id')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if (!empty($sort)) {
            foreach ($sort as $condition) {
                $builder->addOrderBy(
                    $condition['property'],
                    $condition['direction']
                );
            }
        }

        if (!empty($searchTerm)) {
            $builder->andWhere('search.searchTerm LIKE :searchTerm')
                ->setParameter('searchTerm', '%' . $searchTerm . '%');
        }

        if (!empty($shopIds)) {
            $builder->andWhere('search.shopId IN (:shopIds)')
                ->setParameter('shopIds', $shopIds, Connection::PARAM_INT_ARRAY);
        }

        if ($from !== null && $to !== null) {
            $builder->join('search', 's_statistics_search', 'coreSearch', 'search.searchTerm = coreSearch.searchterm')
                ->andWhere('coreSearch.datum BETWEEN :fromDate AND :toDate')
                ->setParameter('fromDate', $from->format(self::DATE_FORMAT_TEMPLATE))
                ->setParameter('toDate', $to->format(self::DATE_FORMAT_TEMPLATE));
        }

        return new Result($builder);
    }
}
