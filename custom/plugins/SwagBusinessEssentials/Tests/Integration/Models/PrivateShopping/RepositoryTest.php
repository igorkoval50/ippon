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

namespace SwagBusinessEssentials\Tests\Integration\Models\PrivateShopping;

use PHPUnit\Framework\TestCase;
use SwagBusinessEssentials\Models\PrivateShopping\PrivateShopping;
use SwagBusinessEssentials\Models\PrivateShopping\Repository;
use SwagBusinessEssentials\Tests\KernelTestCaseTrait;

class RepositoryTest extends TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var Repository
     */
    private $service;

    public function test_getPrivateShoppingConfigQuery()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures.sql'));

        $result = $this->service->getPrivateShoppingConfigQuery('EK')->getArrayResult();

        static::assertEquals([
            [
                'id' => 1000,
                'customerGroup' => 'EK',
                'activateLogin' => 1,
                'redirectLogin' => '1',
                'redirectRegistration' => '',
                'whiteListedControllers' => '',
                'registerLink' => 0,
                'registerGroup' => '',
                'unlockAfterRegister' => 0,
                'templateLogin' => '',
                'templateAfterLogin' => '',
                'redirectURL' => '',
            ],
        ], $result);
    }

    public function test_getAllPrivateShoppingConfigsQuery()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures.sql'));

        $query = $this->service->getAllPrivateShoppingConfigsQuery();

        static::assertCount(2, $query->getArrayResult());
    }

    /**
     * @before
     */
    protected function createBefore()
    {
        $this->service = self::getContainer()->get('models')->getRepository(PrivateShopping::class);
    }
}
