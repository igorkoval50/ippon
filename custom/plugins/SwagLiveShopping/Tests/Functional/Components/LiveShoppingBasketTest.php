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

namespace Shopware\SwagLiveShopping\Tests\Functional\Components;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\AbstractQuery;
use Shopware\Components\Model\QueryBuilder;
use Shopware\Models\Article\Detail;
use Shopware\Models\Article\Price;
use Shopware\Models\Customer\Group;
use Shopware\Models\Order\Basket;
use Shopware\Tests\Functional\Traits\DatabaseTransactionBehaviour;
use SwagLiveShopping\Components\LiveShoppingBasketInterface;

class LiveShoppingBasketTest extends \PHPUnit\Framework\TestCase
{
    use DatabaseTransactionBehaviour;

    public function test_getNewBasketItem()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();

        $result = $liveShoppingBasket->getNewBasketItem();

        static::assertInstanceOf(Basket::class, $result);
    }

    public function test_getVariantCreateData()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingVariantProduct();

        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(322);

        $result = $liveShoppingBasket->getVariantCreateData($variant, 1);

        $expectedResult = [
            'articleId' => 153,
            'orderNumber' => 'SW10153.1',
            'quantity' => 1,
        ];

        static::assertSame($expectedResult['articleId'], $result['articleId']);
        static::assertSame($expectedResult['orderNumber'], $result['orderNumber']);
        static::assertSame($expectedResult['quantity'], $result['quantity']);
    }

    public function test_getAttributeCreateData()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $result = $liveShoppingBasket->getAttributeCreateData(new Detail(), 1);

        $expectedResult = [
            'attribute1' => null,
            'attribute2' => null,
            'attribute3' => null,
            'attribute4' => null,
            'attribute5' => null,
            'attribute6' => null,
        ];

        static::assertSame($expectedResult['attribute1'], $result['attribute1']);
        static::assertSame($expectedResult['attribute2'], $result['attribute2']);
        static::assertSame($expectedResult['attribute3'], $result['attribute3']);
        static::assertSame($expectedResult['attribute4'], $result['attribute4']);
        static::assertSame($expectedResult['attribute5'], $result['attribute5']);
        static::assertSame($expectedResult['attribute6'], $result['attribute6']);
    }

    public function test_getVariantUpdateData()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingVariantProduct();

        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(322);

        $result = $liveShoppingBasket->getVariantUpdateData($variant, 5);

        $expectedResult = [
            'quantity' => 5,
        ];

        static::assertSame($expectedResult['quantity'], $result['quantity']);
    }

    public function test_getAttributeUpdateData()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingVariantProduct();

        $result = $liveShoppingBasket->getAttributeUpdateData(new Detail(), 5);

        static::assertEmpty($result);
    }

    public function test_updateItem()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingBasketProduct();

        $data = [
            'quantity' => 12,
        ];

        $liveShoppingBasket->updateItem(672, $data, new Detail(), 0, []);

        /** @var Basket $basket */
        $basket = Shopware()->Container()->get('models')->find(Basket::class, 672);

        static::assertInstanceOf(Basket::class, $basket);
        static::assertSame(170, $basket->getArticleId());
        static::assertSame(12, $basket->getQuantity());
    }

    public function test_getItem_as_array()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingBasketProduct();

        $result = $liveShoppingBasket->getItem(672);

        static::assertTrue(is_array($result));
        static::assertSame('SW10170', $result['orderNumber']);
    }

    public function test_getItem_as_object()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingBasketProduct();

        /** @var Basket $result */
        $result = $liveShoppingBasket->getItem(672, AbstractQuery::HYDRATE_OBJECT);

        static::assertInstanceOf(Basket::class, $result);
        static::assertSame('SW10170', $result->getOrderNumber());
    }

    public function test_getVariantName()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingVariantProduct();

        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(322);

        $result = $liveShoppingBasket->getVariantName($variant, 1);

        $expectedResult = 'Flip Flops, in mehreren Farben verfügbar blau / 39/40';

        static::assertSame($expectedResult, $result);
    }

    public function test_getProductId()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingVariantProduct();

        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(322);

        $result = $liveShoppingBasket->getProductId($variant, 1);

        static::assertSame(153, $result);
    }

    public function test_getNumber()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingVariantProduct();

        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(322);

        $result = $liveShoppingBasket->getNumber($variant, 1);

        static::assertSame('SW10153.1', $result);
    }

    public function test_getShippingFree()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingVariantProduct();

        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(322);

        $result = $liveShoppingBasket->getShippingFree($variant, 1);

        static::assertFalse((bool) $result);
    }

    public function test_getVariantPrice()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingVariantProduct();

        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(322);

        $result = $liveShoppingBasket->getVariantPrice($variant, 1);

        $expectedResult = [
            'gross' => 6.99,
            'net' => 5.8739495798319,
        ];

        static::assertSame($expectedResult['gross'], $result['gross']);
        static::assertSame($expectedResult['net'], $result['net']);
    }

    public function test_getNetAndGrossPriceForVariantPrice()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingVariantProduct();

        $price = new Price();
        $price->setPrice(99.99);

        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(322);

        $result = $liveShoppingBasket->getNetAndGrossPriceForVariantPrice($price, $variant, []);

        $expectedResult = [
            'gross' => 118.99,
            'net' => 99.99,
        ];

        static::assertSame($expectedResult['gross'], $result['gross']);
        static::assertSame($expectedResult['net'], $result['net']);
    }

    public function test_getPricesForCustomerGroup()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingVariantProduct();

        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(322);

        $result = $liveShoppingBasket->getPricesForCustomerGroup($variant, 'H', 'EK')[0];

        $expectedResult = [
            'articleId' => 153,
            'articleDetailsId' => 322,
            'customerGroupKey' => 'EK',
            'price' => 5.8739495798319,
        ];

        static::assertSame($expectedResult['articleId'], $result['articleId']);
        static::assertSame($expectedResult['articleDetailsId'], $result['articleDetailsId']);
        static::assertSame($expectedResult['customerGroupKey'], $result['customerGroupKey']);
        static::assertSame($expectedResult['price'], $result['price']);
    }

    public function test_getPriceQueryBuilder()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();

        $result = $liveShoppingBasket->getPriceQueryBuilder();

        static::assertInstanceOf(QueryBuilder::class, $result);
    }

    public function test_getCurrentCustomerGroup()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();

        /** @var Group $result */
        $result = $liveShoppingBasket->getCurrentCustomerGroup();

        static::assertInstanceOf(Group::class, $result);
        static::assertSame('EK', $result->getKey());
    }

    public function test_getPriceForQuantity()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();

        $price = new Price();
        $price->setPrice(112.99);
        $price->setFrom(0);
        $price->setTo(10);

        $price2 = new Price();
        $price2->setPrice(100);
        $price2->setFrom(11);
        $price2->setTo(20);

        /** @var Group $result */
        $result = $liveShoppingBasket->getPriceForQuantity([$price, $price2], 12, new Detail());

        static::assertInstanceOf(Price::class, $result);
        static::assertSame($price2, $result);
    }

    public function test_getEsdFlag()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingVariantProduct();

        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(322);
        $result = $liveShoppingBasket->getEsdFlag($variant, 1);

        static::assertSame(0, $result);
    }

    public function test_getVariantByOrderNumber()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingVariantProduct();

        $result = $liveShoppingBasket->getVariantByOrderNumber('SW10153.1');

        static::assertInstanceOf(Detail::class, $result);
        static::assertSame(322, $result->getId());
    }

    public function test_validateProduct()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingProductForCustomerGroupH();

        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(407);

        $request = new \Enlight_Controller_Request_RequestTestCase();
        $request->setParam('customProductsHash', '');
        Shopware()->Container()->get('front')->setRequest($request);

        $result = $liveShoppingBasket->validateProduct($variant, 1);

        $expectedResult = [
            'success' => true,
        ];

        static::assertSame($expectedResult['success'], $result['success']);
    }

    public function test_isVariantInStock_should_be_true()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingProductForCustomerGroupH();

        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(407);

        $result = $liveShoppingBasket->isVariantInStock($variant, 1);

        static::assertTrue($result);
    }

    public function test_isVariantInStock_should_be_false()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingProductForCustomerGroupH();

        /** @var Detail $variant */
        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(407);
        $variant->setLastStock(true);
        $variant->setInStock(10);

        $result = $liveShoppingBasket->isVariantInStock($variant, 12);

        static::assertFalse($result);
    }

    public function test_getSummarizedQuantityOfVariant()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();
        $this->installLiveShoppingBasketProduct();

        Shopware()->Session()->sessionId = 'sessionId';
        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(394);

        $result = $liveShoppingBasket->getSummarizedQuantityOfVariant($variant, 1);

        static::assertSame('1', $result);
    }

    public function test_isCustomerGroupAllowed_should_be_true()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();

        $customerGroup = new Group();
        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(394);
        $result = $liveShoppingBasket->isCustomerGroupAllowed($variant, $customerGroup, []);

        static::assertTrue($result);
    }

    public function test_isCustomerGroupAllowed_should_be_false()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();

        $customerGroup = Shopware()->Container()->get('models')->getRepository(Group::class)->find(2);
        /** @var Detail $variant */
        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(394);
        $variant->getArticle()->setCustomerGroups(new ArrayCollection([$customerGroup]));
        $result = $liveShoppingBasket->isCustomerGroupAllowed($variant, $customerGroup, []);

        static::assertFalse($result);
    }

    public function test_createItem()
    {
        $liveShoppingBasket = $this->getLiveShoppingBasket();

        $data = [
            'customerId' => 1,
            'partnerId' => 0,
            'articleID' => 178,
            'orderNumber' => 'SW10170',
            'quantity' => 10,
            'taxRate' => 10.00,
            'sessionId' => 'sessionId',
            'date' => new \DateTime('01.01.1970 00:00:00'),
        ];

        /** @var Detail $variant */
        $variant = Shopware()->Container()->get('models')->getRepository(Detail::class)->find(394);

        $result = $liveShoppingBasket->createItem($data, $variant, 10, []);
        /** @var Basket $result2 */
        $result2 = Shopware()->Container()->get('models')->getRepository(Basket::class)->find($result);

        static::assertNotEmpty($result);
        static::assertInstanceOf(Basket::class, $result2);
        static::assertSame($data['orderNumber'], $result2->getOrderNumber());
    }

    private function installLiveShoppingProductForCustomerGroupH()
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = Shopware()->Container()->get('dbal_connection');
        $sql = file_get_contents(__DIR__ . '/_fixtures/LiveShoppingProductCustomerGroupH.sql');
        $databaseConnection->exec($sql);
    }

    private function installLiveShoppingBasketProduct()
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = Shopware()->Container()->get('dbal_connection');
        $sql = file_get_contents(__DIR__ . '/_fixtures/LiveShoppingProductInBasket.sql');
        $databaseConnection->exec($sql);
    }

    private function installLiveShoppingVariantProduct()
    {
        /** @var Connection $databaseConnection */
        $databaseConnection = Shopware()->Container()->get('dbal_connection');
        $sql = file_get_contents(__DIR__ . '/_fixtures/LiveShoppingVariantProduct.sql');
        $databaseConnection->exec($sql);
    }

    private function registerNamespace()
    {
        Shopware()->Loader()->registerNamespace('Shopware_Components', __DIR__ . '/../../../Components/');
    }

    /**
     * @return LiveShoppingBasketInterface
     */
    private function getLiveShoppingBasket()
    {
        $this->registerNamespace();

        return Shopware()->Container()->get('swag_liveshopping.live_shopping_basket');
    }
}
