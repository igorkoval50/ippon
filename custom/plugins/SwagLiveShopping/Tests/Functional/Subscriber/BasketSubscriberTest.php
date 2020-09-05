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

namespace SwagLiveShopping\Tests\Functional\Subscriber;

use PHPUnit\Framework\TestCase;
use Shopware\Tests\Functional\Traits\DatabaseTransactionBehaviour;
use SwagLiveShopping\Components\LiveShoppingInterface;
use SwagLiveShopping\Models\LiveShopping;
use SwagLiveShopping\Subscriber\BasketSubscriber;
use SwagLiveShopping\Tests\UserLoginTrait;

class BasketSubscriberTest extends TestCase
{
    use DatabaseTransactionBehaviour;
    use UserLoginTrait;

    public function test_getPrice_NORMAL_TYPE()
    {
        $this->installLiveShopping();

        $liveShopping = new LiveShopping();
        $liveShopping->setId(1);
        $liveShopping->setType(LiveShoppingInterface::NORMAL_TYPE);
        $liveShopping->setValidFrom(new \DateTime('1970-01-01 00:00:00'));
        $liveShopping->setValidTo(new \DateTime('3000-01-01 00:00:00'));

        $basket = [
            'datum' => '2019-01-01 00:00:00',
            'tax_rate' => 10,
        ];

        $subscriber = $this->getSubscriber();
        $method = (new \ReflectionClass(BasketSubscriber::class))->getMethod('getPrice');
        $method->setAccessible(true);

        $result = $method->invokeArgs($subscriber, [$liveShopping, $basket]);

        static::assertSame(100.00, round($result['price'], 2));
        static::assertSame(90.91, round($result['netPrice'], 2));
        static::assertSame('7.00', $result['taxrate']);
    }

    public function test_getPrice_DISCOUNT_TYPE()
    {
        $this->installLiveShopping();

        $liveShopping = new LiveShopping();
        $liveShopping->setId(1);
        $liveShopping->setType(LiveShoppingInterface::DISCOUNT_TYPE);
        $liveShopping->setValidFrom(new \DateTime('1970-01-01 00:00:00'));
        $liveShopping->setValidTo(new \DateTime('3000-01-01 00:00:00'));

        $basket = [
            'datum' => '2019-01-01 00:00:00',
            'tax_rate' => 10,
        ];

        $subscriber = $this->getSubscriber();
        $method = (new \ReflectionClass(BasketSubscriber::class))->getMethod('getPrice');
        $method->setAccessible(true);

        $result = $method->invokeArgs($subscriber, [$liveShopping, $basket]);

        static::assertSame(120.95, round($result['price'], 2));
        static::assertSame(109.96, round($result['netPrice'], 2));
        static::assertSame('7.00', $result['taxrate']);
    }

    public function test_getPrice_SURCHARGE_TYPE()
    {
        $this->installLiveShopping();

        $liveShopping = new LiveShopping();
        $liveShopping->setId(1);
        $liveShopping->setType(LiveShoppingInterface::SURCHARGE_TYPE);
        $liveShopping->setValidFrom(new \DateTime('1970-01-01 00:00:00'));
        $liveShopping->setValidTo(new \DateTime('3000-01-01 00:00:00'));

        $basket = [
            'datum' => '2019-01-01 00:00:00',
            'tax_rate' => 10,
        ];

        $subscriber = $this->getSubscriber();
        $method = (new \ReflectionClass(BasketSubscriber::class))->getMethod('getPrice');
        $method->setAccessible(true);

        $result = $method->invokeArgs($subscriber, [$liveShopping, $basket]);

        static::assertSame(123.05, round($result['price'], 2));
        static::assertSame(111.86, round($result['netPrice'], 2));
        static::assertSame('7.00', $result['taxrate']);
    }

    public function test_getPrice_NORMAL_TYPE_withTaxRule(): void
    {
        $this->installLiveShopping();
        $this->installLiveShoppingWithTaxRule();
        $this->loginAustrianUser();

        $liveShopping = new LiveShopping();
        $liveShopping->setId(1);
        $liveShopping->setType(LiveShoppingInterface::NORMAL_TYPE);
        $liveShopping->setValidFrom(new \DateTime('1970-01-01 00:00:00'));
        $liveShopping->setValidTo(new \DateTime('3000-01-01 00:00:00'));

        $basket = [
            'datum' => '2019-01-01 00:00:00',
            'tax_rate' => 10,
        ];

        Shopware()->Container()->reset('shopware_storefront.context_service');
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');

        $contextServiceProperty = (new \ReflectionClass(\sArticles::class))->getProperty('contextService');
        $contextServiceProperty->setAccessible(true);
        $contextServiceProperty->setValue(
            Shopware()->Modules()->Articles(),
            $contextService
        );

        $subscriber = $this->getSubscriber();
        $method = (new \ReflectionClass(BasketSubscriber::class))->getMethod('getPrice');
        $method->setAccessible(true);

        $result = $method->invokeArgs($subscriber, [$liveShopping, $basket]);

        // Reset
        $this->logOutUser();
        Shopware()->Container()->reset('shopware_storefront.context_service');
        $contextServiceProperty->setValue(
            Shopware()->Modules()->Articles(),
            Shopware()->Container()->get('shopware_storefront.context_service')
        );

        static::assertSame(102.80, round($result['price'], 2));
        static::assertSame(93.46, round($result['netPrice'], 2));
        static::assertSame('10.00', $result['taxrate']);
    }

    private function getSubscriber()
    {
        $container = Shopware()->Container();

        return new BasketSubscriber(
            $container->get('swag_liveshopping.live_shopping_basket'),
            $container->get('swag_liveshopping.live_shopping'),
            $container->get('models'),
            $container->get('swag_liveshopping.price_service'),
            $container->get('shopware_storefront.context_service')
        );
    }

    private function installLiveShopping()
    {
        $sql = file_get_contents(__DIR__ . '/../Components/_fixtures/LiveShoppingPriceTest.sql');

        Shopware()->Container()->get('dbal_connection')->exec($sql);
    }

    private function installLiveShoppingWithTaxRule(): void
    {
        $sql = file_get_contents(__DIR__ . '/../Components/_fixtures/LiveShoppingUserAndTaxRule.sql');

        Shopware()->Container()->get('dbal_connection')->exec($sql);
    }

    private function loginAustrianUser(): void
    {
        $isCustomerLoggedIn = $this->loginUser(
            'PHPUnitTestSessionId',
            5,
            'foo@bar.at',
            '$2y$10$rLnUR.8wNQFVnapW6Rw6KeZqmicNR6torejhKkikeqLT6vljXYzXi',
            'EK',
            23,
            3,
            70
        );

        static::assertTrue($isCustomerLoggedIn, 'Austrian user is not logged in');
    }
}
