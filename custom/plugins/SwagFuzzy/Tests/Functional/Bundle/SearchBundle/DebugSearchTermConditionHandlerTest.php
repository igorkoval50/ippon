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
use Shopware\Bundle\SearchBundle\Condition\SearchTermCondition;
use SwagFuzzy\Bundle\SearchBundle\Condition\DebugSearchTermCondition;
use SwagFuzzy\Bundle\SearchBundleDBAL\ConditionHandler\DebugSearchTermConditionHandler;
use SwagFuzzy\Tests\KernelTestCaseTrait;

class DebugSearchTermConditionHandlerTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_supportsCondition_should_be_false()
    {
        $queryBuilder = Shopware()->Container()->get('shopware_searchdbal.search_query_builder_dbal');
        $condition = new SearchTermCondition('test term');
        $conditionHandler = new DebugSearchTermConditionHandler($queryBuilder);

        $result = $conditionHandler->supportsCondition($condition);

        $this->assertFalse($result);
    }

    public function test_supportsCondition_should_be_true()
    {
        $queryBuilder = Shopware()->Container()->get('shopware_searchdbal.search_query_builder_dbal');
        $condition = new DebugSearchTermCondition('test term');
        $conditionHandler = new DebugSearchTermConditionHandler($queryBuilder);

        $result = $conditionHandler->supportsCondition($condition);

        $this->assertTrue($result);
    }
}
