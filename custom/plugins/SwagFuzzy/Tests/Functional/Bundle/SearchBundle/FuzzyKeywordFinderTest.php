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
use Shopware\Bundle\SearchBundleDBAL\SearchTerm\Keyword;
use SwagFuzzy\Tests\KernelTestCaseTrait;

class FuzzyKeywordFinderTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_getKeywordsOfTerm()
    {
        $finder = Shopware()->Container()->get('shopware_searchdbal.keyword_finder_dbal');

        $result = $finder->getKeywordsOfTerm('bad');

        /** @var Keyword $keyword */
        foreach ($result as $keyword) {
            $this->assertInstanceOf(Keyword::class, $keyword);
            $this->assertSame('bad', $keyword->getTerm());
        }
    }

    private function installTestKeywords()
    {
        $sql = 'INSERT INTO s_search_keywords (id, keyword) VALUES (500102, "batman"), (600102, "Superman"), 
                  (700102, "Spongebob"), (800102, "bad");';

        Shopware()->Container()->get('dbal_connection')->exec($sql);
    }
}
