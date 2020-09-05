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

namespace SwagBundle\Tests\Functional\Services;

use PHPUnit\Framework\TestCase;
use SwagBundle\Components\BundleComponentInterface;
use SwagBundle\Models\Price;
use SwagBundle\Services\BundleHelperService;
use SwagBundle\Tests\DatabaseTestCaseTrait;

class BundleHelperServiceTest extends TestCase
{
    use DatabaseTestCaseTrait;

    /**
     * @param string $key
     * @param string $expectedResult
     *
     * @dataProvider getProductId_test_dataProvider
     */
    public function test_getProductId($key, $expectedResult)
    {
        $bundleServiceHelper = $this->getService();
        $result = $bundleServiceHelper->getProductId($key);

        static::assertSame($expectedResult, $result);
    }

    public function getProductId_test_dataProvider()
    {
        return [
            [null, ''],
            ['', ''],
            ['group-1::1::1', '1::1'],
            ['group-1::1::2', '1::1'],
            ['group-1152::1156::24567', '1152::1156'],
            ['group-0::178::3', '0::178'],
            ['0::178::3', '0::178'],
        ];
    }

    /**
     * @param string $key
     * @param int    $expectedResult
     *
     * @dataProvider getBundleProductId_test_dataProvider
     */
    public function test_getBundleProductId($key, $expectedResult)
    {
        $bundleServiceHelper = $this->getService();
        $result = $bundleServiceHelper->getBundleProductId($key);

        static::assertSame($expectedResult, $result);
    }

    public function getBundleProductId_test_dataProvider()
    {
        return [
            [null, 0],
            ['', 0],
            ['group-1::1::1', 1],
            ['group-12::178::1', 12],
            ['group-100::178::1', 100],
            ['100::178::1', 100],
            ['666', 666],
        ];
    }

    /**
     * @param string $key
     * @param int    $expectedResult
     *
     * @dataProvider prepareArrayKey_test_dataProvider
     */
    public function test_prepareArrayKey($key, $expectedResult)
    {
        $service = $this->getService();
        $result = $service->prepareArrayKey($key);

        static::assertSame($expectedResult, $result);
    }

    public function prepareArrayKey_test_dataProvider()
    {
        return [
            [null, 0],
            ['', 0],
            ['group-12::12::1', 1],
            ['group-12::178::1', 1],
            ['group-100::178::1', 1],
            ['666', 666],
        ];
    }

    /**
     * @param string $key
     * @param bool   $expectedResult
     *
     * @dataProvider isConfigParameter_test_dataProvider
     */
    public function test_isConfigParameter($key, $expectedResult)
    {
        $service = $this->getService();
        $result = $service->isConfigParameter($key);

        static::assertSame($expectedResult, $result);
    }

    public function isConfigParameter_test_dataProvider()
    {
        return [
            [null, false],
            ['', false],
            ['group-12::12::1', true],
            ['group-12::178::1', true],
            ['group-100::178::1', true],
            ['group-100::10', false],
            ['group-100::', false],
            ['group-', false],
            ['666', false],
        ];
    }

    /**
     * @param int  $bundleProductID
     * @param bool $expectedResult
     *
     * @dataProvider isBundleProductInSelection_test_dataProvider
     */
    public function test_isBundleProductInSelection($bundleProductID, array $selection, $expectedResult)
    {
        $service = $this->getService();
        $result = $service->isBundleProductInSelection($bundleProductID, $selection);

        static::assertSame($expectedResult, $result);
    }

    public function isBundleProductInSelection_test_dataProvider()
    {
        $product1 = new \SwagBundle\Models\Article();
        $product1->setId(1);

        $product2 = new \SwagBundle\Models\Article();
        $product2->setId(2);

        $product3 = new \SwagBundle\Models\Article();
        $product3->setId(3);

        $product4 = new \SwagBundle\Models\Article();
        $product4->setId(4);

        $product5 = new \SwagBundle\Models\Article();
        $product5->setId(5);

        return [
            [null, [], false],
            [1, [], false],
            [1, [$product2, $product3], false],
            [4, [$product2, $product3], false],
            [5, [$product2, $product3], false],
            [157, [$product2, $product3], false],
            [1, [$product1, $product2, $product3], true],
            [3, [$product1, $product2, $product3, $product4], true],
            [5, [$product1, $product2, $product3, $product4, $product5], true],
            [1, [$product1], true],
            [2, [$product2], true],
            [3, [$product3], true],
            [4, [$product4], true],
            [5, [$product5], true],
        ];
    }

