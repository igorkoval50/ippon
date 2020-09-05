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

namespace SwagCustomProducts\tests\Functional\Bundle\ESIndexingBundle;

use Shopware\Bundle\ESIndexingBundle\Product\ProductMapping as ShopwareProductMapping;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;
use SwagCustomProducts\Bundle\ESIndexingBundle\ProductMapping;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class ProductMappingTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_it_should_add_custom_products_property()
    {
        $mockReturn = [
            'properties' => [
                'attributes' => [
                    'properties' => [
                    ],
                ],
            ],
        ];

        $mock = $this->createMock(ShopwareProductMapping::class);
        $mock->method('get')
            ->willReturn($mockReturn);

        $service = new ProductMapping($mock);

        /** @var \Shopware\Models\Shop\Repository $shopRepository */
        $shopRepository = Shopware()->Container()->get('models')->getRepository(\Shopware\Models\Shop\Shop::class);
        $shop = Shop::createFromShopEntity($shopRepository->getActiveDefault());

        $result = $service->get($shop);

        static::assertEquals([
            'swag_custom_product' => [
                'properties' => [
                    'is_custom_product' => [
                        'type' => 'boolean',
                    ],
                ],
            ],
        ], $result['properties']['attributes']['properties']);
    }
}
