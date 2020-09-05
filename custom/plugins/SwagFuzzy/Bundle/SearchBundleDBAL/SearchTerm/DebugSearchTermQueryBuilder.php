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
use Shopware\Bundle\SearchBundleDBAL\SearchTermQueryBuilderInterface;

/**
 * Class DebugSearchTermQueryBuilder
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class DebugSearchTermQueryBuilder implements SearchTermQueryBuilderInterface
{
    /**
     * @var SearchTermQueryBuilderInterface
     */
    private $coreSearchTermQueryBuilder;

    public function __construct(SearchTermQueryBuilderInterface $searchTermQueryBuilder)
    {
        $this->coreSearchTermQueryBuilder = $searchTermQueryBuilder;
    }

    /**
     * decorates the buildQuery method to add debug information
     *
     * {@inheritdoc}
     */
    public function buildQuery($term)
    {
        $searchTermQueryBuilder = $this->coreSearchTermQueryBuilder->buildQuery($term);

        if ($searchTermQueryBuilder === null) {
            return $searchTermQueryBuilder;
        }

        $this->addDebugKeywords($searchTermQueryBuilder);

        $searchTermQueryBuilder->addSelect('a.topseller as isTopSeller');

        return $searchTermQueryBuilder;
    }

    private function addDebugKeywords(QueryBuilder $searchTermQueryBuilder)
    {
        $relevanceSelect = 'SUM(srd.relevance) as relevance';
        $additionalSelect = 'GROUP_CONCAT(srd.keywordID) as keywords, GROUP_CONCAT(srd.relevance) as relevances';
        $fromPart = $searchTermQueryBuilder->getQueryPart('from');
        $fromPart[0]['table'] = str_replace(
            $relevanceSelect,
            $relevanceSelect . ', ' . $additionalSelect,
            $fromPart[0]['table']
        );
        $searchTermQueryBuilder->resetQueryPart('from');
        $searchTermQueryBuilder->from($fromPart[0]['table'], $fromPart[0]['alias']);
        $searchTermQueryBuilder->addSelect('sr.keywords');
        $searchTermQueryBuilder->addSelect('sr.relevances');
    }
}
