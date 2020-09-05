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

namespace SwagPromotion\Tests\Functional\Components\Promotion\DiscountCommand\Handler;

use PHPUnit\Framework\TestCase;
use SwagPromotion\Components\Promotion\DiscountCommand\Command\FreeGoodsBundleCommand;
use SwagPromotion\Components\Promotion\DiscountCommand\Handler\FreeGoodsBundleCommandHandler;
use SwagPromotion\Struct\Promotion;
use SwagPromotion\Tests\DatabaseTestCaseTrait;
use SwagPromotion\Tests\Helper\PromotionFactory;

class FreeGoodBundleCommandHandlerTest extends TestCase
{
    use DatabaseTestCaseTrait;

    public function setUp(): void
    {
        Shopware()->Front()->setRequest(new \Enlight_Controller_Request_RequestTestCase());
        Shopware()->Session()->offsetSet('sessionId', 'sessionId');
        Shopware()->Container()->get('dbal_connection')->exec(
            "INSERT INTO `s_order_basket` (`id`, `sessionID`, `userID`, `articlename`, `articleID`, `ordernumber`, `shippingfree`, `quantity`, `price`, `netprice`, `tax_rate`, `datum`, `modus`, `esdarticle`, `partnerID`, `lastviewport`, `useragent`, `config`, `currencyFactor`) VALUES
                        (67500158, 'sessionId', 0, 'Cigar Special 40%', 6, 'SW10006', 0, 2, 35.95, 30.210084033613, 19, '2017-03-27 14:51:51', 0, 0, '', 'detail', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36', '', 1)"
        );
    }

    public function test_handle_shouldReturn_false_noDiscountAmount(): void
    {
        $command = new FreeGoodsBundleCommand(0.0, 0);
        $promotion = PromotionFactory::create(
            [
                'number' => 'freeGoodsBundle1',
                'type' => Promotion::TYPE_PRODUCT_FREEGOODSBUNDLE,
                'freeGoods' => [6], // 6 = cigar special
            ]
        );

        $result = $this->getFreeGoodsBundleCommandHandler()->handle($command, $promotion, [], []);

        static::assertFalse($result);
    }

    public function test_handle_shouldReturn_false_matchCountIsToHigh(): void
    {
        $command = new FreeGoodsBundleCommand(10, 1);
        $promotion = PromotionFactory::create(
            [
                'number' => 'freeGoodsBundle2',
                'type' => Promotion::TYPE_PRODUCT_FREEGOODSBUNDLE,
                'freeGoods' => [6], // 6 = cigar special
            ]
        );

        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        $matchingProducts = [
            ['quantity' => 5],
        ];

        $result = $this->getFreeGoodsBundleCommandHandler()->handle($command, $promotion, $basket, $matchingProducts);

        static::assertFalse($result);
    }

    public function test_handle_shouldReturn_true(): void
    {
        $command = new FreeGoodsBundleCommand(10, 1);
        $promotion = PromotionFactory::create(
            [
                'number' => 'freeGoodsBundle3',
                'type' => Promotion::TYPE_PRODUCT_FREEGOODSBUNDLE,
                'freeGoods' => [6], // 6 = cigar special
            ]
        );

        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        $matchingProducts = [
            ['quantity' => 1],
        ];

        $result = $this->getFreeGoodsBundleCommandHandler()->handle($command, $promotion, $basket, $matchingProducts);

        static::assertTrue($result);
    }

    private function getFreeGoodsBundleCommandHandler(): FreeGoodsBundleCommandHandler
    {
        return Shopware()->Container()->get('swag_promotion.free_goods_bundle_command_handler');
    }
}
