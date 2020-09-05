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

use Enlight_Template_Manager;
use SwagNewsletter\Tests\Mocks\ControllerDummy;
use SwagNewsletter\Tests\Mocks\ViewDummy;

trait SubscriberTestTrait
{
    /**
     * @var \Enlight_Event_EventArgs
     */
    private $eventArgs;

    /**
     * @var \Enlight_Controller_ActionEventArgs
     */
    private $controllerArgs;

    /**
     * @return \Enlight_Event_EventArgs
     */
    public function EventArgs()
    {
        if (!$this->eventArgs) {
            $this->eventArgs = new \Enlight_Event_EventArgs();
        }

        return $this->eventArgs;
    }

    /**
     * @return \Enlight_Controller_ActionEventArgs
     */
    public function ActionEventArgs()
    {
        if (!$this->controllerArgs) {
            $this->controllerArgs = new \Enlight_Controller_ActionEventArgs([
                'subject' => $this->getDummySubject(),
                'request' => $this->getDummyRequest(),
            ]);
        }

        return $this->controllerArgs;
    }

    /**
     * @return ControllerDummy
     */
    private function getDummySubject()
    {
        return new ControllerDummy(
            new \Enlight_Controller_Request_RequestTestCase(),
            new ViewDummy(new Enlight_Template_Manager()));
    }

    /**
     * @return \Enlight_Controller_Request_RequestTestCase
     */
    private function getDummyRequest()
    {
        return new \Enlight_Controller_Request_RequestTestCase();
    }
}
