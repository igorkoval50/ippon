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

namespace SwagBusinessEssentials\Tests\Integration\Components\PrivateRegister;

use PHPUnit\Framework\TestCase;
use Shopware\Bundle\AccountBundle\Service\RegisterService as CoreRegisterService;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;
use Shopware\Models\Customer\Address;
use Shopware\Models\Customer\Customer;
use Shopware\Models\Customer\Group;
use SwagBusinessEssentials\Components\PrivateRegister\RegisterService;
use SwagBusinessEssentials\Tests\KernelTestCaseTrait;

class RegisterServiceTest extends TestCase
{
    use KernelTestCaseTrait;

    public function test_register_x1()
    {
        /** @var Customer $customer */
        $customer = self::getContainer()->get('models')->find(Customer::class, 1);
        $shopModel = self::getContainer()->get('shop');
        $shop = Shop::createFromShopEntity($shopModel);

        $mock = $this->createMock(CoreRegisterService::class);
        $mock->method('register')
            ->with($shop, $customer, new Address());

        $this->getService($mock)->register($shop, $customer, new Address());

        static::assertEquals('EK', $customer->getGroup()->getKey());
    }

    public function test_register_should_assign_customer_to_temporary_customer_group()
    {
        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/Register.sql'));

        /** @var Customer $customer */
        $customer = self::getContainer()->get('models')->find(Customer::class, 1);
        $shopModel = self::getContainer()->get('shop');
        $shop = Shop::createFromShopEntity($shopModel);

        /** @var Group $customerGroup */
        $customerGroup = self::getContainer()->get('models')->find(Group::class, 1000);
        $customer->setGroup($customerGroup);
        $mock = $this->createMock(CoreRegisterService::class);
        $mock->method('register')
            ->with($shop, $customer, new Address());

        $this->getService($mock)->register($shop, $customer, new Address());

        static::assertEquals('TMP', $customer->getGroup()->getKey());
    }

    /**
     * @param $mock
     *
     * @return RegisterService
     */
    protected function getService($mock)
    {
        return new RegisterService(
            $mock,
            self::getContainer()->get('swag_business_essentials.config_helper'),
            self::getContainer()->get('models')
        );
    }
}

class RegisterServiceMock extends CoreRegisterService
{
    public function __construct()
    {
    }

    public function register(
        Shop $shop,
        Customer $customer,
        Address $billing,
        Address $shipping = null
    ) {
    }
}
