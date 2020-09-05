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

namespace SwagFuzzy\Tests\Functional\Subscriber;

use PHPUnit\Framework\TestCase;
use SwagFuzzy\Subscriber\Backend;
use SwagFuzzy\Tests\KernelTestCaseTrait;
use SwagFuzzy\Tests\Mocks\EventArgsMock;
use SwagFuzzy\Tests\Mocks\FrontMock;
use SwagFuzzy\Tests\Mocks\RequestMock;
use SwagFuzzy\Tests\Mocks\SubjectMock;
use SwagFuzzy\Tests\Mocks\ViewMock;

class BackendTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_onPostDispatchIndex()
    {
        $subscriber = $this->getSubscriber();
        $args = $this->getArguments();

        $subscriber->onPostDispatchIndex($args);

        $view = $args->subject->view;

        $this->assertStringEndsWith('backend/swag_fuzzy/menu_entry.tpl', $view->templates[0]);
    }

    public function test_onPostDispatchAnalytics_index()
    {
        $subscriber = $this->getSubscriber();
        $args = $this->getArguments();

        $subscriber->onPostDispatchAnalytics($args);

        $view = $args->subject->view;

        $this->assertStringEndsWith('backend/analytics/fuzzy_analytics.js', $view->templates[0]);
    }

    public function test_onPostDispatchAnalytics_load()
    {
        $subscriber = $this->getSubscriber();
        $args = $this->getArguments('load');

        $subscriber->onPostDispatchAnalytics($args);

        $view = $args->subject->view;

        $this->assertStringEndsWith('backend/analytics/store/fuzzy/navigation.js', $view->templates[0]);
        $this->assertStringEndsWith('backend/analytics/view/table/fuzzy/search.js', $view->templates[1]);
        $this->assertStringEndsWith('backend/analytics/view/main/fuzzy/toolbar.js', $view->templates[2]);
        $this->assertStringEndsWith('backend/analytics/controller/fuzzy/main.js', $view->templates[3]);
    }

    public function test_onPostDispatchConfig()
    {
        $subscriber = $this->getSubscriber();
        $args = $this->getArguments('load');

        $subscriber->onPostDispatchConfig($args);

        $view = $args->subject->view;

        $this->assertStringEndsWith('backend/config/view/form/fuzzy/search.js', $view->templates[0]);
    }

    public function test_onPreDispatchProductStream_will_return_with_wrong_action_name()
    {
        $args = $this->getArguments('invalid');
        $subscriber = $this->getSubscriber();

        Shopware()->Container()->reset('shop');

        $subscriber->onPreDispatchProductStream($args);

        $this->assertFalse(Shopware()->Container()->has('shop'));
    }

    public function test_onPreDispatchProductStream_will_register_shop_resource()
    {
        $args = $this->getArguments('loadPreview');
        $args->getRequest()->setParam('shopId', 1);
        $subscriber = $this->getSubscriber();

        Shopware()->Container()->reset('shop');

        $subscriber->onPreDispatchProductStream($args);

        $this->assertTrue(Shopware()->Container()->has('shop'));
    }

    private function getSubscriber()
    {
        return new Backend(
            '',
            new FrontMock(
                new \Enlight_Event_EventManager()
            ),
            Shopware()->Container()->get('models'),
            Shopware()->Container()->get('shopware.components.shop_registration_service')
        );
    }

    private function getArguments($actionName = 'index')
    {
        $view = new ViewMock();
        $request = new RequestMock($actionName);
        $subject = new SubjectMock($view, $request);

        return new EventArgsMock($subject, $request);
    }
}
