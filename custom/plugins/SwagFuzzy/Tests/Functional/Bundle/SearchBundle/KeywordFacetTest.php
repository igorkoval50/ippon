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

namespace SwagFuzzy\Tests\Functional\Bundle\SearchBundle;

use PHPUnit\Framework\TestCase;
use SwagFuzzy\Bundle\SearchBundle\Facet\KeywordFacet;
use SwagFuzzy\Tests\KernelTestCaseTrait;

class KeywordFacetTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_getName()
    {
        $facet = new KeywordFacet('term');

        $this->assertSame('keyword_facet', $facet->getName());
    }

    public function test_getTerm()
    {
        $facet = new KeywordFacet('term');

        $this->assertSame('term', $facet->getTerm());
    }

    public function test_setTerm()
    {
        $facet = new KeywordFacet('term');
        $facet->setTerm('newTerm');

        $this->assertSame('newTerm', $facet->getTerm());
    }

    public function test_set_getKeywords()
    {
        $keyWords = ['a', 'b', 'c'];
        $facet = new KeywordFacet('term');
        $facet->setKeywords($keyWords);

        $this->assertArraySubset($keyWords, $facet->getKeywords());
    }

    public function test_set_getSimilarResults()
    {
        $similarResults = ['res1', 'res2'];
        $facet = new KeywordFacet('term');
        $facet->setSimilarResults($similarResults);

        $this->assertArraySubset($similarResults, $facet->getSimilarResults());
    }
}
