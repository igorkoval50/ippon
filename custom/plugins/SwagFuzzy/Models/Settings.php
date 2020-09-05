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

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * Shopware SwagFuzzy Plugin - Settings Model
 *
 * @category Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 *
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_swag_fuzzy_settings")
 */
class Settings extends ModelEntity
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer");
     */
    private $shopId;

    /**
     * @var
     *
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Shop\Shop")
     * @ORM\JoinColumn(name="shopId", referencedColumnName="id")
     */
    private $shop;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="keyword_algorithm")
     */
    private $keywordAlgorithm;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="exact_match_algorithm")
     */
    private $exactMatchAlgorithm;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $searchDistance;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $searchExactMatchFactor;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $searchMatchFactor;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $searchMinDistancesTop;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $searchPartNameDistances;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $searchPatternMatchFactor;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $maxKeywordsAndSimilarWords;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $topSellerRelevance;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $newArticleRelevance;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @param $shop
     *
     * @return $this
     */
    public function setShop($shop)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * @return int
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @param int $shopId
     *
     * @return $this
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeyWordAlgorithm()
    {
        return $this->keywordAlgorithm;
    }

    /**
     * @param string $keyWordAlgorithm
     *
     * @return $this
     */
    public function setKeyWordAlgorithm($keyWordAlgorithm)
    {
        $this->keywordAlgorithm = (string) $keyWordAlgorithm;

        return $this;
    }

    /**
     * @return string
     */
    public function getExactMatchAlgorithm()
    {
        return $this->exactMatchAlgorithm;
    }

    /**
     * @param string $exactMatchAlgorithm
     *
     * @return $this
     */
    public function setExactMatchAlgorithm($exactMatchAlgorithm)
    {
        $this->exactMatchAlgorithm = (string) $exactMatchAlgorithm;

        return $this;
    }

    /**
     * @return int
     */
    public function getSearchDistance()
    {
        return $this->searchDistance;
    }

    /**
     * @param int $searchDistance
     *
     * @return $this
     */
    public function setSearchDistance($searchDistance)
    {
        $this->searchDistance = $searchDistance;

        return $this;
    }

    /**
     * @return int
     */
    public function getSearchExactMatchFactor()
    {
        return $this->searchExactMatchFactor;
    }

    /**
     * @param int $searchExactMatchFactor
     *
     * @return $this
     */
    public function setSearchExactMatchFactor($searchExactMatchFactor)
    {
        $this->searchExactMatchFactor = $searchExactMatchFactor;

        return $this;
    }

    /**
     * @return int
     */
    public function getSearchMatchFactor()
    {
        return $this->searchMatchFactor;
    }

    /**
     * @param int $searchMatchFactor
     *
     * @return $this
     */
    public function setSearchMatchFactor($searchMatchFactor)
    {
        $this->searchMatchFactor = $searchMatchFactor;

        return $this;
    }

    /**
     * @return int
     */
    public function getSearchMinDistancesTop()
    {
        return $this->searchMinDistancesTop;
    }

    /**
     * @param int $searchMinDistancesTop
     *
     * @return $this
     */
    public function setSearchMinDistancesTop($searchMinDistancesTop)
    {
        $this->searchMinDistancesTop = $searchMinDistancesTop;

        return $this;
    }

    /**
     * @return int
     */
    public function getSearchPartNameDistances()
    {
        return $this->searchPartNameDistances;
    }

    /**
     * @param int $searchPartNameDistances
     *
     * @return $this
     */
    public function setSearchPartNameDistances($searchPartNameDistances)
    {
        $this->searchPartNameDistances = $searchPartNameDistances;

        return $this;
    }

    /**
     * @return int
     */
    public function getSearchPatternMatchFactor()
    {
        return $this->searchPatternMatchFactor;
    }

    /**
     * @param int $searchPatternMatchFactor
     *
     * @return $this
     */
    public function setSearchPatternMatchFactor($searchPatternMatchFactor)
    {
        $this->searchPatternMatchFactor = $searchPatternMatchFactor;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxKeywordsAndSimilarWords()
    {
        return $this->maxKeywordsAndSimilarWords;
    }

    /**
     * @param int $maxKeywordsAndSimilarWords
     *
     * @return $this
     */
    public function setMaxKeywordsAndSimilarWords($maxKeywordsAndSimilarWords)
    {
        $this->maxKeywordsAndSimilarWords = $maxKeywordsAndSimilarWords;

        return $this;
    }

    /**
     * @return int
     */
    public function getTopSellerRelevance()
    {
        return $this->topSellerRelevance;
    }

    /**
     * @param int $topSellerRelevance
     *
     * @return $this
     */
    public function setTopSellerRelevance($topSellerRelevance)
    {
        $this->topSellerRelevance = $topSellerRelevance;

        return $this;
    }

    /**
     * @return int
     */
    public function getNewArticleRelevance()
    {
        return $this->newArticleRelevance;
    }

    /**
     * @param int $newArticleRelevance
     *
     * @return $this
     */
    public function setNewArticleRelevance($newArticleRelevance)
    {
        $this->newArticleRelevance = $newArticleRelevance;

        return $this;
    }
}
