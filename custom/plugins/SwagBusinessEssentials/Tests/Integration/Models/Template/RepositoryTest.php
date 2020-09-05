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

namespace SwagBusinessEssentials\Tests\Integration\Models\Template;

use PHPUnit\Framework\TestCase;
use SwagBusinessEssentials\Models\Template\Repository;
use SwagBusinessEssentials\Models\Template\TplVariables;
use SwagBusinessEssentials\Tests\KernelTestCaseTrait;

class RepositoryTest extends TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var Repository
     */
    private $service;

    public function test_getVariablesQuery()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures.sql'));

        $result = $this->service->getVariablesQuery(2, 5, 'more')->getArrayResult();

        static::assertEquals([
            [
                'id' => 1002,
                'variable' => 'more3',
                'description' => 'descirption',
            ],
            [
                'id' => 1003,
                'variable' => 'more',
                'description' => 'descirption',
            ],
        ], $result);
    }

    /**
     * @before
     */
    protected function createBefore()
    {
        $this->service = self::getContainer()->get('models')->getRepository(TplVariables::class);
    }
}
