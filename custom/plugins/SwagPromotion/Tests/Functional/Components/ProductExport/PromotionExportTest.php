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

namespace SwagPromotion\Tests\Functional\Components\ProductExport;

use PHPUnit\Framework\TestCase;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ListProductService;
use SwagPromotion\Components\Listing\ListProductDecorator;
use SwagPromotion\Components\ProductExport\PromotionExport;
use SwagPromotion\Struct\Promotion;
use SwagPromotion\Tests\DatabaseTestCaseTrait;
use SwagPromotion\Tests\Helper\PromotionFactory;

class PromotionExportTest extends TestCase
{
    use DatabaseTestCaseTrait;

    const DUMMY_CONFIG = ['languageID' => 1, 'customergroupID' => 1];

    const DUMMY_PRODUCTS = [['ordernumber' => 'SW10003', 'price' => 14.95]];

    public function test_construct()
    {
        $instance = new PromotionExport(
            Shopware()->Container()->get('swag_promotion.repository'),
            Shopware()->Container()->get('swag_promotion.promotion_product_highlighter'),
            Shopware()->Container()->get('shopware_storefront.list_product_service'),
            Shopware()->Container()->get('shopware_storefront.context_service')
        );

        static::assertInstanceOf(PromotionExport::class, $instance);
    }

    /**
     * @dataProvider handleExport_with_invalid_config_test_DataProvider
     */
    public function test_handleExport_with_invalid_config(array $config)
    {
        $service = $this->getPromotionExportService();

        $result = $service->handleExport(self::DUMMY_PRODUCTS, $config);
        static::assertCount(1, $result);
        static::assertSame(14.95, $result[0]['price'], 'The price should not have been touched at all');
    }

    /**
     * @return array
     */
    public function handleExport_with_invalid_config_test_DataProvider()
    {
        return [
            [['customergroupID' => 1]],
            [['languageID' => 1]],
        ];
    }

    /**
     * @dataProvider handleExport_with_different_promotion_types_DataProvider
     *
     * @param Promotion[] $promotions
     */
    public function test_handleExport_with_different_promotion_types($promotions = [])
    {
        $service = $this->getPromotionExportService($promotions);

        $result = $service->handleExport(self::DUMMY_PRODUCTS, self::DUMMY_CONFIG);

        static::assertCount(1, $result);
        static::assertSame(14.95, $result[0]['price'], 'The price should not have been touched at all');
    }

    /**
     * @return array
     */
    public function handleExport_with_different_promotion_types_DataProvider()
    {
        return [
            [[new Promotion(['type' => 'basket.absolute', 'active' => true])]],
            [[new Promotion(['type' => 'basket.percentage', 'active' => true])]],
            [[new Promotion(['type' => 'product.buyxgetyfree', 'active' => true])]],
            [[new Promotion(['type' => 'product.freegoods', 'active' => true])]],
            [[]],
        ];
    }

    public function test_handleExport_will_update_price()
    {
        $service = $this->getPromotionExportService([$this->getPromotion()]);

        $result = $service->handleExport(self::DUMMY_PRODUCTS, self::DUMMY_CONFIG);

        static::assertCount(1, $result);
        static::assertSame(9.95, $result[0]['price'], 'The price should be updated to 9,95€');
        static::assertSame(14.95, $result[0]['pseudoprice'], 'The pseudo price should be updated to 14,95€');
    }

    public function test_handleExport_expects_different_variant_prices()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/price_update.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $ordernumbers = $this->getFlipFlopVariantNumbers();
        $products = array_map(function ($number) {
            return ['ordernumber' => $number];
        }, $ordernumbers);

        $service = $this->getPromotionExportService([$this->getPercentageProductPromotion()]);

        $expected = [
            'SW10153.1' => 5.99,
            'SW10153.2' => 4.99,
            'SW10153.3' => 3.99,
            'SW10153.4' => 2.99,
            'SW10153.5' => 1.99,
            'SW10153.6' => 7.99,
            'SW10153.7' => 8.99,
            'SW10153.8' => 9.99,
        ];

