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

namespace SwagBundle\Tests\Functional\Services;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Models\Article\Article as Product;
use Shopware\Models\Article\Detail;
use Shopware\Models\Customer\Group;
use Shopware\Models\Order\Basket;
use Shopware\Models\Shop\Shop;
use SwagBundle\Components\BundleBasketInterface;
use SwagBundle\Components\BundleComponentInterface;
use SwagBundle\Models\Bundle;
use SwagBundle\Services\FullBundleServiceInterface;
use SwagBundle\Tests\DatabaseTestCaseTrait;
use SwagBundle\Tests\Functional\TestHelper\BundleData;
use SwagBundle\Tests\Functional\TestHelper\BundleTestDataAdministration;

class FullBundleServiceTest extends TestCase
{
    use DatabaseTestCaseTrait;

    /**
     * @var Container
     */
    private $container;

    /**
     * set up the required bundles
     */
    public function setUp(): void
    {
        $this->container = Shopware()->Container();

        /** @var BundleTestDataAdministration $dataAdministrator */
        $dataAdministrator = $this->container->get('swag_bundle.test_data_administration');
        $dataAdministrator->installBundles();
    }

    public function test_bundle_getCalculatedBundle_with_all_products()
    {
        $this->installDefaultData();
        $fullBundleService = $this->container->get('swag_bundle.full_bundle_service');

        $bundleData = BundleData::getBundleData();
        $activeBundleArray = $bundleData[0];
        /** @var \SwagBundle\Models\Bundle $activeBundle */
        $activeBundle = $this->container->get('models')->getRepository(Bundle::class)->findOneBy([
            'number' => trim($activeBundleArray['ordernumber'], '"'),
        ]);

        static::assertNotNull($activeBundle);

        $activeBundle = $fullBundleService->getCalculatedBundle($activeBundle, '', false, null, []);

        static::assertEquals($activeBundle->getDiscount()['display'], '8,28');
    }

    public function test_bundle_getCalculatedBundle_with_selected_products()
    {
        $this->installDefaultData();
        /** @var FullBundleServiceInterface $fullBundleService */
        $fullBundleService = $this->container->get('swag_bundle.full_bundle_service');
        $this->addBundleToBasket();

        /** @var \SwagBundle\Models\Bundle $activeBundle */
        $activeBundle = $this->container->get('models')->getRepository(Bundle::class)->find(1);
        $activeBundle = $fullBundleService->getCalculatedBundle($activeBundle, '', true, $this->getBasketDiscountItem());

        static::assertEquals($activeBundle->getDiscount()['display'], '4,29');
    }

    public function test_getCalculatedBundle_returns_validation_error_if_customer_group_is_not_allowed()
    {
        $this->installDefaultData();
        $bundle = $this->getBundleWithInvalidCustomerGroups();

        /** @var FullBundleServiceInterface $bundleService */
        $bundleService = $this->container->get('swag_bundle.full_bundle_service');

        $result = $bundleService->getCalculatedBundle($bundle);

        static::assertEquals(
            ['success' => false, 'bundle' => 'Bundle without customer groups', 'notForCustomerGroup' => true],
            $result
        );
    }

    public function test_getCalculatedBundle_returns_validation_error_if_bundle_is_limited_and_out_of_stock()
    {
        $this->installDefaultData();
        $bundle = $this->getLimitedBundleWithoutInStock();

        /** @var FullBundleServiceInterface $bundleService */
        $bundleService = $this->container->get('swag_bundle.full_bundle_service');

        $result = $bundleService->getCalculatedBundle($bundle);

        static::assertEquals(
            ['success' => false, 'bundle' => 'Bundle with invalid instock', 'noStock' => true],
            $result
        );
    }

    public function test_getCalculatedBundle_should_calculate_percentage_discount()
    {
        $this->installDefaultData();
        /** @var Bundle $bundle */
        $bundle = $this->container->get('models')->find(Bundle::class, 10005);

        /** @var FullBundleServiceInterface $bundleService */
        $bundleService = $this->container->get('swag_bundle.full_bundle_service');
        $bundleService->getCalculatedBundle($bundle);

        static::assertEquals(35.979999999999997, $bundle->getTotalPrice()['gross']);
        static::assertEquals(30.239999999999998, $bundle->getTotalPrice()['net']);
        static::assertEquals('35,98', $bundle->getTotalPrice()['display']);

        static::assertEquals(3.597999999999999, $bundle->getDiscount()['gross']);
        static::assertEquals(3.0240000000000009, $bundle->getDiscount()['net']);
        static::assertEquals('3,60', $bundle->getDiscount()['display']);

        static::assertEquals(32.381999999999998, $bundle->getCurrentPrice()->getGrossPrice());
        static::assertEquals(27.216, $bundle->getCurrentPrice()->getNetPrice());
    }

