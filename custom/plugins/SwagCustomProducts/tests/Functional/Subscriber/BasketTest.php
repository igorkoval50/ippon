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

use Doctrine\DBAL\Connection;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ShopContextFactoryInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Shop\Shop;
use SwagCustomProducts\Subscriber\Basket;
use SwagCustomProducts\Subscriber\Basket as BasketSubscriber;
use SwagCustomProducts\tests\KernelTestCaseTrait;
use SwagCustomProducts\tests\ServicesHelper;

class BasketTest extends \PHPUnit\Framework\TestCase
{
    use KernelTestCaseTrait;

    public function test_getBasket_translation_for_default_shop()
    {
        $this->registerServices();

        Shopware()->Container()->get('dbal_connection')->exec(
            file_get_contents(__DIR__ . '/_fixtures/basic_setup.sql')
        );

        $basketFixture = require __DIR__ . '/_fixtures/basket_basic.php';
        static::assertIsArray($basketFixture);
        $basket = $this->getBasketSubscriber();

        $hookArgs = new \Enlight_Hook_HookArgs($this, '');
        $hookArgs->setReturn($basketFixture);
        $computedBasket = $basket->getBasket($hookArgs);

        static::assertIsArray($computedBasket);

        static::assertEquals('Textfeld', $computedBasket['content'][1]['articlename']);
        static::assertEquals('Bildauswahl', $computedBasket['content'][16]['articlename']);
        static::assertEquals('Bildauswahl 2', $computedBasket['content'][17]['articlename']);
        static::assertEquals('Textfeld', $computedBasket['content'][0]['custom_product_adds'][19]['name']);
        static::assertEquals('Bildauswahl', $computedBasket['content'][0]['custom_product_adds'][27]['name']);
        static::assertEquals(
            'Bildauswahl 2',
            $computedBasket['content'][0]['custom_product_adds'][27]['values'][0]['name']
        );
    }

    public function test_getBasket_translate_options_and_values_in_basket()
    {
        $this->registerServices();

        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/basic_setup.sql'));

        $this->createEnglishShopContext();

        /** @var ModelManager $modelManager */
        $modelManager = Shopware()->Container()->get('models');
        /** @var Shop $englishShop */
        $englishShop = $modelManager->getRepository(Shop::class)->find(2);

        $shopRegistrationService = Shopware()->Container()->get('shopware.components.shop_registration_service');
        $shopRegistrationService->registerResources($englishShop);

        $basketFixture = require __DIR__ . '/_fixtures/basket_basic.php';
        static::assertIsArray($basketFixture);
        $basket = $this->getBasketSubscriber();

        $hookArgs = new \Enlight_Hook_HookArgs($this, '');
        $hookArgs->setReturn($basketFixture);
        $computedBasket = $basket->getBasket($hookArgs);

        Shopware()->Container()->reset('custom_products.translation_service');

        static::assertIsArray($computedBasket);

        static::assertEquals('Text field', $computedBasket['content'][0]['custom_product_adds'][19]['name']);
        static::assertEquals('Image selection', $computedBasket['content'][0]['custom_product_adds'][27]['name']);
        static::assertEquals(
            'Image selection 2',
            $computedBasket['content'][0]['custom_product_adds'][27]['values'][0]['name']
        );
    }

    public function test_getBasket_translate_option_and_value_positions_in_basket()
    {
        $this->registerServices();

        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/basic_setup.sql'));

        $this->createEnglishShopContext();

        /** @var ModelManager $modelManager */
        $modelManager = Shopware()->Container()->get('models');
        /** @var Shop $englishShop */
        $englishShop = $modelManager->getRepository(Shop::class)->find(2);

        $shopRegistrationService = Shopware()->Container()->get('shopware.components.shop_registration_service');
        $shopRegistrationService->registerResources($englishShop);

        $basketFixture = require __DIR__ . '/_fixtures/basket_basic.php';
        static::assertIsArray($basketFixture);
        $basket = $this->getBasketSubscriber();

        $hookArgs = new \Enlight_Hook_HookArgs($this, '');
        $hookArgs->setReturn($basketFixture);
        $computedBasket = $basket->getBasket($hookArgs);

        Shopware()->Container()->reset('custom_products.translation_service');

        static::assertEquals('Text field', $computedBasket['content'][1]['articlename']);
        static::assertEquals('Image selection', $computedBasket['content'][16]['articlename']);
        static::assertEquals('Image selection 2', $computedBasket['content'][17]['articlename']);
    }

