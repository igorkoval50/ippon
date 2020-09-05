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

use Doctrine\DBAL\Connection;
use Shopware\Bundle\SearchBundleDBAL\KeywordFinderInterface;
use Shopware\Bundle\SearchBundleDBAL\SearchTerm\Keyword;
use Shopware\Bundle\SearchBundleDBAL\SearchTerm\TermHelperInterface;
use Shopware\Bundle\StoreFrontBundle\Service\ContextServiceInterface;
use SwagFuzzy\Components\AlgorithmService;
use SwagFuzzy\Components\ColognePhonetic;
use SwagFuzzy\Components\ExactMatchAlgorithm;
use SwagFuzzy\Components\KeywordAlgorithms;
use SwagFuzzy\Components\Metaphone;
use SwagFuzzy\Components\SettingsService;
use SwagFuzzy\Components\SynonymService;

/**
 * Class FuzzyKeywordFinder
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class FuzzyKeywordFinder implements KeywordFinderInterface
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

    /**
     * @var AlgorithmService
     */
    private $algorithmService;

    /**
     * @var SynonymService
     */
    private $synonymService;

    /**
     * @var Metaphone
     */
    private $metaphone;

    /**
     * @var ColognePhonetic
     */
    private $colognePhonetic;

    /**
     * @var ContextServiceInterface
     */
    private $contextService;

    /**
     * initialises the private properties.
     */
    public function __construct(
        SettingsService $settingsService,
        TermHelperInterface $termHelper,
        Connection $connection,
        AlgorithmService $algorithmService,
        SynonymService $synonymService,
        ContextServiceInterface $contextService
    ) {
        $this->settingsService = $settingsService;
        $this->termHelper = $termHelper;
        $this->connection = $connection;
        $this->algorithmService = $algorithmService;
        $this->synonymService = $synonymService;
        $this->contextService = $contextService;
        $this->colognePhonetic = new ColognePhonetic();
        $this->metaphone = new Metaphone();
    }

    /**
     * returns array of keywords for the given term
     *
     * {@inheritdoc}
     */
    public function getKeywordsOfTerm($term)
    {
        $words = $this->searchWordsAndSynonymsOfTerm($term);

        $keywords = $this->searchKeywords($words);

        $matches = $this->searchMatchingKeywords($keywords);

        $matches = $this->maxMatchingKeywords($matches);

        return $matches;
    }

    /**
     * Returns words and synonyms of a search term
     *
     * @param string $term
     *
     * @return array
     */
    private function searchWordsAndSynonymsOfTerm($term)
    {
        $shopId = $this->contextService->getShopContext()->getShop()->getId();

        $searchTerms = $this->termHelper->splitTerm($term);

        $matches = [];
        foreach ($searchTerms as $searchTerm) {
            $matches = array_merge($matches, $this->synonymService->getSynonyms($searchTerm, $shopId));
            $matches[] = $searchTerm;
        }
        $matches = array_unique($matches);

        return $matches;
    }

    /**
     * returns words and synonyms of a search term
     *
     * @return array
     */
    private function searchKeywords(array $words)
    {
        $fuzzySettings = $this->settingsService->getSettings();
        $algorithm = $fuzzySettings['keyword_algorithm'];

        $keywords = [];
        foreach ($words as $word) {
            $keywords = array_merge($keywords, $this->getKeywords($word, $algorithm));
        }

        return $keywords;
    }

    /**
     * helper method to find keywords for the given term using several algorithms depending on fuzzy settings
     *
     * @param array $keywords
     *
     * @return Keyword[]
     */
    private function searchMatchingKeywords($keywords)
    {
        $fuzzySettings = $this->settingsService->getSettings();

        $matches = [];
        foreach ($keywords as $keyword) {
            $keywordID = $keyword['id'];
            $term = $keyword['term'];
            $keyword = $keyword['keyword'];

            if (strlen($term) < strlen($keyword)) {
                $term1 = $keyword;
                $term2 = $term;
            } else {
                $term2 = $keyword;
                $term1 = $term;
            }

            $relevance = 0;

            // Terms are similar
            if ($term1 === $term2) {
                $relevance = $fuzzySettings['searchExactMatchFactor'];
            // Check for sub term matching
            } elseif (strpos($term1, $term2) !== false) {
                if (strlen($term1) < 4) {
                    $relevance = $fuzzySettings['searchMatchFactor'];
                //ipod === ipods
                } elseif (strlen($term1) - strlen($term2) <= 1) {
                    $relevance = $fuzzySettings['searchExactMatchFactor'];
                //digital == digi
                } elseif ((round(strlen($term2) / strlen($term1), 2) * 100) >= $fuzzySettings['searchPartNameDistances']) {
                    $relevance = $fuzzySettings['searchPatternMatchFactor'];
                }
                //ipod = ipop
            } elseif ($fuzzySettings['exact_match_algorithm'] === ExactMatchAlgorithm::TYPE_LEVINSTEIN) {
                $relevance = $this->algorithmService->doLevenshtein($term, $keyword, $term1);
            } elseif ($fuzzySettings['exact_match_algorithm'] === ExactMatchAlgorithm::TYPE_SIMILAR_TEXT) {
                $relevance = $this->algorithmService->doSimilarText($term, $keyword, $term1);
            }

            if (!empty($relevance)) {
                $matches[] = new Keyword($keywordID, $relevance, $term, $keyword);
            }
        }

        return $matches;
    }

    /**
     * @param $searchTerm
     * @param $algorithm
     *
     * @return array
     */
    private function getKeywords($searchTerm, $algorithm)
    {
        $result = [];
        switch (true) {
            case is_numeric($searchTerm):
                $sql = $this->algorithmService->getKeywordQuery('numeric');
                $result = $this->connection->fetchAll($sql, ['term' => $searchTerm]);
                break;
            case $algorithm === KeywordAlgorithms::TYPE_SOUNDEX:
                $sql = $this->algorithmService->getKeywordQuery($algorithm);
                $result = $this->connection->fetchAll($sql, ['term' => $searchTerm]);
                break;
            case $algorithm === KeywordAlgorithms::TYPE_COLOGNE_PHONETIC:
                $sql = $this->algorithmService->getKeywordQuery($algorithm);
                $hash = $this->colognePhonetic->getPhoneticHash($searchTerm);
                $result = $this->connection->fetchAll($sql, ['term' => $searchTerm, 'phoneticHash' => $hash]);
                break;
            case $algorithm === KeywordAlgorithms::TYPE_METAPHONE:
                $sql = $this->algorithmService->getKeywordQuery($algorithm);
                $hash = $this->metaphone->getPhoneticHash($searchTerm);
                $result = $this->connection->fetchAll($sql, ['term' => $searchTerm, 'phoneticHash' => $hash]);
                break;
            default:
                break;
        }

        return $result;
    }

    /**
     * @param Keyword[] $matches
     *
     * @return Keyword[]
     */
    private function maxMatchingKeywords($matches)
    {
        $fuzzySettings = $this->settingsService->getSettings();

        $sort = array_map(function ($match) {
            /* @var Keyword $match */
            return $match->getRelevance();
        }, $matches);

        array_multisort($sort, SORT_NUMERIC, SORT_DESC, $matches);

        return array_slice($matches, 0, $fuzzySettings['maxKeywordsAndSimilarWords']);
    }
}
