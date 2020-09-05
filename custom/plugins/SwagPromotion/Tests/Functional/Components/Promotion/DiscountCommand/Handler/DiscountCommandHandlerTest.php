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

namespace SwagPromotion\Tests\Functional;

use PHPUnit\Framework\TestCase;
use SwagPromotion\Components\Promotion\DiscountCommand\CommandHandlerNotFoundException;
use SwagPromotion\Components\Promotion\DiscountCommand\Handler\DiscountCommandHandler;
use SwagPromotion\Struct\Promotion;

/**
 * @small
 */
class DiscountCommandHandlerTest extends TestCase
{
    protected static $ensureLoadedPlugins = [
        'SwagPromotion' => [],
    ];

    public function testCommandHandlerRegistry()
    {
        $registry = Shopware()->Container()->get('swag_promotion.discount_command_registry');
        $handler = $registry->get('addDiscount');

        static::assertInstanceOf(
            DiscountCommandHandler::class,
            $handler
        );
    }

    public function testCommandNotFound()
    {
        $registry = Shopware()->Container()->get('swag_promotion.discount_command_registry');

        $this->expectException(CommandHandlerNotFoundException::class);

        $registry->get('fooBar');
    }

    /**
     * @dataProvider verifyInsert_test_dataProvider
     */
    public function test_verifyInsert(float $amount, bool $expectedResult): void
    {
        $promotion = new Promotion([
            'id' => 1,
            'amount' => $amount,
            'type' => Promotion::TYPE_PRODUCT_PERCENTAGE,
        ]);

        $basket = [
            'AmountNumeric' => 100.00,
            'content' => [[
                'isFreeGoodByPromotionId' => false,
            ]],
        ];

        $registry = Shopware()->Container()->get('swag_promotion.discount_command_registry');
        $discountCommandHandler = $registry->get('addDiscount');

        $reflectionClass = new \ReflectionClass(DiscountCommandHandler::class);

        $reflectionPropertyBasketAmount = $reflectionClass->getProperty('basketAmount');
        $reflectionPropertyBasketAmount->setAccessible(true);
        $reflectionPropertyBasketAmount->setValue($discountCommandHandler, null);

        $reflectionPropertyProcessed = $reflectionClass->getProperty('processed');
        $reflectionPropertyProcessed->setAccessible(true);
        $reflectionPropertyProcessed->setValue($discountCommandHandler, []);

        $reflectionMethodVerifyInsert = $reflectionClass->getMethod('verifyInsert');
        $reflectionMethodVerifyInsert->setAccessible(true);

        $result = $reflectionMethodVerifyInsert->invokeArgs($discountCommandHandler, [$promotion, $amount, $basket]);

        static::assertSame($expectedResult, $result);
    }

    public function verifyInsert_test_dataProvider(): array
    {
        return [
            [0, true],
            [1, true],
            [12, true],
            [1.0, true],
            [99.0, true],
            [100.0, true],
            [100.01, false],
            [100.1, false],
            [101.1, false],
            [1001.1, false],
        ];
    }
}
