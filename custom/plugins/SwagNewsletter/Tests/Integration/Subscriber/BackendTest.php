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

namespace SwagNewsletter\Tests\Integration\Subscriber;

use PHPUnit\Framework\TestCase;
use SwagNewsletter\Subscriber\Backend;
use SwagNewsletter\Tests\KernelTestCaseTrait;
use SwagNewsletter\Tests\Mocks\ViewDummy;

class BackendTest extends TestCase
{
    use KernelTestCaseTrait;
    use SubscriberTestTrait;

    const EVENT_COUNT = 2;

    public function test_construct()
    {
        $subscriber = new Backend(Shopware()->Container()->getParameter('swag_newsletter.plugin_dir'));
        $this->assertInstanceOf(Backend::class, $subscriber);
    }

    public function test_getSubscribedEvents()
    {
        $this->assertCount(self::EVENT_COUNT, Backend::getSubscribedEvents());
    }

    public function test_onPostDispatch_index_action()
    {
        $args = $this->ActionEventArgs();
        $args->getRequest()->setActionName('index');

        /** @var ViewDummy $view */
        $view = $args->getSubject()->View();

        $subscriber = new Backend(Shopware()->Container()->getParameter('swag_newsletter.plugin_dir'));
        $subscriber->onPostDispatch($args);

        $this->assertCount(1, $view->getExtends());
    }

    public function test_onPostDispatch_load_action()
    {
        $args = $this->ActionEventArgs();
        $args->getRequest()->setActionName('load');

        /** @var ViewDummy $view */
        $view = $args->getSubject()->View();

        $subscriber = new Backend(Shopware()->Container()->getParameter('swag_newsletter.plugin_dir'));
        $subscriber->onPostDispatch($args);

        $this->assertCount(9, $view->getExtends());
    }

    public function test_onPostDispatchBackendIndex_with_invalid_action_name()
    {
        $args = $this->ActionEventArgs();
        $args->getRequest()->setActionName('invalid');

        /** @var ViewDummy $view */
        $view = $args->getSubject()->View();

        $subscriber = new Backend(Shopware()->Container()->getParameter('swag_newsletter.plugin_dir'));
        $subscriber->onPostDispatchBackendIndex($args);

        $this->assertCount(0, $view->getExtends());
    }

    public function test_onPostDispatchBackendIndex()
    {
        $args = $this->ActionEventArgs();
        $args->getRequest()->setActionName('index');

        /** @var ViewDummy $view */
        $view = $args->getSubject()->View();

        $subscriber = new Backend(Shopware()->Container()->getParameter('swag_newsletter.plugin_dir'));
        $subscriber->onPostDispatchBackendIndex($args);

        $this->assertCount(1, $view->getExtends());
    }
}