    public function test_getCalculatedBundle_sales_product_out_of_stock()
    {
        $this->installDefaultData();
        $fullBundleService = $this->container->get('swag_bundle.full_bundle_service');

        $bundle = $this->getBundleWithSalesOutOfStockProduct();

        $result = $fullBundleService->getCalculatedBundle($bundle);

        static::assertSame(false, $result['success']);
        static::assertSame('Bundle with sales out of stock product', $result['bundle']);
    }

    public function test_getBundlePrices_should_calculate_absolute_discount()
    {
        $this->installDefaultData();
        /** @var Bundle $bundle */
        $bundle = $this->container->get('models')->find(Bundle::class, 10006);

        /** @var FullBundleServiceInterface $bundleService */
        $bundleService = $this->container->get('swag_bundle.full_bundle_service');
        $bundleService->getCalculatedBundle($bundle);

        static::assertEquals(35.979999999999997, $bundle->getTotalPrice()['gross']);
        static::assertEquals(30.239999999999998, $bundle->getTotalPrice()['net']);
        static::assertEquals('35,98', $bundle->getTotalPrice()['display']);

        static::assertEquals(24.079999999999998, $bundle->getDiscount()['gross']);
        static::assertEquals(20.239999999999998, $bundle->getDiscount()['net']);
        static::assertEquals('24,08', $bundle->getDiscount()['display']);

        static::assertEquals(11.9, $bundle->getCurrentPrice()->getGrossPrice());
        static::assertEquals(10, $bundle->getCurrentPrice()->getNetPrice());
    }

    public function test_getCalculatedBundle_validateLastStock_parameter_is_true()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/bundle_with_products_with_last_stock.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        /** @var Bundle $bundle */
        $bundle = $this->container->get('models')->find(Bundle::class, 200);

        /** @var FullBundleServiceInterface $bundleService */
        $bundleService = $this->container->get('swag_bundle.full_bundle_service');
        $result = $bundleService->getCalculatedBundle(
            $bundle,
            'SW10002.4',
            false,
            null,
            [],
            [],
            true
        );

