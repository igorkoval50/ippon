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
use Shopware\Tests\Functional\Traits\DatabaseTransactionBehaviour;
use SwagPromotion\Components\DataProvider\ProductDataProvider;
use SwagPromotion\Components\Promotion\CurrencyConverter;
use SwagPromotion\Components\Promotion\DiscountHandler\BasketHandler\AbsoluteBasketHandler;
use SwagPromotion\Components\Promotion\DiscountHandler\BasketHandler\PercentageBasketHandler;
use SwagPromotion\Components\Promotion\DiscountHandler\ProductHandler\AbsoluteProductHandler;
use SwagPromotion\Components\Promotion\DiscountHandler\ProductHandler\BuyXGetYFreeProductHandler;
use SwagPromotion\Components\Promotion\DiscountHandler\ProductHandler\PercentageProductHandler;
use SwagPromotion\Components\Promotion\ProductStacker\ProductStackRegistry;
use SwagPromotion\Struct\Promotion;
use SwagPromotion\Tests\Helper\CurrencyConverterHelper;

/**
 * @medium
 */
class DiscountHandlerTest extends TestCase
{
    use DatabaseTransactionBehaviour;

    protected static $ensureLoadedPlugins = [
        'SwagPromotion' => [],
    ];

    /**
     * @var \Enlight_Components_Session_Namespace
     */
    private $session;

    public function setUp(): void
    {
        $this->session = Shopware()->Session();
    }

    public function testAbsoluteDiscount()
    {
        $absolute = new AbsoluteBasketHandler(new CurrencyConverter(Shopware()->Container()->get('shopware_storefront.context_service')));
        $discount = $absolute->getDiscountCommand(null, null, new Promotion(['amount' => 3]));

        static::assertEquals(3, $discount->getAmount());
    }

    public function testBuyXGetYFreeProductDiscount()
    {
        list($basket, $products) = $this->fillBasket(['SW10009' => 8]);

        $handler = new BuyXGetYFreeProductHandler();
        $discount = $handler->getDiscountCommand(
            $basket,
            $this->splitProducts($products, 3, 1),
            new Promotion(['amount' => 1, 'maxQuantity' => 1, 'step' => 3])
        );
        $discount = $discount->getAmount();
        static::assertTrue(abs(24.99 - $discount) <= 0.009, "Discount {$discount} does not match 24.99");
    }

    public function testAbsoluteProductDiscount()
    {
        list($basket, $products) = $this->fillBasket(['SW10009' => 8]);

        $percentage = new AbsoluteProductHandler(new CurrencyConverter(Shopware()->Container()->get('shopware_storefront.context_service')), $this->session);
        $discount = $percentage->getDiscountCommand(
            $basket,
            $this->splitProducts($products, 2, 2),
            new Promotion(['amount' => 2, 'maxQuantity' => 2, 'step' => 2])
        );
        $discount = $discount->getAmount();
        static::assertTrue(abs(4 - $discount) <= 0.009, "Discount {$discount} does not match 4");
    }

    public function testPercentageDiscountProducts()
    {
        list($basket, $products) = $this->fillBasket(['SW10009' => 1]);

        $percentage = new PercentageProductHandler($this->session, Shopware()->Container()->get('shopware_storefront.context_service'));
        $discount = $percentage->getDiscountCommand(
            $basket,
            $this->splitProducts($products),
            new Promotion(['amount' => 30])
        );
        $discount = $discount->getAmount();
        static::assertTrue(abs(7.497 - $discount) <= 0.009, "Discount {$discount} does not match 7.497");
    }

    public function testPercentageDiscountProductsWithStep()
    {
        list($basket, $products) = $this->fillBasket(['SW10009' => 5]);

        $percentage = new PercentageProductHandler($this->session, Shopware()->Container()->get('shopware_storefront.context_service'));
        $discount = $percentage->getDiscountCommand(
            $basket,
            $this->splitProducts($products, 2),
            new Promotion(['amount' => 100, 'step' => 2])
        );

        $discount = $discount->getAmount();
        static::assertTrue(abs(49.98 - $discount) <= 0.009, "Discount {$discount} does not match 49.98");
    }

