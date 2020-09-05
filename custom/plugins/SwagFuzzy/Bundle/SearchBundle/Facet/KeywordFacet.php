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

namespace SwagFuzzy\Bundle\SearchBundle\Facet;

use Shopware\Bundle\SearchBundle\FacetInterface;
use Shopware\Bundle\SearchBundleDBAL\SearchTerm\Keyword;

/**
 * Class KeywordFacet
 *
 * @category  Shopware
 *
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class KeywordFacet implements FacetInterface
{
    /**
     * @var string
     */
    private $term;

    /**
     * @var Keyword[]
     */
    private $keywords;

    /**
     * @var array
     */
    private $similarResults;

    /**
     * @param string $term
     */
    public function __construct($term)
    {
        $this->term = $term;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'keyword_facet';
    }

    /**
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * @param string $term
     */
    public function setTerm($term)
    {
        $this->term = $term;
    }

    /**
     * @return Keyword[]
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param Keyword[] $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @return array
     */
    public function getSimilarResults()
    {
        return $this->similarResults;
    }

    /**
     * @param array $similarResults
     */
    public function setSimilarResults($similarResults)
    {
        $this->similarResults = $similarResults;
    }
}
