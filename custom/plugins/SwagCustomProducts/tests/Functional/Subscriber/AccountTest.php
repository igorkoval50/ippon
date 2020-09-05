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

use SwagCustomProducts\Subscriber\Account;
use SwagCustomProducts\tests\KernelTestCaseTrait;
use SwagCustomProducts\tests\ServicesHelper;

class AccountTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_afterGetOpenOrderData_should_disable_buy_button()
    {
        $this->execSql(
            file_get_contents(__DIR__ . '/_fixtures/basic_setup_required_options.sql')
        );

        $accountSubscriber = $this->getAccountSubscriber();

        $hookArgs = new \Enlight_Hook_HookArgs($this, '');
        $hookArgs->setReturn([
            'orderData' => [
                [
                    'details' => [[
                        'articleID' => 178,
                        'articleordernumber' => 'SW10178',
                    ]],
                ],
                [
                    'details' => [[
                        'articleID' => 3,
                        'articleordernumber' => 'SW10003',
                    ]],
                ],
            ],
        ]);

        $sql = 'UPDATE s_plugin_custom_products_option SET required = 1 WHERE id = 19';
        $this->execSql($sql);

        $products = $accountSubscriber->afterGetOpenOrderData($hookArgs);

        static::assertArrayHasKey('activeBuyButton', $products['orderData'][0]);
        static::assertEquals(0, $products['orderData'][0]['activeBuyButton']);
    }

    public function test_shouldHideButton_templateIsNull()
    {
        $accountSubscriber = $this->getAccountSubscriber();

        $reflectionMethod = (new \ReflectionClass(Account::class))->getMethod('shouldHideButton');
        $reflectionMethod->setAccessible(true);

        $product = ['articleordernumber' => 'SW10178', 'articleID' => 178];

        $result = $reflectionMethod->invoke($accountSubscriber, $product);

        static::assertFalse($result);
    }

    private function getAccountSubscriber()
    {
        $serviceHelper = new ServicesHelper(Shopware()->Container());
        $serviceHelper->registerServices();

        return new Account(Shopware()->Container());
    }
}
