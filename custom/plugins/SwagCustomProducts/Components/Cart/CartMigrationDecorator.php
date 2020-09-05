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

namespace SwagCustomProducts\Components\Cart;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Enlight_Components_Session_Namespace as Session;
use sBasket as Basket;
use Shopware\Components\Cart\CartMigrationInterface;

class CartMigrationDecorator implements CartMigrationInterface
{
    /**
     * @var CartMigrationInterface
     */
    private $coreCartMigration;

    /**
     * @var Basket
     */
    private $basket;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(
        CartMigrationInterface $coreCartMigration,
        Basket $basket,
        Session $session,
        Connection $connection
    ) {
        $this->coreCartMigration = $coreCartMigration;
        $this->basket = $basket;
        $this->session = $session;
        $this->connection = $connection;
    }

    public function migrate(): void
    {
        $this->coreCartMigration->migrate();

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->update('s_order_basket', 'basket')
            ->set('basket.sessionID', ':sessionId')
            ->where('basket.userID = :userId')
            ->andWhere('basket.modus = 4')
            ->andWhere($queryBuilder->expr()->in('basket.id', $this->getAttributeQuery()->getSQL()))
            ->setParameter('sessionId', $this->session->get('sessionId'))
            ->setParameter('userId', $this->session->get('sUserId'))
            ->execute();

        $this->basket->sRefreshBasket();
    }

    private function getAttributeQuery(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
            ->select('basket_attributes.basketID')
            ->from('s_order_basket_attributes', 'basket_attributes')
            ->where('basket_attributes.swag_custom_products_mode > 1')
            ->andWhere('basket_attributes.swag_custom_products_configuration_hash IS NOT NULL');
    }
}
