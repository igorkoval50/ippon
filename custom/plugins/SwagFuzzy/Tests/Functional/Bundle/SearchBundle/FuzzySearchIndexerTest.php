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
use SwagFuzzy\Bundle\SearchBundleDBAL\SearchTerm\FuzzySearchIndexer;
use SwagFuzzy\Components\ColognePhonetic;
use SwagFuzzy\Components\Metaphone;
use SwagFuzzy\Tests\KernelTestCaseTrait;

class FuzzySearchIndexerTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_build()
    {
        $indexer = new FuzzySearchIndexer(
            Shopware()->Container()->get('dbal_connection'),
            new ColognePhonetic(),
            new Metaphone(),
            Shopware()->Container()->get('shopware_searchdbal.search_indexer')
        );

        $this->resetKeywords();

        $indexer->build();

        $sql = 'SELECT ssk.id, ssk.keyword, ssk.cologne_phonetic, ssk.metaphone FROM s_search_keywords ssk WHERE (ssk.cologne_phonetic IS NULL) OR (ssk.metaphone IS NULL)';
        $result = Shopware()->Container()->get('dbal_connection')->fetchAll($sql);

        $this->assertEmpty($result);
    }

    private function resetKeywords()
    {
        $sql = 'UPDATE s_search_keywords SET soundex = NULL, cologne_phonetic = NULL, metaphone = NULL';
        Shopware()->Container()->get('dbal_connection')->exec($sql);
    }
}
