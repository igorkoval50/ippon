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

namespace SwagCustomProducts\tests\Functional\Bundle\StoreFrontBundle;

use Shopware\Bundle\StoreFrontBundle\Struct\ListProduct;
use SwagCustomProducts\Bundle\StoreFrontBundle\ListProductService;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class ListProductServiceTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_getList()
    {
        $service = $this->getService();
        $this->installCustomProduct();

        $result = $service->getList(['SW10178', 'SW10172', 'SW10173'], $this->getContext());

        static::assertCount(3, $result);
        static::assertInstanceOf(ListProduct::class, $result['SW10178']);
    }

    public function test_getList_has_no_listing_buyButton_feature()
    {
        $service = $this->getService();
        $this->installCustomProduct();

        $result = $service->getList([], $this->getContext());

        static::assertEmpty($result);
    }

    public function test_get()
    {
        $service = $this->getService();
        $this->installCustomProduct();

        $result = $service->get('SW10178', $this->getContext());

        static::assertInstanceOf(ListProduct::class, $result);
    }

    private function getService()
    {
        return new ListProductService(
            Shopware()->Container()->get('shopware_storefront.list_product_service'),
            Shopware()->Container()->get('dbal_connection')
        );
    }

    private function getContext()
    {
        return Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1);
    }

    private function installCustomProduct()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/custom_product.sql');
        $this->execSql($sql);
    }
}
