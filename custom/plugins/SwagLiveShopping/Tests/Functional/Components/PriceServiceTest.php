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

namespace SwagLiveShopping\Tests\Functional\Components;

use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Struct\Country;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContext;
use Shopware\Tests\Functional\Traits\DatabaseTransactionBehaviour;
use SwagLiveShopping\Components\LiveShoppingInterface;
use SwagLiveShopping\Components\LiveShoppingTypeNotSupportedException;
use SwagLiveShopping\Components\NoAssociatedTaxRate;
use SwagLiveShopping\Components\PriceService;
use SwagLiveShopping\Tests\Functional\Components\mocks\ContextServiceMock;
use SwagLiveShopping\Tests\UserLoginTrait;

class PriceServiceTest extends TestCase
{
    use DatabaseTransactionBehaviour;
    use UserLoginTrait;

    const EXPECT_NOT_SUPPORTED_EXCEPTION = -999999;

    const EXPECT_NOT_ASSOCIATED_EXCEPTION = -888888;

    /**
     * @dataProvider getLiveShoppingPrice_dataProvider
     *
     * @param int $liveShoppingId
     * @param int $liveShoppingType
     *
     * @throws \SwagLiveShopping\Components\NoLiveShoppingPriceException
     */
    public function test_getLiveShoppingPrice(
        $liveShoppingId,
        $liveShoppingType,
        \DateTime $buyTime,
        \DateTime $validFrom,
        \DateTime $validTo,
        $expectedResult
    ) {
        $this->installLiveShopping();
        $service = $this->getService();

        if ($expectedResult === self::EXPECT_NOT_SUPPORTED_EXCEPTION) {
            $this->expectException(LiveShoppingTypeNotSupportedException::class);
        }

        if ($expectedResult === self::EXPECT_NOT_ASSOCIATED_EXCEPTION) {
            $this->expectException(NoAssociatedTaxRate::class);
        }

        $price = $service->getLiveShoppingPrice(
            $liveShoppingId,
            $liveShoppingType,
            $buyTime,
            $validFrom,
            $validTo
        );

        static::assertSame($expectedResult, round($price, 2, PHP_ROUND_HALF_UP));
    }

    /**
     * @return array
     */
    public function getLiveShoppingPrice_dataProvider()
    {
        return [
            [
                1,
                null,
                new \DateTime('2018-07-20 13:00:00'),
                new \DateTime('2018-07-20 00:00:00'),
                new \DateTime('2018-07-31 00:00:00'),
                self::EXPECT_NOT_SUPPORTED_EXCEPTION,
            ],
            [
                1,
                4,
                new \DateTime('2018-07-20 13:00:00'),
                new \DateTime('2018-07-20 00:00:00'),
                new \DateTime('2018-07-31 00:00:00'),
                self::EXPECT_NOT_SUPPORTED_EXCEPTION,
            ],
            [
                1,
                'aaa',
                new \DateTime('2018-07-20 13:00:00'),
                new \DateTime('2018-07-20 00:00:00'),
                new \DateTime('2018-07-31 00:00:00'),
                self::EXPECT_NOT_SUPPORTED_EXCEPTION,
            ],
            [
                1,
                '1',
                new \DateTime('2018-07-20 13:00:00'),
                new \DateTime('2018-07-20 00:00:00'),
                new \DateTime('2018-07-31 00:00:00'),
                self::EXPECT_NOT_SUPPORTED_EXCEPTION,
            ],
            [
                1,
                '1',
                new \DateTime('2018-07-20 13:00:00'),
                new \DateTime('2018-07-20 00:00:00'),
                new \DateTime('2018-07-31 00:00:00'),
                self::EXPECT_NOT_SUPPORTED_EXCEPTION,
            ],
            [
                2,
                LiveShoppingInterface::NORMAL_TYPE,
                new \DateTime('2018-07-20 13:00:00'),
                new \DateTime('2018-07-20 00:00:00'),
                new \DateTime('2018-07-31 00:00:00'),
                self::EXPECT_NOT_ASSOCIATED_EXCEPTION,
            ],
            [
                1,
                LiveShoppingInterface::NORMAL_TYPE,
                new \DateTime('2018-07-20 13:00:00'),
                new \DateTime('2018-07-20 00:00:00'),
                new \DateTime('2018-07-31 00:00:00'),
                100.00,
            ],
            [
                1,
                LiveShoppingInterface::NORMAL_TYPE,
                new \DateTime('2018-07-30 13:00:00'),
                new \DateTime('2018-07-20 00:00:00'),
                new \DateTime('2018-07-31 00:00:00'),
                100.00,
            ],
            [
                1,
                LiveShoppingInterface::DISCOUNT_TYPE,
                new \DateTime('2018-07-20 13:00:00'),
                new \DateTime('2018-07-20 00:00:00'),
                new \DateTime('2018-07-31 00:00:00'),
                120.92,
            ],
            [
                1,
                LiveShoppingInterface::DISCOUNT_TYPE,
                new \DateTime('2018-07-30 13:00:00'),
                new \DateTime('2018-07-20 00:00:00'),
                new \DateTime('2018-07-31 00:00:00'),
                100.92,
            ],
            [
                1,
                LiveShoppingInterface::SURCHARGE_TYPE,
                new \DateTime('2018-07-20 13:00:00'),
                new \DateTime('2018-07-20 00:00:00'),
                new \DateTime('2018-07-31 00:00:00'),
                123.08,
            ],
            [
                1,
                LiveShoppingInterface::SURCHARGE_TYPE,
                new \DateTime('2018-07-30 13:00:00'),
                new \DateTime('2018-07-20 00:00:00'),
                new \DateTime('2018-07-31 00:00:00'),
                143.08,
            ],
        ];
    }