    /**
     * @param int    $bundleType
     * @param array  $selection
     * @param string $key
     *
     * @dataProvider canConfigBeAdded_test_dataProvider
     */
    public function test_canConfigBeAdded($bundleType, $selection, $key, $expectedResult)
    {
        $service = $this->getService();
        $result = $service->canConfigBeAdded($bundleType, $selection, $key);

        static::assertSame($expectedResult, $result);
    }

    public function canConfigBeAdded_test_dataProvider()
    {
        $product1 = new \SwagBundle\Models\Article();
        $product1->setId(1);

        $product2 = new \SwagBundle\Models\Article();
        $product2->setId(2);

        return [
            [null, [], '', true],
            [BundleComponentInterface::NORMAL_BUNDLE, [], '', true],
            [BundleComponentInterface::NORMAL_BUNDLE, [$product1], '', true],
            [BundleComponentInterface::NORMAL_BUNDLE, [$product1, $product2], '', true],
            [BundleComponentInterface::NORMAL_BUNDLE, [$product1, $product2], 'group-1::1::1', true],
            [BundleComponentInterface::SELECTABLE_BUNDLE, [$product1, $product2], 'group-1::1::1', true],
            [BundleComponentInterface::SELECTABLE_BUNDLE, [$product1, $product2], 'group-3::3::3', false],
            [BundleComponentInterface::SELECTABLE_BUNDLE, [$product1, $product2], 'group-13::178::3', false],
            [BundleComponentInterface::SELECTABLE_BUNDLE, [$product1, $product2], 'group-13::1::2', false],
            [BundleComponentInterface::SELECTABLE_BUNDLE, [$product1, $product2], 'group-111', false],
        ];
    }

    /**
     * @param bool $isTaxFree
     *
     * @dataProvider createPrice_test_dataProvider
     */
    public function test_createPrice($isTaxFree, Price $price, array $discount, array $expectedResult)
    {
        $service = $this->getService();
        $result = $service->createPrice($isTaxFree, $price, $discount);

        static::assertSame(
            $expectedResult['price'],
            $this->replaceEuroSign($result['price'])
        );

        static::assertSame(
            $expectedResult['regularPrice'],
            $this->replaceEuroSign($result['regularPrice'])
        );
    }

    public function createPrice_test_dataProvider()
    {
        $price1 = new Price();
        $price1->setNetPrice(10.0);
        $price1->setGrossPrice(12.0);

        $price2 = new Price();
        $price2->setNetPrice(100.0);
        $price2->setGrossPrice(120.0);

        return [
            [false, $price1, ['net' => 1, 'gross' => 2], ['price' => '12,00', 'regularPrice' => '14,00']],
            [false, $price2, ['net' => 10, 'gross' => 20], ['price' => '120,00', 'regularPrice' => '140,00']],
            [false, $price2, ['net' => 5, 'gross' => 10], ['price' => '120,00', 'regularPrice' => '130,00']],
            [true, $price1, ['net' => 1, 'gross' => 2], ['price' => '10,00', 'regularPrice' => '11,00']],
            [true, $price2, ['net' => 10, 'gross' => 20], ['price' => '100,00', 'regularPrice' => '110,00']],
            [true, $price2, ['net' => 5, 'gross' => 10], ['price' => '100,00', 'regularPrice' => '105,00']],
        ];
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function replaceEuroSign($string)
    {
        return str_replace(' &euro;', '', $string);
    }

    /**
     * @return BundleHelperService
     */
    private function getService()
    {
        return new BundleHelperService(Shopware()->Container()->get('currency'));
    }
}
