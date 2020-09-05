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

namespace SwagBundle\Tests\Functional\Controllers\Widgets;

require_once __DIR__ . '/../../../../Controllers/Widgets/Bundle.php';

use SwagBundle\Models\Bundle;
use SwagBundle\Tests\DatabaseTestCaseTrait;
use SwagBundle\Tests\Functional\BundleControllerTestCase;
use SwagBundle\Tests\Functional\Controllers\_fixtures\Test_GetPriceData;
use SwagBundle\Tests\Functional\Mocks\BundleWidgetsControllerMock;
use SwagBundle\Tests\ReflectionHelper;

class BundleTest extends BundleControllerTestCase
{
    use DatabaseTestCaseTrait;

    public function test_addBundleToBasketAction_should_return_no_bundle_id()
    {
        $view = new \Enlight_View_Default(
            new \Enlight_Template_Manager()
        );

        $this->Request()->setParam('bundleId', 0);

        $bundleCtl = new BundleWidgetsControllerMock(
            $this->Request(),
            $this->Response(),
            Shopware()->Container(),
            $view
        );

        $return = $bundleCtl->addBundleToBasketAction();

        static::assertNull($return);
    }

    public function test_addBundleToBasketAction_should_redirect_to_checkout_cart()
    {
        $view = new \Enlight_View_Default(
            new \Enlight_Template_Manager()
        );

        $bundleId = 16020;

        $this->Request()->setParam('bundleId', $bundleId);

        Shopware()->Container()->get('dbal_connection')->executeQuery(
            file_get_contents(__DIR__ . '/../_fixtures/default_bundle.sql'),
            [':bundleId' => $bundleId]
        );

        $bundleCtl = new BundleWidgetsControllerMock(
            $this->Request(),
            $this->Response(),
            Shopware()->Container(),
            $view
        );

        $bundleCtl->addBundleToBasketAction();
        $header = $this->getHeader($this->Response()->getHeaders());
        $strPosRedirect = strpos($header['value'], 'checkout/cart');

        static::assertNotFalse($strPosRedirect);
        static::assertTrue($strPosRedirect > 0);
    }

    public function test_addBundleToBasketAction_should_redirect_to_product_error()
    {
        $view = new \Enlight_View_Default(
            new \Enlight_Template_Manager()
        );

        $bundleId = 16020;

        $this->Request()->setParam('bundleId', $bundleId);

        Shopware()->Container()->get('dbal_connection')->executeQuery(
            file_get_contents(__DIR__ . '/../_fixtures/bundle_without_prices.sql'),
            [':bundleId' => $bundleId]
        );

        $bundleCtl = new BundleWidgetsControllerMock(
            $this->Request(),
            $this->Response(),
            Shopware()->Container(),
            $view
        );

        $bundleCtl->addBundleToBasketAction();
        $header = $this->getHeader($this->Response()->getHeaders());
        $strPosRedirect = strpos($header['value'], 'detail/index');

        static::assertNotFalse($strPosRedirect);
        static::assertTrue($strPosRedirect > 0);
    }

    public function test_addBundleToBasketAction_should_redirect_to_product_error_because_bundle_does_not_exist()
    {
        $view = new \Enlight_View_Default(
            new \Enlight_Template_Manager()
        );

        $this->Request()->setParam('bundleId', 9999);

        $bundleCtl = new BundleWidgetsControllerMock(
            $this->Request(),
            $this->Response(),
            Shopware()->Container(),
            $view
        );

        $bundleCtl->addBundleToBasketAction();
        $header = $this->getHeader($this->Response()->getHeaders());
        $strPosRedirect = strpos($header['value'], 'detail/index');

        static::assertNotFalse($strPosRedirect);
        static::assertTrue($strPosRedirect > 0);
    }

    public function test_addBundleToBasketAction_should_redirect_to_product_error_because_bundle_is_inactive()
    {
        $view = new \Enlight_View_Default(
            new \Enlight_Template_Manager()
        );

        $bundleId = 16021;

        $this->Request()->setParam('bundleId', $bundleId);

        Shopware()->Container()->get('dbal_connection')->executeQuery(
            file_get_contents(__DIR__ . '/../_fixtures/bundle_inactive.sql'),
            [':bundleId' => $bundleId]
        );

        $bundleCtl = new BundleWidgetsControllerMock(
            $this->Request(),
            $this->Response(),
            Shopware()->Container(),
            $view
        );

        $bundleCtl->addBundleToBasketAction();
        $header = $this->getHeader($this->Response()->getHeaders());
        $strPosRedirect = strpos($header['value'], 'detail/index');

        static::assertNotFalse($strPosRedirect);
        static::assertTrue($strPosRedirect > 0);
    }

    /**
     * @dataProvider getPrice_test_dataProvider
     */
    public function test_getPrices(array $params, array $expected)
    {
        $view = new \Enlight_View_Default(
            new \Enlight_Template_Manager()
        );

        $bundleId = 2;

        $this->Request()->setParam('bundleId', $bundleId);

        foreach ($params as $key => $param) {
            $this->Request()->setParam($key, $param);
        }

        $bundleCtl = new BundleWidgetsControllerMock(
            $this->Request(),
            $this->Response(),
            Shopware()->Container(),
            $view
        );

        Shopware()->Container()->get('dbal_connection')->exec(
            file_get_contents(__DIR__ . '/../_fixtures/bundle_test_getPricesData.sql')
        );

        /** @var Bundle $bundle */
        $bundle = Shopware()->Container()->get('models')->find(Bundle::class, $bundleId);
        $selection = ReflectionHelper::getMethod(BundleWidgetsControllerMock::class, 'getSelection')->invoke($bundleCtl, $bundle);
        $bundle->setArticles($selection);
        $configuration = ReflectionHelper::getMethod(BundleWidgetsControllerMock::class, 'getBundleConfigurationFromRequest')->invokeArgs($bundleCtl, [$bundle->getType(), $selection]);
        $bundle = Shopware()->Container()->get('swag_bundle.full_bundle_service')->getCalculatedBundle($bundle, '', false, null, $configuration, $selection);

        if (!$bundle instanceof Bundle) {
            $message = sprintf('Expected instance of %s got %s.', Bundle::class, get_class($bundle));
            static::fail($message);
        }

        $result = ReflectionHelper::getMethod(BundleWidgetsControllerMock::class, 'getPrices')->invoke($bundleCtl, $bundle);

        static::assertSame($expected, $result);
    }

    public function getPrice_test_dataProvider(): array
    {
        $data = new Test_GetPriceData();

        return [
            $data->getSet1(),
            $data->getSet2(),
            $data->getSet3(),
        ];
    }

    private function getHeader(array $headers): array
    {
        foreach ($headers as $header) {
            if ($header['name'] === 'location') {
                return $header;
            }
        }

        return [];
    }
}
