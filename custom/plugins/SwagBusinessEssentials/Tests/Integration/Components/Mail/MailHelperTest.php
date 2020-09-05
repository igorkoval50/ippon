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

namespace SwagBusinessEssentials\Tests\Integration\Components\Mail;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Shopware\Models\Customer\Customer;
use SwagBusinessEssentials\Components\Mail\MailHelper;
use SwagBusinessEssentials\Tests\KernelTestCaseTrait;

class MailHelperTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_construct()
    {
        $mailHelper = new MailHelper(
            Shopware()->Container()->get('config'),
            Shopware()->Container()->get('templatemail'),
            Shopware()->Container()->get('models'),
            Shopware()->Container()->get('snippets'),
            Shopware()->Container()->get('shopware.components.shop_registration_service')
        );

        static::assertInstanceOf(MailHelper::class, $mailHelper);
    }

    public function test_getMailTemplateForCustomer_will_return_correct_accepted_template_without_settings()
    {
        $mailHelper = $this->getMailHelper();

        /** @var Customer $customer */
        $customer = self::getContainer()->get('models')->find(Customer::class, 1);

        $method = (new ReflectionClass(MailHelper::class))->getMethod('getMailTemplateForCustomer');
        $method->setAccessible(true);

        $result = $method->invoke($mailHelper, $customer);
        static::assertEquals(MailHelper::MAIL_TEMPLATE_ACCEPTED, $result);
    }

    public function test_getMailTemplateForCustomer_will_return_correct_accepted_template_with_settings()
    {
        $mailHelper = $this->getMailHelper();
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/mail_template.sql'));

        /** @var Customer $customer */
        $customer = self::getContainer()->get('models')->find(Customer::class, 1);
        $customer->setValidation('EK');

        $method = (new ReflectionClass(MailHelper::class))->getMethod('getMailTemplateForCustomer');
        $method->setAccessible(true);

        $result = $method->invoke($mailHelper, $customer);

        static::assertEquals('sEMAIL_ALLOW_TEST', $result);
    }

    public function test_getMailTemplateForCustomer_will_return_correct_deny_template_without_settings()
    {
        $mailHelper = $this->getMailHelper();

        /** @var Customer $customer */
        $customer = self::getContainer()->get('models')->find(Customer::class, 1);

        $method = (new ReflectionClass(MailHelper::class))->getMethod('getMailTemplateForCustomer');
        $method->setAccessible(true);

        $result = $method->invoke($mailHelper, $customer, false);
        static::assertEquals(MailHelper::MAIL_TEMPLATE_REJECTED, $result);
    }

    public function test_getMailTemplateForCustomer_will_return_correct_deny_template_with_settings()
    {
        $mailHelper = $this->getMailHelper();
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/mail_template.sql'));

        /** @var Customer $customer */
        $customer = self::getContainer()->get('models')->find(Customer::class, 1);
        $customer->setValidation('EK');

        $method = (new ReflectionClass(MailHelper::class))->getMethod('getMailTemplateForCustomer');
        $method->setAccessible(true);

        $result = $method->invoke($mailHelper, $customer, false);

        static::assertEquals('sEMAIL_DENY_TEST', $result);
    }

    public function test_getAdditionalMailContext()
    {
        $mailHelper = $this->getMailHelper();

        $method = (new ReflectionClass(MailHelper::class))->getMethod('getAdditionalMailContext');
        $method->setAccessible(true);

        $result = $method->invoke($mailHelper);

        static::assertArrayHasKey('sConfig', $result);
    }

    /**
     * @return MailHelper
     */
    private function getMailHelper()
    {
        return self::getContainer()->get('swag_business_essentials.mail_helper');
    }
}