    public function testPercentageDiscountProductsWithMissingQuantity()
    {
        list($basket, $products) = $this->fillBasket(['SW10009' => 1]);

        $percentage = new PercentageProductHandler($this->session, Shopware()->Container()->get('shopware_storefront.context_service'));
        $discount = $percentage->getDiscountCommand(
            $basket,
            $this->splitProducts($products, 2),
            new Promotion(['amount' => 100, 'step' => 2])
        );
        $discount = $discount->getAmount();
        static::assertTrue(abs(0 - $discount) <= 0.009, "Discount {$discount} does not match 0");
    }

    public function testPercentageDiscountProductsWithMaxQuantity()
    {
        list($basket, $products) = $this->fillBasket(['SW10009' => 40]);

        $percentage = new PercentageProductHandler($this->session, Shopware()->Container()->get('shopware_storefront.context_service'));
        $discount = $percentage->getDiscountCommand(
            $basket,
            $this->splitProducts($products, 2, 4),
            new Promotion(['amount' => 100, 'step' => 2, 'maxQuantity' => 4])
        );
        $discount = $discount->getAmount();
        static::assertTrue(abs(99.96 - $discount) <= 0.009, "Discount {$discount} does not match 49.98");
    }

    public function testPercentageDiscountBasket()
    {
        list($basket, $products) = $this->fillBasket(['SW10009' => 4]);
        $percentage = new PercentageBasketHandler();
        $discount = $percentage->getDiscountCommand(
            $basket,
            $this->splitProducts($products),
            new Promotion(['amount' => 30])
        );
        $discount = $discount->getAmount();
        static::assertTrue(abs(29.988 - $discount) <= 0.009, "Discount {$discount} does not match 29.988");
    }

    public function test_getDiscountCommand_percentageWithOtherCurrencyFactor()
    {
        $sql = 'UPDATE s_core_currencies SET factor = 2.0 WHERE standard = 1;';
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        list($basket, $products) = $this->fillBasket(['SW10178' => 1]);
        $percentage = new PercentageProductHandler(
            $this->session,
            Shopware()->Container()->get('shopware_storefront.context_service')
        );

        $discount = $percentage->getDiscountCommand(
            $basket,
            $this->splitProducts($products),
            new Promotion(['amount' => 10])
        );

        $discount = $discount->getAmount();

        static::assertSame(1.995, $discount);
    }

    public function test_getDiscountCommand_absoluteWithOtherCurrencyFactor()
    {
        $currencyConverterHelper = new CurrencyConverterHelper();
        $currencyConverterHelper->setCurrency(2);

        list($basket, $products) = $this->fillBasket(['SW10178' => 1]);
        $absoluteProductHandler = new AbsoluteProductHandler(
            Shopware()->Container()->get('swag_promotion.currency_converter'),
            $this->session
        );

        $discount = $absoluteProductHandler->getDiscountCommand(
            $basket,
            $this->splitProducts($products),
            new Promotion(['amount' => 5])
        );

        // Reset currency factor
        $currencyConverterHelper->setCurrency(1);

        static::assertSame(10.0, $discount->getAmount());
    }

    private function splitProducts($products, $step = 1, $maxQuantity = 0, $stacker = 'detail')
    {
        /** @var ProductStackRegistry $registry */
        $registry = Shopware()->Container()->get('swag_promotion.stacker.product_stacker_registry');
        $stacker = $registry->getStacker($stacker);

        $stack = $stacker->getStack($products, $step, $maxQuantity);

        if ($maxQuantity !== 0) {
            static::assertEquals($maxQuantity, count($stack), "Stack does not match {$maxQuantity}");
        }

        return $stack;
    }

    /**
     * @param array $products
     *
     * @return array
     */
    private function fillBasket($products)
    {
        Shopware()->Modules()->Basket()->sDeleteBasket();
        foreach ($products as $number => $quantity) {
            Shopware()->Modules()->Basket()->sAddArticle($number, $quantity);
        }
        $basket = Shopware()->Modules()->Basket()->sGetBasket();

        $dataProvider = new ProductDataProvider(
            Shopware()->Db(),
            Shopware()->Container()->get('shopware_storefront.context_service')
        );

        $products = $dataProvider->get($products);

        return [$basket, $products];
    }
}
