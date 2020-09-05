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

use Doctrine\DBAL\Query\QueryBuilder;
use Shopware\Bundle\SearchBundleDBAL\SearchTerm\TermHelperInterface;
use Shopware\Bundle\SearchBundleDBAL\SearchTermQueryBuilderInterface;
use Shopware_Components_Config as Config;
use SwagFuzzy\Components\SettingsService;

/**
 * Class FuzzySearchTermQueryBuilder
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class FuzzySearchTermQueryBuilder implements SearchTermQueryBuilderInterface
{
    /**
     * @var SearchTermQueryBuilderInterface
     */
    private $coreSearchTermQueryBuilder;

    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var TermHelperInterface
     */
    private $termHelper;

    public function __construct(
        SearchTermQueryBuilderInterface $searchTermQueryBuilder,
        SettingsService $settingsService,
        Config $config,
        TermHelperInterface $termHelper
    ) {
        $this->coreSearchTermQueryBuilder = $searchTermQueryBuilder;
        $this->settingsService = $settingsService;
        $this->config = $config;
        $this->termHelper = $termHelper;
    }

    /**
     * decorates the buildQuery method to exchange the relevance values by the values given from fuzzy settings
     *
     * {@inheritdoc}
     */
    public function buildQuery($term)
    {
        $searchTermQueryBuilder = $this->coreSearchTermQueryBuilder->buildQuery($term);

        $enableAndSearchLogic = $this->config->get('enableAndSearchLogic', false);

        if (is_null($searchTermQueryBuilder)) {
            return $searchTermQueryBuilder;
        }

        $selectPart = $searchTermQueryBuilder->getQueryPart('select');

        $selectRanking = $selectPart[1];
        $selectRanking = str_replace($this->getRelevanceSelection(), $this->getNewRelevanceSelection(), $selectRanking);

        $searchTermQueryBuilder->resetQueryPart('select');
        $searchTermQueryBuilder->resetQueryPart('where');

        $this->addToleranceCondition($searchTermQueryBuilder);
        $searchTermQueryBuilder->select(
            [
                $selectPart[0],
                $selectRanking,
            ]
        );

        if ($enableAndSearchLogic) {
            $this->addAndSearchLogic($searchTermQueryBuilder, $term);
        }

        return $searchTermQueryBuilder;
    }

    /**
     * checks if the given result set matches all search terms
     *
     * @param QueryBuilder $query
     * @param string       $term
     */
    private function addAndSearchLogic($query, $term)
    {
        $searchTerms = $this->termHelper->splitTerm($term);
        $query->andWhere('termCount >= ' . count($searchTerms));
    }

    /**
     * helper method to get the old relevance select part
     *
     * @return string
     */
    private function getRelevanceSelection()
    {
        return 'sr.relevance
        + IF(a.topseller = 1, 50, 0)
        + IF(a.datum >= DATE_SUB(NOW(),INTERVAL 7 DAY), 25, 0)';
    }

    /**
     * helper method to get the new relevance select part
     *
     * @return string
     */
    private function getNewRelevanceSelection()
    {
        $fuzzySettings = $this->settingsService->getSettings();
        $topSellerRelevance = $fuzzySettings['topSellerRelevance'];
        $newArticleRelevance = $fuzzySettings['newArticleRelevance'];
        $articleMarkAsNewDays = $this->config->get('markasnew');

        $relevanceString = 'sr.relevance
        + IF(a.topseller = 1, ' . $topSellerRelevance . ', 0)
        + IF(a.datum >= DATE_SUB(NOW(),INTERVAL ' . $articleMarkAsNewDays . ' DAY), ' . $newArticleRelevance . ', 0)';

        return $relevanceString;
    }

    /**
     * Calculates the new search tolerance and adds an where condition
     * to the query
     */
    private function addToleranceCondition(QueryBuilder $query)
    {
        $distance = $this->settingsService->getSettings()['searchMinDistancesTop'];
        $query->select('MAX(' . $this->getNewRelevanceSelection() . ") / 100 * $distance");

        //calculates the tolerance limit
        if ($distance) {
            $query->andWhere('(' . $this->getNewRelevanceSelection() . ') > (' . $query->getSQL() . ')');
        }
    }
}
