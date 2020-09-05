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

namespace SwagPromotion\Subscriber;

use Doctrine\DBAL\Connection;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs as EventArgs;
use SwagPromotion\Components\Services\DependencyProviderInterface;
use SwagPromotion\Components\Services\FreeGoodsServiceInterface;

class Checkout implements SubscriberInterface
{
    /**
     * @var FreeGoodsServiceInterface
     */
    private $freeGoodsService;

    /**
     * @var DependencyProviderInterface
     */
    private $dependencyProvider;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(
        FreeGoodsServiceInterface $freeGoodsService,
        DependencyProviderInterface $dependencyProvider,
        Connection $connection
    ) {
        $this->freeGoodsService = $freeGoodsService;
        $this->dependencyProvider = $dependencyProvider;
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Modules_Basket_UpdateArticle_Start' => 'onUpdateArticle',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'onCart',
        ];
    }

    public function onUpdateArticle(EventArgs $args)
    {
        $basketId = $args->get('id');
        $quantity = $args->get('quantity');

        $updateSuccess = $this->freeGoodsService->updateFreeGoodsItem($basketId, $quantity);

        if (!$updateSuccess) {
            return;
        }
    }

    /**
     * Refresh the basket
     */
    public function onCart(\Enlight_Event_EventArgs $args)
    {
        /** @var \Shopware_Controllers_Frontend_Checkout $subject */
        $subject = $args->getSubject();

        if ($subject->Request()->getActionName() !== 'changeQuantity') {
            return;
        }

        if ($this->requireBasketRefresh()) {
            $this->dependencyProvider->getModules()->Basket()->sRefreshBasket();
        }
    }

    private function requireBasketRefresh(): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $result = $queryBuilder->select('id')
            ->from('s_plugin_promotion')
            ->where('active = 1')
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('type', ':type'),
                    $queryBuilder->expr()->gt('no_vouchers', 0)
                )
            )
            ->setParameter('type', 'basket.shippingfree')
            ->execute()
            ->fetchAll();

        return count($result) > 0;
    }
}