        static::assertTrue(is_array($result));
        static::assertFalse($result['success']);
        static::assertTrue($result['noProductInStock']);
    }

    public function test_getCalculatedBundle_validateLastStock_parameter_is_false()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/bundle_with_products_with_last_stock.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        /** @var Bundle $bundle */
        $bundle = $this->container->get('models')->find(Bundle::class, 200);

        /** @var FullBundleServiceInterface $bundleService */
        $bundleService = $this->container->get('swag_bundle.full_bundle_service');
        $result = $bundleService->getCalculatedBundle(
            $bundle,
            'SW10002.4',
            false,
            null,
            [],
            [],
            false
        );

        static::assertInstanceOf(Bundle::class, $result);
    }

    public function test_getCalculatedBundle_additionalTextIsSet()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/aditional_text_bundle.sql');
        $this->container->get('dbal_connection')->exec($sql);

        /** @var Bundle $bundle */
        $bundle = $this->container->get('models')->find(Bundle::class, 222);

        /** @var FullBundleServiceInterface $bundleService */
        $bundleService = $this->container->get('swag_bundle.full_bundle_service');

        $result = $bundleService->getCalculatedBundle(
            $bundle,
            'SW10153.1',
            false,
            null,
            [],
            [],
            false
        );

        $expected = [
            'blau / 39/40',
            'blau',
        ];

        foreach ($result->getProductData() as $index => $resultItem) {
            static::assertSame($expected[$index], $resultItem['additionalText']);
        }

        static::assertCount(2, $result->getProductData());
    }

    public function test_getCalculatedBundle_additionalTextIsSetAndTranslated()
    {
        $repository = $this->container->get('models')->getRepository(Shop::class);
        $enShop = $repository->find(2);
        $defaultShop = $this->container->get('shop');
        $this->container->set('shop', $enShop);

        $sql = file_get_contents(__DIR__ . '/_fixtures/aditional_text_bundle.sql');
        $this->container->get('dbal_connection')->exec($sql);

        /** @var Bundle $bundle */
        $bundle = $this->container->get('models')->find(Bundle::class, 222);

        /** @var FullBundleServiceInterface $bundleService */
        $bundleService = $this->container->get('swag_bundle.full_bundle_service');

        $result = $bundleService->getCalculatedBundle(
            $bundle,
            'SW10153.1',
            false,
            null,
            [],
            [],
            false
        );

        $this->container->set('shop', $defaultShop);

        $expected = [
            'Blue ONE',
            'Blue TWO',
        ];

        foreach ($result->getProductData() as $index => $resultItem) {
            static::assertSame($expected[$index], $resultItem['additionalText']);
        }

        static::assertCount(2, $result->getProductData());
    }

    /**
     * @return \Shopware\Models\Order\Basket
     */
    private function getBasketDiscountItem()
    {
        /** @var \Doctrine\ORM\QueryBuilder $builder */
        $builder = $this->container->get('models')->createQueryBuilder();
        $builder->select(['basket', 'attribute'])
            ->from(Basket::class, 'basket')
            ->innerJoin('basket.attribute', 'attribute')
            ->where('basket.mode = :mode')
            ->andWhere('basket.sessionId = :sessionId')
            ->andWhere('attribute.bundleId IS NOT NULL')
            ->setParameters(['mode' => BundleBasketInterface::BUNDLE_DISCOUNT_BASKET_MODE, 'sessionId' => $this->container->get('session')->get('sessionId')]);

        return $builder->getQuery()->getSingleResult();
    }

    /**
     * Adds the Bundle with id 1 to the basket
     */
    private function addBundleToBasket(array $selectionConfig = [])
    {
        /** @var BundleComponentInterface $bundleService */
        $bundleService = $this->container->get('swag_bundle.bundle_component');
        /** @var \SwagBundle\Models\Bundle $activeBundle */
        $activeBundle = $this->container->get('models')->getRepository(Bundle::class)->find(1);
        static::assertNotNull($activeBundle);

        $selection = [];
        foreach ($activeBundle->getArticles() as $bundleProduct) {
            if ($bundleProduct->getArticleDetail()->getNumber() === 'SW10170') {
                continue;
            }

            $selection[] = $bundleProduct;
        }

        $bundleService->addBundleToBasket(1, $selection, $selectionConfig);
    }

    /**
     * @return Bundle
     */
    private function getBundleWithSalesOutOfStockProduct()
    {
        $bundle = new Bundle();
        $bundle->setArticle($this->getAssociatedSalesOutOfStockProduct());
        $bundle->setName('Bundle with sales out of stock product');
        $bundle->setCustomerGroups($this->getCustomerGroups());

        return $bundle;
    }

    /**
     * @return Bundle
     */
    private function getLimitedBundleWithoutInStock()
    {
        $bundle = new Bundle();
        $bundle->setArticle($this->getAssociatedArticle());
        $bundle->setName('Bundle with invalid instock');
        $bundle->setLimited(true);
        $bundle->setQuantity(0);
        $bundle->setCustomerGroups($this->getCustomerGroups());

        return $bundle;
    }

    /**
     * @return Bundle
     */
    private function getBundleWithInvalidCustomerGroups()
    {
        $bundle = new Bundle();
        $bundle->setArticle($this->getAssociatedArticle());
        $bundle->setName('Bundle without customer groups');
        $bundle->setCustomerGroups($this->getCustomerGroups('FOO'));

        return $bundle;
    }

    /**
     * @return Product
     */
    private function getAssociatedArticle()
    {
        $detail = new Detail();

        $product = new Product();
        $product->setConfiguratorSet(null);
        $product->setMainDetail($detail);

        return $product;
    }

    /**
     * @return Product
     */
    private function getAssociatedSalesOutOfStockProduct()
    {
        $detail = new Detail();
        $detail->setInStock(0);
        $detail->setLastStock(true);

        $product = new Product();
        $product->setConfiguratorSet(null);
        $product->setMainDetail($detail);

        return $product;
    }

    /**
     * @param string $groupKey
     *
     * @return ArrayCollection
     */
    private function getCustomerGroups($groupKey = 'EK')
    {
        $customerGroup = new Group();
        $customerGroup->setKey($groupKey);

        return new ArrayCollection([$customerGroup]);
    }
}
