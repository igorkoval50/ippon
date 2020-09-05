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

namespace SwagPromotion\Tests\Functional\Subscriber;

use Shopware\Components\DependencyInjection\Container;
use SwagPromotion\Subscriber\Checkout;
use SwagPromotion\Tests\DatabaseTestCaseTrait;
use SwagPromotion\Tests\PromotionTestCase;

class CheckoutTest extends PromotionTestCase
{
    use DatabaseTestCaseTrait;

    /**
     * @var Container
     */
    private $container;

    public function setUp(): void
    {
        parent::setUp();

        $this->container = Shopware()->Container();
    }

    public function test_onUpdateArticle()
    {
        $arguments = new CheckoutTestArgumentMock();
        $arguments->setData('id', '672659');
        $arguments->setData('quantity', 0);

        $sql = file_get_contents(__DIR__ . '/Fixtures/promotion1.sql');
        $this->container->get('dbal_connection')->exec($sql);

        $subscriber = $this->getCheckoutSubscriber();
        $subscriber->onUpdateArticle($arguments);

        $sql = 'SELECT swag_is_free_good_by_promotion_id FROM s_order_basket_attributes WHERE basketID = 672659';
        $result = $this->container->get('dbal_connection')->fetchColumn($sql);

        static::assertEmpty($result);
    }

    public function test_requireBasketRefresh_shouldReturn_false()
    {
        $this->installRequireRefreshPromotions();
        $checkoutSubscriber = $this->getCheckoutSubscriber();

        $reflectionMethod = (new \ReflectionClass(Checkout::class))->getMethod('requireBasketRefresh');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($checkoutSubscriber);

        static::assertFalse($result);
    }

    public function test_requireBasketRefresh_shouldReturn_true()
    {
        $this->installRequireRefreshPromotions();
        $this->activatePromotion(1);
        $this->activatePromotion(2);
        $checkoutSubscriber = $this->getCheckoutSubscriber();

        $reflectionMethod = (new \ReflectionClass(Checkout::class))->getMethod('requireBasketRefresh');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($checkoutSubscriber);

        static::assertTrue($result);
    }

    public function test_requireBasketRefresh_shouldReturn_true_becauseNoVoucherAllowedPromotion()
    {
        $this->installRequireRefreshPromotions();
        $this->activatePromotion(1);
        $checkoutSubscriber = $this->getCheckoutSubscriber();

        $reflectionMethod = (new \ReflectionClass(Checkout::class))->getMethod('requireBasketRefresh');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($checkoutSubscriber);

        static::assertTrue($result);
    }

    public function test_requireBasketRefresh_shouldReturn_true_because_shippingFreePromotion()
    {
        $this->installRequireRefreshPromotions();
        $this->activatePromotion(2);
        $checkoutSubscriber = $this->getCheckoutSubscriber();

        $reflectionMethod = (new \ReflectionClass(Checkout::class))->getMethod('requireBasketRefresh');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($checkoutSubscriber);

        static::assertTrue($result);
    }

    private function installRequireRefreshPromotions()
    {
        $sql = file_get_contents(__DIR__ . '/Fixtures/create_require_refresh_promotions.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);
    }

    private function activatePromotion(int $id)
    {
        $sql = 'UPDATE s_plugin_promotion SET active = 1 WHERE id = ?';
        Shopware()->Container()->get('dbal_connection')->executeQuery($sql, [$id]);
    }

    private function getCheckoutSubscriber(): Checkout
    {
        return new Checkout(
            $this->container->get('swag_promotion.service.free_goods_service'),
            $this->container->get('swag_promotion.service.dependency_provider'),
            $this->container->get('dbal_connection')
        );
    }
}

class CheckoutTestArgumentMock extends \Enlight_Event_EventArgs
{
    /**
     * @var array
     */
    public $data;

    public function __construct()
    {
        $this->data = [];
    }

    /**
     * @param string $key
     */
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $string
     */
    public function get($string)
    {
        return $this->data[$string];
    }
}
