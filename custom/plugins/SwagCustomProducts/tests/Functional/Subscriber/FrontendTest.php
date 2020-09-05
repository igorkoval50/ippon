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

use Enlight_Event_EventArgs;
use SwagCustomProducts\Subscriber\Frontend;
use SwagCustomProducts\tests\KernelTestCaseTrait;
use SwagCustomProducts\tests\ServicesHelper;
use SwagCustomProducts\tests\TestDataProvider;

/**
 * Subscriber test:
 *
 * @see \SwagCustomProducts\Subscriber\Frontend
 */
class FrontendTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var TestDataProvider
     */
    private $testDataProvider;

    public function test_onPostDispatchFrontendDetail()
    {
        $this->beforeTest();

        $request = new FrontendTestRequestMock();
        $request->setParam('isEmotionAdvancedQuickView', false);

        $view = new FrontendTestViewMock();
        $view->assign('sArticle', ['articleID' => 2, 'ordernumber' => 'SW10002.3']);

        $controller = new FrontendTestControllerMock($view, $request);
        $arguments = new FrontendTestEventArgsMock($controller);

        $subscriber = new Frontend(Shopware()->Container());
        $subscriber->onPostDispatchFrontendDetail($arguments);

        $expectedStringPosition = 0;

        $template = $view->getAssign('swagCustomProductsTemplate');

        $currentStringPosition = strpos('t_shirt_config_internal_name', $template['internal_name']);
        static::assertEquals($expectedStringPosition, $currentStringPosition);
    }

    public function testOptionPositionOrder()
    {
        $this->beforeTest();

        $request = new FrontendTestRequestMock();
        $request->setParam('isEmotionAdvancedQuickView', false);

        $view = new FrontendTestViewMock();
        $view->assign('sArticle', ['articleID' => 2, 'ordernumber' => 'SW10002.3']);

        $controller = new FrontendTestControllerMock($view, $request);
        $arguments = new FrontendTestEventArgsMock($controller);

        $subscriber = new Frontend(Shopware()->Container());
        $subscriber->onPostDispatchFrontendDetail($arguments);

        $expectedPosition = 1;

        $template = $view->getAssign('swagCustomProductsTemplate');

        static::assertEquals($expectedPosition, $template['options'][0]['position']);
    }

    public function testValuesPositionOrder()
    {
        $this->beforeTest();

        $request = new FrontendTestRequestMock();
        $request->setParam('isEmotionAdvancedQuickView', false);

        $view = new FrontendTestViewMock();
        $view->assign('sArticle', ['articleID' => 2, 'ordernumber' => 'SW10002.3']);

        $controller = new FrontendTestControllerMock($view, $request);
        $arguments = new FrontendTestEventArgsMock($controller);

        $subscriber = new Frontend(Shopware()->Container());
        $subscriber->onPostDispatchFrontendDetail($arguments);

        $template = $view->getAssign('swagCustomProductsTemplate');
        $expectedPosition = 2;

        $option = $template['options'][0];
        static::assertEquals($expectedPosition, $option['values'][1]['position']);
    }

    public function test_onPostDispatchFrontendDetail_should_assign_quick_view()
    {
        $this->beforeTest();

        $request = new FrontendTestRequestMock();
        $request->setParam('isEmotionAdvancedQuickView', true);

        $view = new FrontendTestViewMock();
        $view->assign('sArticle', ['articleID' => 2, 'ordernumber' => 'SW10002.3']);

        $controller = new FrontendTestControllerMock($view, $request);
        $subscriber = new Frontend(Shopware()->Container());
        $arguments = new FrontendTestEventArgsMock($controller);

        $subscriber->onPostDispatchFrontendDetail($arguments);

        static::assertTrue($view->getAssign('customProductsIsEmotionAdvancedQuickView'));
    }

    public function test_onPostDispatchFrontendDetail_should_return_null_inactive_template()
    {
        $this->beforeTest();

        Shopware()->Container()->get('dbal_connection')->exec(file_get_contents(__DIR__ . '/_fixtures/set_template_inactive.sql'));

        $request = new FrontendTestRequestMock();
        $request->setParam('isEmotionAdvancedQuickView', false);
        $view = new FrontendTestViewMock();
        $view->assign('sArticle', ['articleID' => 2, 'ordernumber' => 'SW10002.3']);

        $controller = new FrontendTestControllerMock($view, $request);
        $subscriber = new Frontend(Shopware()->Container());
        $arguments = new FrontendTestEventArgsMock($controller);

        static::assertNull($subscriber->onPostDispatchFrontendDetail($arguments));
    }

    private function beforeTest()
    {
        $this->registerServices();

        $this->testDataProvider = Shopware()->Container()->get('swag_custom_products.test_data_provider');
        $this->testDataProvider->setUp();
    }

    private function registerServices()
    {
        $services = new ServicesHelper(Shopware()->Container());
        $services->registerServices();
    }
}

class FrontendTestEventArgsMock extends Enlight_Event_EventArgs
{
    /**
     * @var FrontendTestControllerMock
     */
    public $subject;

    public function __construct(FrontendTestControllerMock $controllerMock)
    {
        $this->subject = $controllerMock;
    }

    public function get($string)
    {
        return $this->$string;
    }
}

class FrontendTestControllerMock extends \Shopware_Controllers_Frontend_Detail
{
    /**
     * @var FrontendTestViewMock
     */
    public $view;

    /**
     * @var FrontendTestRequestMock
     */
    public $request;

    public function __construct(FrontendTestViewMock $view, FrontendTestRequestMock $request)
    {
        $this->view = $view;
        $this->request = $request;
    }

    /**
     * @return FrontendTestViewMock
     */
    public function View()
    {
        return $this->view;
    }

    /**
     * @return FrontendTestRequestMock
     */
    public function Request()
    {
        return $this->request;
    }
}

class FrontendTestRequestMock
{
    public $params;

    public function __construct()
    {
        $this->params = [];
    }

    /**
     * @param string $key
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * @param string $key
     * @param null   $default
     */
    public function getParam($key, $default = null)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }

        return $default;
    }
}

class FrontendTestViewMock
{
    /**
     * @var array
     */
    public $assign;

    public function __construct()
    {
        $this->assign = [];
    }

    /**
     * @param string $string
     */
    public function getAssign($string)
    {
        return $this->assign[$string];
    }

    /**
     * @param string $key
     */
    public function assign($key, $value)
    {
        $this->assign[$key] = $value;
    }
}
