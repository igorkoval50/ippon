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

/**
 * Initialize the shopware kernel
 */
require __DIR__ . '/../../../../autoload.php';
require __DIR__ . '/Helper/PromotionFactory.php';

use Shopware\Kernel;
use Shopware\Models\Shop\Shop;
use SwagPromotion\Models\Repository\DummyRepository;

class SwagPromotionTestKernel extends Kernel
{
    public static function start()
    {
        $kernel = new self((string) getenv('SHOPWARE_ENV') ?: 'testing', true);
        $kernel->boot();

        $container = $kernel->getContainer();
        $container->get('plugins')->Core()->ErrorHandler()->registerErrorHandler(E_ALL | E_STRICT);

        /** @var \Shopware\Models\Shop\Repository $repository */
        $repository = $container->get('models')->getRepository(Shop::class);

        $shop = $repository->getActiveDefault();
        $shopRegistrationService = $container->get('shopware.components.shop_registration_service');
        $shopRegistrationService->registerResources($shop);

        $_SERVER['HTTP_HOST'] = $shop->getHost();

        if (!self::assertPlugin('SwagPromotion')) {
            throw new \Exception('Plugin SwagPromotion is not installed or activated.');
        }

        /*
         * \sBasket::sInsertPremium expects a request object and is called by sGetBasket
         * which we use a lot here
         */
        Shopware()->Front()->setRequest(new \Enlight_Controller_Request_RequestTestCase());
        Shopware()->Container()->set('swag_promotion.repository', new DummyRepository([]));
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private static function assertPlugin($name)
    {
        $sql = 'SELECT 1 FROM s_core_plugins WHERE name = ? AND active = 1';

        return (bool) Shopware()->Container()->get('dbal_connection')->fetchColumn($sql, [$name]);
    }
}

SwagPromotionTestKernel::start();