    public function test_getBasket_invalid_optionId_in_configuration()
    {
        $this->registerServices();

        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/basic_setup.sql'));

        $basketFixture = require __DIR__ . '/_fixtures/basket_basic.php';
        static::assertIsArray($basketFixture);

        $basketFixture['content'][0]['customProductHash'] = 'e8561ea583614dbc2c5d2e0dddbaad8f';
        /** @var Connection $connection */
        $connection = Shopware()->Container()->get('dbal_connection');
        $connection->exec(file_get_contents(__DIR__ . '/_fixtures/basket_invalid_optionId_in_configuration.sql'));

        $basket = $this->getBasketSubscriber();
        $hookArgs = new \Enlight_Hook_HookArgs($this, '');
        $hookArgs->setReturn($basketFixture);
        $computedBasket = $basket->getBasket($hookArgs);

        static::assertEmpty($computedBasket['content'][0]['custom_product_adds']);
    }

    public function test_getBasket_invalid_hash_in_basket()
    {
        $this->registerServices();

        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/basic_setup.sql'));

        $basketFixture = require __DIR__ . '/_fixtures/basket_basic.php';
        static::assertIsArray($basketFixture);

        $basketFixture['content'][0]['customProductHash'] = 'f8561ea583614dbc2c5d2e0dddbaad8f';

        $basket = $this->getBasketSubscriber();
        $hookArgs = new \Enlight_Hook_HookArgs($this, '');
        $hookArgs->setReturn($basketFixture);
        $computedBasket = $basket->getBasket($hookArgs);

        static::assertEmpty($computedBasket['content'][0]['custom_product_adds']);
    }

    public function test_addArticle_should_add_configuration_option_to_basket()
    {
        $this->registerServices();

        $this->execSql(file_get_contents(__DIR__ . '/_fixtures/basket_fixtures.sql'));

        $eventArgs = new \Enlight_Event_EventArgs();
        $eventArgs->set('id', 'SW10008');
        $eventArgs->set('quantity', '1');
        Shopware()->Container()->set('front', new FrontControllerMock());

        $basket = new Basket(Shopware()->Container());
        $result = $basket->addArticle($eventArgs);

        static::assertTrue($result);

        /** @var Connection $connection */
        $connection = Shopware()->Container()->get('dbal_connection');
        $insertedBasketOption = $connection->fetchAll("SELECT * FROM s_order_basket WHERE ordernumber='custom1'");

        static::assertEquals('ordernumber_validation_option', $insertedBasketOption[0]['articlename']);
        static::assertEquals('custom1', $insertedBasketOption[0]['ordernumber']);
        static::assertEquals(15.966386554622, $insertedBasketOption[0]['netprice']);
    }

    public function test_onCheckBasketForArticle_should_modify_query_builder()
    {
        $basketSubscriber = $this->getBasketSubscriber();

        $queryBuilder = Shopware()->Models()->getConnection()->createQueryBuilder();
        $queryBuilder->select(['id', 'quantity', 'foo', 'bar'])
            ->from('s_test', 'basket');

        $eventArgs = new \Enlight_Event_EventArgs();
        $eventArgs->set('queryBuilder', $queryBuilder);

        $basketSubscriber->onCheckBasketForArticle($eventArgs);

        static::assertEquals(['basket.id', 'basket.quantity', 'foo', 'bar'], $queryBuilder->getQueryPart('select'));
        static::assertEquals('s_order_basket_attributes', $queryBuilder->getQueryPart('join')['basket'][0]['joinTable']);
        static::assertEquals('basketAttr.swag_custom_products_configuration_hash IS NULL', $queryBuilder->getQueryPart('where')->__toString());
    }

    private function registerServices()
    {
        $serviceHelper = new ServicesHelper(Shopware()->Container());
        $serviceHelper->registerServices();
    }

    /**
     * @return BasketSubscriber
     */
    private function getBasketSubscriber()
    {
        return new BasketSubscriber(Shopware()->Container());
    }

    private function createEnglishShopContext()
    {
        Shopware()->Container()->set(
            'shopware_storefront.context_service',
            new BasketTestShopContextMock(
                Shopware()->Container(),
                Shopware()->Container()->get(ShopContextFactoryInterface::class)
            )
        );

        Shopware()->Container()->reset('custom_products.translation_service');
    }
}

class BasketTestShopContextMock extends ContextService
{
    public function getShopContext()
    {
        return $this->createShopContext(2, 1, 'EK');
    }
}

class FrontControllerMock extends \Enlight_Controller_Front
{
    public function __construct()
    {
    }

    public function Request()
    {
        $request = new \Enlight_Controller_Request_RequestTestCase();
        $request->setParams(['customProductsHash' => '234712dfc688121d873cb925a89fb4ea']);

        return $request;
    }
}
