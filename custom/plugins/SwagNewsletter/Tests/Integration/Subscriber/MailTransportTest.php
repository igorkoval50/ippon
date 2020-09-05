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
use SwagNewsletter\Subscriber\MailTransport;
use SwagNewsletter\Tests\KernelTestCaseTrait;

class MailTransportTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_beforeMailAction_should_overwrite_transport()
    {
        $mailTransport = $this->getMailTransport();

        self::getContainer()->set('mailtransport_factory', new MailTransportFactoryMock());

        self::getContainer()->get('mail')->setDefaultTransport(new \Zend_Mail_Transport_Sendmail());
        self::assertInstanceOf(\Zend_Mail_Transport_Sendmail::class, self::getContainer()->get('mail')->getDefaultTransport());
        $mailTransport->beforeMailAction();

        self::assertInstanceOf(\Zend_Mail_Transport_File::class, self::getContainer()->get('mail')->getDefaultTransport());
    }

    /**
     * @return MailTransport
     */
    private function getMailTransport()
    {
        return new MailTransport(
            self::getContainer()->get('mail'),
            self::getContainer()->get('swag_newsletter.dependendency_provider'),
            new MailTransportFactoryMock(),
            self::getContainer()->get('Loader'),
            self::getContainer()->get('config')
        );
    }
}

class MailTransportFactoryMock extends \Shopware\Components\DependencyInjection\Bridge\MailTransport
{
    public function factory(\Enlight_Loader $loader = null, \Shopware_Components_Config $config = null, array $options = [])
    {
        return new \Zend_Mail_Transport_File();
    }
}
