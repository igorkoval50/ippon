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

namespace SwagCustomProducts\tests\Functional\Controllers\Backend;

require_once __DIR__ . '/../../../../Controllers/Backend/SwagCustomProducts.php';

use Doctrine\Common\Collections\Collection;
use Enlight_Controller_Request_RequestHttp;
use Enlight_Controller_Response_ResponseHttp;
use ReflectionClass;
use SwagCustomProducts\tests\KernelTestCaseTrait;
use Symfony\Component\DependencyInjection\Container;

class SwagCustomProductsTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_searchArticleAction_should_return_all_products()
    {
        $controller = $this->getController();

        $controller->searchArticleAction();

        $view = $controller->View();

        static::assertEquals(225, $view->getAssign('total'));
    }

    public function test_searchArticleAction_should_filter_products_by_query()
    {
        $controller = $this->getController();

        $controller->Request()->setParam('query', 'Lager');
        $controller->searchArticleAction();

        $view = $controller->View();

        static::assertEquals(4, $view->getAssign('total'));
    }

    public function test_resolveExjsData_options_values_pricese_shouldBeCollections()
    {
        $controller = $this->getController();

        $reflectionMethod = (new ReflectionClass(ControllerMock::class))->getMethod('resolveExtJsData');
        $reflectionMethod->setAccessible(true);

        $data = require __DIR__ . '/_fixtures/data.php';

        $result = $reflectionMethod->invoke($controller, $data);

        foreach ($result['options'] as $option) {
            static::assertInstanceOf(Collection::class, $option['values']);
            static::assertInstanceOf(Collection::class, $option['prices']);

            foreach ($option['values'] as $value) {
                static::assertInstanceOf(Collection::class, $value['prices']);
            }
        }

        static::assertInstanceOf(Collection::class, $result['articles']);
        static::assertInstanceOf(Collection::class, $result['options']);
    }

    private function getController()
    {
        return new ControllerMock(
            new Enlight_Controller_Request_RequestHttp(),
            new Enlight_Controller_Response_ResponseHttp(),
            Shopware()->Container(),
            new \Enlight_View_Default(
                new \Enlight_Template_Manager()
            )
        );
    }
}

class ControllerMock extends \Shopware_Controllers_Backend_SwagCustomProducts
{
    public function __construct(
        \Enlight_Controller_Request_RequestHttp $request,
        \Enlight_Controller_Response_ResponseHttp $response,
        Container $container,
        \Enlight_View_Default $view
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->container = $container;
        $this->view = $view;
    }
}
