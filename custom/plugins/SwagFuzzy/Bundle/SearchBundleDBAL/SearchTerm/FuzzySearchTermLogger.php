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

namespace SwagFuzzy\Bundle\SearchBundleDBAL\SearchTerm;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Shopware\Bundle\SearchBundle\Condition\SearchTermCondition;
use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\ProductNumberSearchResult;
use Shopware\Bundle\SearchBundleDBAL\SearchTerm\SearchTermLoggerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;

/**
 * Class FuzzySearchTermLogger
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class FuzzySearchTermLogger implements SearchTermLoggerInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var SearchTermLoggerInterface
     */
    private $coreSearchTermLogger;

    public function __construct(Connection $connection, SearchTermLoggerInterface $searchTermLoggerInterface)
    {
        $this->connection = $connection;
        $this->coreSearchTermLogger = $searchTermLoggerInterface;
    }

    /**
     * decorates the logResult method to write into fuzzy statistics table
     *
     * {@inheritdoc}
     */
    public function logResult(
        Criteria $criteria,
        ProductNumberSearchResult $result,
        Shop $shop
    ) {
        $this->coreSearchTermLogger->logResult($criteria, $result, $shop);

        /** @var SearchTermCondition $condition */
        $condition = $criteria->getCondition('search');

        if (!$condition) {
            return;
        }

        $term = $condition->getTerm();
        $totalCount = $result->getTotalCount();
        $shopId = $shop->getId();
        $now = new DateTime();

        $this->writeStatisticTable($term, $totalCount, $shopId, $now);
    }

    /**
     * helper method to update the fuzzy statistics table
     *
     * @param string $term
     * @param int    $totalCount
     * @param int    $shopId
     *
     * @throws DBALException
     */
    private function writeStatisticTable($term, $totalCount, $shopId, DateTime $now)
    {
        $sql = 'INSERT INTO `s_plugin_swag_fuzzy_statistics` (`shopId`, `searchTerm`, `firstSearchDate`, `lastSearchDate`, `searchesCount`, `resultsCount`)
                VALUES (:shopId, :searchTerm, :searchDate, :searchDate, :searchesCount, :resultsCount)
                ON DUPLICATE KEY UPDATE `lastSearchDate` = :searchDate, `searchesCount` = `searchesCount` +1, `resultsCount` = :resultsCount;';

        $this->connection->executeUpdate(
            $sql,
            [
                'shopId' => $shopId,
                'searchTerm' => $term,
                'searchDate' => $now->format('Y-m-d H:i:s'),
                'searchesCount' => 1,
                'resultsCount' => $totalCount,
            ]
        );
    }
}