    public function test_getLiveShoppingPrice_considerCurrencyFactor()
    {
        $this->installLiveShopping();
        $service = $this->getService();

        $contextService = new ContextServiceMock(
            Shopware()->Container(),
            Shopware()->Container()->get('Shopware\Bundle\StoreFrontBundle\Service\Core\ShopContextFactoryInterface')
        );
        $contextService->setTestCurrencyFactor(2);

        $reflectionProperty = (new \ReflectionClass(PriceService::class))->getProperty('contextService');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($service, $contextService);

        $result = $service->getLiveShoppingPrice(
            1,
            LiveShoppingInterface::NORMAL_TYPE,
            new \DateTime('2018-07-20 13:00:00'),
            new \DateTime('2018-07-20 00:00:00'),
            new \DateTime('2018-07-31 00:00:00')
        );

        static::assertSame(200.00, round($result, 2));
    }

    public function test_applyLiveShoppingPrice_considerCurrencyfactor()
    {
        $this->installLiveShopping();

        $liveShopping = $this->getLiveShoppingArray();
        $liveShopping['price'] = 100.00;
        $liveShopping['endprice'] = 100.00;
        $liveShopping['tax'] = 10;

        $service = $this->getService();

        $contextService = new ContextServiceMock(
            Shopware()->Container(),
            Shopware()->Container()->get('Shopware\Bundle\StoreFrontBundle\Service\Core\ShopContextFactoryInterface')
        );
        $contextService->setTestCurrencyFactor(3);

        $reflectionProperty = (new \ReflectionClass(PriceService::class))->getProperty('contextService');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($service, $contextService);

        $result = $service->applyLiveShoppingPrice($liveShopping, $contextService->getShopContext()->getCurrentCustomerGroup(), true);

        static::assertSame(330.00, $result['currentPrice']);
    }

    public function test_applyLiveShoppingPrice_considerTaxFreeCountry()
    {
        $this->installLiveShopping();

        $liveShopping = $this->getLiveShoppingArray();
        $liveShopping['price'] = 100.00;
        $liveShopping['endprice'] = 100.00;
        $liveShopping['tax'] = 10;

        $service = $this->getService();

        $contextService = new ContextServiceMock(
            Shopware()->Container(),
            Shopware()->Container()->get('Shopware\Bundle\StoreFrontBundle\Service\Core\ShopContextFactoryInterface')
        );
        $contextService->setTestCurrencyFactor(1);

        $country = new Country();
        $country->setTaxFree(true);

        $context = $contextService->getShopContext();

        $reflectionPropertyCountry = (new \ReflectionClass(ShopContext::class))->getProperty('country');
        $reflectionPropertyCountry->setAccessible(true);
        $reflectionPropertyCountry->setValue($context, $country);

        $reflectionPropertyContext = (new \ReflectionClass(ContextService::class))->getProperty('context');
        $reflectionPropertyContext->setAccessible(true);
        $reflectionPropertyContext->setValue($contextService, $context);

        $reflectionProperty = (new \ReflectionClass(PriceService::class))->getProperty('contextService');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($service, $contextService);

        $result = $service->applyLiveShoppingPrice($liveShopping, $contextService->getShopContext()->getCurrentCustomerGroup(), true);

        static::assertSame(100.00, $result['currentPrice']);
    }

    public function test_getTaxRate(): void
    {
        $this->installLiveShopping();

        $priceService = $this->getService();

        $result = $priceService->getTaxRate(1);

        static::assertSame('7.00', $result);
    }

    public function test_getTaxRate_withTaxRule(): void
    {
        // install User and tax rule for austria user
        $sql = file_get_contents(__DIR__ . '/_fixtures/LiveShoppingUserAndTaxRule.sql');
        Shopware()->Container()->get('dbal_connection')->exec($sql);

        $this->loginAustrianCustomer();
        $this->installLiveShopping();

        $priceService = $this->getService();

        Shopware()->Container()->reset('shopware_storefront.context_service');
        $contextService = Shopware()->Container()->get('shopware_storefront.context_service');

        $contextServiceProperty = (new \ReflectionClass(\sArticles::class))->getProperty('contextService');
        $contextServiceProperty->setAccessible(true);
        $contextServiceProperty->setValue(
            Shopware()->Modules()->Articles(),
            $contextService
        );

        $result = $priceService->getTaxRate(1);

        // Reset
        $this->logOutUser();
        Shopware()->Container()->reset('shopware_storefront.context_service');
        $contextServiceProperty->setValue(
            Shopware()->Modules()->Articles(),
            Shopware()->Container()->get('shopware_storefront.context_service')
        );

        static::assertSame('10.00', $result);
    }

    public function loginAustrianCustomer(): void
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

    /**
     * @return PriceService
     */
    private function getService()
    {
        return new PriceService(
            Shopware()->Container()->get('dbal_connection'),
            Shopware()->Container()->get('shopware_storefront.context_service'),
            Shopware()->Container()->get('swag_liveshopping.dependendency_provider')
        );
    }

    private function installLiveShopping()
    {
        $sql = file_get_contents(__DIR__ . '/_fixtures/LiveShoppingPriceTest.sql');

        Shopware()->Container()->get('dbal_connection')->exec($sql);
    }

    private function getLiveShoppingArray(): array
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();

        return $queryBuilder->select('*')
            ->from('s_articles_lives')
            ->where('id = 1')
            ->execute()
            ->fetch();
    }
}
