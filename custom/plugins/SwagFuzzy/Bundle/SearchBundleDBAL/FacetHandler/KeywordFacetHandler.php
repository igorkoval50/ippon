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

namespace SwagFuzzy\Bundle\SearchBundleDBAL\FacetHandler;

use Shopware\Bundle\SearchBundle\Criteria;
use Shopware\Bundle\SearchBundle\FacetInterface;
use Shopware\Bundle\SearchBundle\FacetResult\FacetResultGroup;
use Shopware\Bundle\SearchBundle\FacetResult\RadioFacetResult;
use Shopware\Bundle\SearchBundle\FacetResult\ValueListItem;
use Shopware\Bundle\SearchBundleDBAL\KeywordFinderInterface;
use Shopware\Bundle\SearchBundleDBAL\PartialFacetHandlerInterface;
use Shopware\Bundle\SearchBundleDBAL\SearchTerm\Keyword;
use Shopware\Bundle\SearchBundleDBAL\SearchTerm\TermHelperInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Shopware_Components_Snippet_Manager;
use SwagFuzzy\Bundle\SearchBundle\Facet\KeywordFacet;
use SwagFuzzy\Bundle\SearchBundleDBAL\SearchTerm\FuzzyKeywordFinder;
use SwagFuzzy\Components\SimilarResultsService;
use SwagFuzzy\Components\SynonymService;

/**
 * Class KeywordFacetHandler
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class KeywordFacetHandler implements PartialFacetHandlerInterface
{
    /**
     * @var TermHelperInterface
     */
    private $termHelper;

    /**
     * @var FuzzyKeywordFinder
     */
    private $keywordFinder;

    /**
     * @var SimilarResultsService
     */
    private $similarResultsService;

    /**
     * @var SynonymService
     */
    private $synonymService;

    /**
     * @var Shopware_Components_Snippet_Manager
     */
    private $snippetManager;

    /**
     * initialises the private properties
     *
     * @throws \Exception
     */
    public function __construct(
        KeywordFinderInterface $keywordFinder,
        TermHelperInterface $termHelper,
        SimilarResultsService $similarResultsService,
        SynonymService $synonymService,
        Shopware_Components_Snippet_Manager $snippetManager
    ) {
        $this->keywordFinder = $keywordFinder;
        $this->termHelper = $termHelper;
        $this->similarResultsService = $similarResultsService;
        $this->synonymService = $synonymService;
        $this->snippetManager = $snippetManager;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsFacet(FacetInterface $facet)
    {
        return $facet instanceof KeywordFacet;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePartialFacet(
        FacetInterface $facet,
        Criteria $reverted,
        Criteria $criteria,
        ShopContextInterface $context
    ) {
        $shopId = $context->getShop()->getId();

        /** @var KeywordFacet $facet */
        $keywords = $this->keywordFinder->getKeywordsOfTerm(
            $facet->getTerm()
        );

        $filtered = $this->filterKeywordsByTerm(
            $facet->getTerm(),
            $keywords
        );

        $similarResults = $this->similarResultsService->getSimilarResults(
            $facet->getTerm(),
            $filtered,
            $shopId
        );

        $synonyms = $this->synonymService->getSynonyms(
            $facet->getTerm(),
            $shopId
        );

        /* @var Keyword $synonym */
        if (!empty($synonyms)) {
            foreach ($synonyms as $synonym) {
                $similarResults[] = [
                    'searchTerm' => $synonym,
                ];
            }
        }

        if (!empty($similarResults)) {
            $items[] = $this->createSimilarTermFacetResult($similarResults);
        }

        if (!empty($keywords)) {
            $items[] = $this->createKeywordFacetResult($keywords);
        }

        if (empty($items)) {
            return null;
        }

        return new FacetResultGroup(
            $items,
            $this->getFacetLabel('facet_result'),
            $facet->getName(),
            [],
            null
        );
    }

    /**
     * helper method to create the keyword facet result
     *
     * @param Keyword[] $keywords
     *
     * @return RadioFacetResult
     */
    private function createKeywordFacetResult($keywords)
    {
        $items = [];

        $facetName = 'related_terms';

        $facetLabel = $this->getFacetLabel($facetName);

        foreach ($keywords as $keyword) {
            $items[] = new ValueListItem(
                $keyword->getWord(),
                $keyword->getWord(),
                false
            );
        }

        return new RadioFacetResult(
            $facetName,
            false,
            $facetLabel,
            $items,
            'related-terms',
            [],
            null
        );
    }

    /**
     * helper method to create the similar term facet result
     *
     * @param $similarResults
     *
     * @return RadioFacetResult
     */
    private function createSimilarTermFacetResult($similarResults)
    {
        $items = [];

        $facetName = 'similar_requests';

        $facetLabel = $this->getFacetLabel($facetName);

        foreach ($similarResults as $result) {
            $items[] = new ValueListItem(
                $result['searchTerm'],
                $result['searchTerm'],
                false
            );
        }

        return new RadioFacetResult(
            $facetName,
            false,
            $facetLabel,
            $items,
            'similar-requests',
            [],
            null
        );
    }

    /**
     * helper method to filter the search term from the found keywords
     *
     * @param $term
     * @param Keyword[] $keywords
     *
     * @return array
     */
    private function filterKeywordsByTerm($term, $keywords)
    {
        $terms = $this->termHelper->splitTerm($term);

        $keywordsResult = [];
        foreach ($keywords as $keyword) {
            if ($keyword->getTerm() === $keyword->getWord()) {
                continue;
            }

            $position = array_search($keyword->getWord(), $terms);

            if ($position) {
                continue;
            }
            $keywordsResult[] = $keyword->getWord();
        }

        return $keywordsResult;
    }

    /**
     * helper method to get the facet label
     *
     * @param string $facetName
     *
     * @return mixed
     */
    private function getFacetLabel($facetName)
    {
        $facetLabel = $this->snippetManager->getNamespace('frontend/search/fuzzy')->get($facetName);

        return $facetLabel;
    }
}
