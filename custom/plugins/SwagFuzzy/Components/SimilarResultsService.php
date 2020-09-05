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

use Doctrine\DBAL\Connection;
use Shopware\Bundle\SearchBundleDBAL\SearchTerm\TermHelperInterface;

/**
 * Class SimilarResultsService
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class SimilarResultsService implements SimilarResultServiceInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var TermHelperInterface
     */
    private $termHelper;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(
        Connection $connection,
        TermHelperInterface $termHelper,
        SettingsService $settingsService
    ) {
        $this->connection = $connection;
        $this->termHelper = $termHelper;
        $this->settingsService = $settingsService;
    }

    /**
     * {@inheritdoc}
     */
    public function getSimilarResults($term, $keywords, $shopId)
    {
        $similarTerms = $keywords;
        $terms = $this->termHelper->splitTerm($term);
        $similarTerms = array_unique(array_merge($similarTerms, $terms));

        if (empty($similarTerms)) {
            return [];
        }

        $sql = [];
        $term = $this->connection->quote($term);
        $limit = $this->settingsService->getSettings()['maxKeywordsAndSimilarWords'];

        foreach ($similarTerms as $similarTerm) {
            $similarTerm = $this->connection->quote($similarTerm);
            $sql[] = "
                SELECT
                  `searchTerm`,
                  `searchesCount`,
                  `lastSearchDate`,
                  `resultsCount`
                FROM `s_plugin_swag_fuzzy_statistics`
                WHERE `searchTerm` NOT LIKE {$term}
                    AND (
                        `searchTerm` LIKE {$similarTerm}
                        OR `searchTerm` LIKE CONCAT({$similarTerm}, ' %')
                        OR `searchTerm` LIKE CONCAT('% ', {$similarTerm})
                        OR `searchTerm` LIKE CONCAT('% ', {$similarTerm}, ' %')
                    )
                AND `resultsCount` > 0
                AND `shopId` = {$shopId}";
        }
        $sql = '(' . implode(') UNION ALL (', $sql) . ')';
        $sql .= "
            ORDER BY searchesCount DESC, resultsCount DESC
            LIMIT {$limit}";

        return $this->connection->fetchAll($sql);
    }
}
