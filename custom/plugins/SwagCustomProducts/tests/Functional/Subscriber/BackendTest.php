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

namespace SwagCustomProducts\tests\Functional\Subscriber;

use SwagCustomProducts\Subscriber\Backend;
use SwagCustomProducts\tests\KernelTestCaseTrait;
use SwagCustomProducts\tests\ReflectionHelper;

class BackendTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    /**
     * @dataProvider assignAttributes_dataProvider
     */
    public function test_assignAttributes(array $data, $expectedResult)
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/order_details.sql');
        $this->execSql($sql);

        $subscriber = $this->getSubscriber();
        $method = ReflectionHelper::getMethod(Backend::class, 'assignAttributes');

        $result = $method->invoke($subscriber, $data);

        static::assertArraySubset($expectedResult, $result);
    }

    public function assignAttributes_dataProvider()
    {
        $expected1 = [[
            'details' => [[
                'id' => 210,
                'swag_custom_products_configuration_hash' => 'f1e465ab7738751164df054203015446',
            ], [
                'id' => 211,
                'swag_custom_products_configuration_hash' => 'f1e465ab7738751164df054203015446',
            ]],
        ]];

        $expected2 = [[
            'details' => [[
                'id' => 212,
                'swag_custom_products_configuration_hash' => 'f1e465ab7738751164df054203015446',
                'swag_custom_products_mode' => '2',
                'swag_custom_products_hide_details' => true,
            ]],
        ]];

        return [
            [[['details' => [['id' => 210, ''], ['id' => 211]]]], $expected1],
            [[['details' => [['id' => 212, '']]]], $expected2],
            [[['details' => [['id' => 1666, '']]]], []], // test for expecting "noException"
            [[['details' => []]], []], // test for expecting "noException"
        ];
    }

    /**
     * @return Backend
     */
    private function getSubscriber()
    {
        return new Backend(
            Shopware()->Container(),
            __DIR__ . '/../../../'
        );
    }
}
