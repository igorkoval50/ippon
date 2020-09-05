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

namespace SwagBusinessEssentials\Tests\Integration\Components\RequestManager;

use PHPUnit\Framework\TestCase;
use Shopware\Models\Customer\Customer;
use SwagBusinessEssentials\Components\Mail\MailHelper;
use SwagBusinessEssentials\Components\RequestManager\RequestManager;
use SwagBusinessEssentials\Tests\KernelTestCaseTrait;

class RequestManagerTest extends TestCase
{
    use KernelTestCaseTrait;

    /**
     * @var RequestManager
     */
    private $service;

    public function test_acceptCustomerRequest()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures.sql'));

        $this->service->acceptCustomerRequest(1);

        $customer = self::getKernel()->getContainer()->get('models')->find(Customer::class, 1);

        static::assertEquals('', $customer->getValidation());
        static::assertEquals('H', $customer->getGroup()->getKey());
    }

    public function test_declineCustomerRequest()
    {
        $this->service->declineCustomerRequest(1);

        $result = self::getKernel()->getContainer()->get('dbal_connection')->fetchAll('SELECT * FROM s_user WHERE id = 1');

        static::assertEquals('', $result[0]['validation']);
        static::assertEquals('EK', $result[0]['customergroup']);
    }

    /**
     * @before
     */
    protected function createServiceBefore()
    {
        $this->service = new RequestManager(
            self::getKernel()->getContainer()->get('models'),
            new MailHelperDummy()
        );
    }
}

class MailHelperDummy extends MailHelper
{
    public function __construct()
    {
    }

    public function sendAcceptedMail(Customer $customer)
    {
    }

    public function sendDeclinedMail(Customer $customer)
    {
    }
}