        $result = $service->handleExport($products, self::DUMMY_CONFIG);
        $result = array_filter($result, function ($product) {
            return $product['pseudoprice'] !== 6.99;
        });

        foreach ($result as $product) {
            if (!isset($expected[$product['ordernumber']])) {
                static::fail('Expected number missing');
            }

            if ($expected[$product['ordernumber']] !== $product['pseudoprice']) {
                static::fail('Expected price error');
            }
        }

        static::assertCount(8, $result);
    }

    /**
     * @return PromotionExport
     */
    private function getPromotionExportService(array $activePromotions = [])
    {
        Shopware()->Container()->get('swag_promotion.repository')->set($activePromotions);

        $listProductService = new ListProductService(
            Shopware()->Container()->get('shopware_storefront.list_product_gateway'),
            Shopware()->Container()->get('shopware_storefront.graduated_prices_service'),
            Shopware()->Container()->get('shopware_storefront.cheapest_price_service'),
            Shopware()->Container()->get('shopware_storefront.price_calculation_service'),
            Shopware()->Container()->get('shopware_storefront.media_service'),
            Shopware()->Container()->get('shopware_storefront.marketing_service'),
            Shopware()->Container()->get('shopware_storefront.vote_service'),
            Shopware()->Container()->get('shopware_storefront.category_service'),
            Shopware()->Container()->get('config')
        );

        $listProductDecorator = new ListProductDecorator(
            'SwagPromotion',
            $listProductService,
            Shopware()->Container()->get('swag_promotion.promotion_product_highlighter'),
            Shopware()->Container()->get('shopware.plugin.cached_config_reader'),
            null,
            Shopware()->Container()->get('shopware_storefront.context_service')
        );

        $this->setPriceDisplaying($listProductDecorator);

        $promotionExport = new PromotionExport(
            Shopware()->Container()->get('swag_promotion.repository'),
            Shopware()->Container()->get('swag_promotion.promotion_product_highlighter'),
            $listProductDecorator,
            Shopware()->Container()->get('shopware_storefront.context_service')
        );

        return $promotionExport;
    }

    private function setPriceDisplaying(ListProductDecorator $listProductService)
    {
        $reflectionClass = new \ReflectionClass(ListProductDecorator::class);
        $property = $reflectionClass->getProperty('priceDisplaying');
        $property->setAccessible(true);
        $property->setValue($listProductService, 'pseudo');
    }

    /**
     * @return Promotion
     */
    private function getPromotion()
    {
        return PromotionFactory::create([
            'type' => 'product.absolute',
            'name' => 'PHPUnit',
            'number' => 'TEST1234',
            'amount' => 5,
            'active' => true,
            'applyRules' => ['and' => [
                'productCompareRule0.20067316602042906' => [
                    'detail::ordernumber',
                    '=',
                    'SW10003',
                ],
            ]],
        ]);
    }

    private function getPercentageProductPromotion()
    {
        return PromotionFactory::create([
            'type' => 'product.percentage',
            'name' => 'PHPUnit',
            'number' => 'FooBar',
            'amount' => 10,
            'active' => true,
            'applyRules' => ['and' => [
                'productCompareRule0.20067316602042906' => [
                    'detail::ordernumber',
                    'in',
                    implode(' | ', $this->getFlipFlopVariantNumbers()),
                ],
            ]],
        ]);
    }

    private function getFlipFlopVariantNumbers(): array
    {
        return [
            'SW10153.1',
            'SW10153.2',
            'SW10153.3',
            'SW10153.4',
            'SW10153.5',
            'SW10153.6',
            'SW10153.7',
            'SW10153.8',
            'SW10153.9',
            'SW10153.10',
            'SW10153.11',
            'SW10153.12',
            'SW10153.13',
            'SW10153.14',
            'SW10153.15',
            'SW10153.16',
            'SW10153.17',
            'SW10153.18',
            'SW10153.19',
            'SW10153.20',
            'SW10153.21',
        ];
    }
}
