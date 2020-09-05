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
use SwagFuzzy\Subscriber\Frontend;
use SwagFuzzy\Tests\KernelTestCaseTrait;
use SwagFuzzy\Tests\Mocks\EventArgsMock;
use SwagFuzzy\Tests\Mocks\RequestMock;
use SwagFuzzy\Tests\Mocks\SubjectMock;
use SwagFuzzy\Tests\Mocks\ViewMock;

class FrontendTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_onFrontendSearch()
    {
        $subscriber = $this->getSubscriber();
        $args = $this->getArguments();
        $args->request->params['sSearch'] = 'ibiza';

        $subscriber->onFrontendSearch($args);

        $view = $args->subject->view;

        $this->assertArrayHasKey('swagFuzzySynonymGroups', $view->assign);
        $this->assertArrayHasKey('hasEmotion', $view->assign);
    }

    private function getArguments($actionName = 'index')
    {
        $view = new ViewMock();
        $request = new RequestMock($actionName);
        $subject = new SubjectMock($view, $request);

        return new EventArgsMock($subject, $request);
    }

    /**
     * @return Frontend
     */
    private function getSubscriber()
    {
        return new Frontend();
    }
}
