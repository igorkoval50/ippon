<?php declare(strict_types=1);
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

use PHPUnit\Framework\TestCase;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContext;
use Shopware\Tests\Functional\Traits\DatabaseTransactionBehaviour;
use SwagLiveShopping\Components\LiveShoppingService;
use SwagLiveShopping\Components\LiveShoppingServiceInterface;

class LiveShoppingServiceTest extends TestCase
{
    use DatabaseTransactionBehaviour;

    public function test_getLiveShoppingList_priceIdIsDifferentToLiveShoppingId(): void
    {
        Shopware()->Container()->get('dbal_connection')->exec(
            file_get_contents(__DIR__ . '/_fixtures/LiveShoppingsWithDifferentLivePricesIds.sql')
        );

        $service = $this->getLiveShoppingService();

        /** @var ShopContext $context */
        $context = Shopware()->Container()->get('shopware_storefront.context_service')->createShopContext(1, 1, 'EK');

        $result = $service->getLiveShoppingList([1, 2, 3], $context->getCurrentCustomerGroup());
        $secondResult = array_column($result, 'priceId');

        static::assertTrue(is_array($result));

        static::assertTrue(in_array('101', $secondResult));
        static::assertTrue(in_array('202', $secondResult));
        static::assertTrue(in_array('303', $secondResult));
    }

    private function getLiveShoppingService(): LiveShoppingServiceInterface
    {
        return new LiveShoppingService(
            Shopware()->Container()->get('dbal_connection'),
            Shopware()->Container()->get('swag_liveshopping.price_service')
        );
    }
}
