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

use Enlight_Hook_HookArgs;
use SwagCustomProducts\Components\Services\DocumentValueExtenderInterface;
use SwagCustomProducts\Subscriber\Mail;
use SwagCustomProducts\tests\KernelTestCaseTrait;

class MailTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_filterMailVariables_should_modify_sOrderDetails()
    {
        $mailSubscriber = $this->getMailSubscriber();

        $eventArgs = new EventArgsMock(['sOrderDetails' => ['foo']]);

        $mailSubscriber->filterMailVariables($eventArgs);

        static::assertEquals(['sOrderDetails' => ['bar']], $eventArgs->getReturn());
    }

    /**
     * @return Mail
     */
    private function getMailSubscriber()
    {
        return new Mail(new DocumentValueExtenderMock());
    }
}

class EventArgsMock extends \Enlight_Event_EventArgs
{
    private $return;

    public function __construct(array $return)
    {
        $this->return = $return;
    }

    /**
     * @return array
     */
    public function getReturn()
    {
        return $this->return;
    }

    public function setReturn($return)
    {
        $this->return = $return;
    }
}

class DocumentValueExtenderMock implements DocumentValueExtenderInterface
{
    public function groupOptionsForMail(array $data)
    {
        return ['bar'];
    }

    public function extendWithValues(Enlight_Hook_HookArgs $args)
    {
    }

    public function groupOptionsForDocument(Enlight_Hook_HookArgs $args)
    {
    }
}
