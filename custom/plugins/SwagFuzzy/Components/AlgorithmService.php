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

/**
 * Class AlgorithmService
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class AlgorithmService
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * calculates the levenshtein distance for the given terms
     *
     * @param string $term
     * @param string $keyword
     * @param string $term1
     *
     * @return bool|int
     */
    public function doLevenshtein($term, $keyword, $term1)
    {
        $settings = $this->settingsService->getSettings();
        $searchDistanceSetting = $settings['searchDistance'];

        $searchDistance = round(1 - levenshtein($term, $keyword) / strlen($term1), 2) * 100;
        if ($searchDistance >= $searchDistanceSetting) {
            return $searchDistance;
        }

        return false;
    }

    /**
     * calculates the similar text distance for the given terms
     *
     * @param string $term
     * @param string $keyword
     * @param string $term1
     *
     * @return bool|int
     */
    public function doSimilarText($term, $keyword, $term1)
    {
        $settings = $this->settingsService->getSettings();
        $searchDistanceSetting = $settings['searchDistance'];

        $searchDistance = 100 - round(1 - similar_text($term, $keyword) / strlen($term1), 2) * 100;
        if ($searchDistance >= $searchDistanceSetting) {
            return $searchDistance;
        }

        return false;
    }

    /**
     * depending on the fuzzy settings, this method returns a query for finding keywords
     *
     * @param string $algorithm
     *
     * @return string
     */
    public function getKeywordQuery($algorithm)
    {
        switch ($algorithm) {
            case KeywordAlgorithms::TYPE_SOUNDEX:
                $sql = $this->getSoundexQuery();
                break;
            case KeywordAlgorithms::TYPE_COLOGNE_PHONETIC:
                $sql = $this->getColognePhoneticQuery();
                break;
            case KeywordAlgorithms::TYPE_METAPHONE:
                $sql = $this->getMetaphoneQuery();
                break;
            default:
                $sql = $this->getNumericQuery();
        }

        return $sql;
    }

    /**
     * returns query to find keywords with soundex
     *
     * @return string
     */
    private function getSoundexQuery()
    {
        $sql = "
            SELECT `id` , `keyword`, :term AS `term`
            FROM `s_search_keywords`
            WHERE
            (
                `soundex` IS NOT NULL
                AND `soundex` LIKE CONCAT(LEFT(SOUNDEX(:term), 4), '%')
            )
                OR `keyword` LIKE CONCAT('%', :term, '%')
                OR `keyword` LIKE CONCAT(LEFT(:term, 2), '%');";

        return $sql;
    }

    /**
     * returns query to find keywords with cologne phonetic
     *
     * @return string
     */
    private function getColognePhoneticQuery()
    {
        $sql = "
            SELECT `id` , `keyword`, :term AS `term`
            FROM `s_search_keywords`
            WHERE
            (
                `cologne_phonetic` IS NOT NULL
                AND `cologne_phonetic` LIKE CONCAT(LEFT(:phoneticHash, 4), '%')
            )
                OR `keyword` LIKE CONCAT('%', :term, '%')
                OR `keyword` LIKE CONCAT(LEFT(:term, 2), '%');";

        return $sql;
    }

    /**
     * returns query to find keywords with metaphone
     *
     * @return string
     */
    private function getMetaphoneQuery()
    {
        $sql = "
            SELECT `id` , `keyword`, :term AS `term`
            FROM `s_search_keywords`
            WHERE
            (
                `metaphone` IS NOT NULL
                AND `metaphone` LIKE CONCAT(LEFT(:phoneticHash, 4), '%')
            )
                OR `keyword` LIKE CONCAT('%', :term, '%')
                OR `keyword` LIKE CONCAT(LEFT(:term, 2), '%');";

        return $sql;
    }

    /**
     * returns query to find keywords without special algorithm
     *
     * @return string
     */
    private function getNumericQuery()
    {
        $sql = "
            SELECT `id` , `keyword`, :term AS `term`
            FROM `s_search_keywords`
            WHERE keyword LIKE CONCAT('%', :term, '%')";

        return $sql;
    }
}
