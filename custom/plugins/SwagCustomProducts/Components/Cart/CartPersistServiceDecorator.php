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
use Enlight_Components_Session_Namespace as Session;
use Shopware\Components\Cart\CartPersistServiceInterface;

class CartPersistServiceDecorator implements CartPersistServiceInterface
{
    /**
     * @var CartPersistServiceInterface
     */
    private $coreCartPersistService;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var array
     */
    private $basket;

    /**
     * @var array
     */
    private $basketAttributes;

    public function __construct(
        CartPersistServiceInterface $coreCartPersistService,
        Connection $connection,
        Session $session
    ) {
        $this->coreCartPersistService = $coreCartPersistService;
        $this->connection = $connection;
        $this->session = $session;
    }

    public function prepare(): void
    {
        $this->coreCartPersistService->prepare();

        $this->init();
        $this->createBasket();

        if (empty($this->basket)) {
            return;
        }

        $this->createBasketAttributes();
    }

    public function persist(): void
    {
        $this->coreCartPersistService->persist();

        foreach ($this->basket as $id => $basketItem) {
            $this->connection->insert('s_order_basket', $basketItem);
            $lastId = $this->connection->lastInsertId();

            if (isset($this->basketAttributes[$id])) {
                $attribute = $this->basketAttributes[$id];
                unset($attribute['id']);
                $attribute['basketID'] = $lastId;
                $this->connection->insert('s_order_basket_attributes', $attribute);
            }
        }
    }

    private function init(): void
    {
        $this->basket = [];
        $this->basketAttributes = [];
    }

    private function createBasket(): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $this->basket = $queryBuilder->select('basket.*')
            ->from('s_order_basket', 'basket')
            ->join('basket', 's_order_basket_attributes', 'basket_attributes', 'basket.id = basket_attributes.basketID')
            ->where('basket.sessionID = :sessionId')
            ->andWhere('basket.modus = 4')
            ->andWhere('basket_attributes.swag_custom_products_mode > 1')
            ->andWhere('basket_attributes.swag_custom_products_configuration_hash IS NOT NULL')
            ->setParameter('sessionId', $this->session->get('sessionId'))
            ->execute()
            ->fetchAll(\PDO::FETCH_GROUP | \PDO::FETCH_UNIQUE | \PDO::FETCH_ASSOC);
    }

    private function createBasketAttributes(): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $this->basketAttributes = $queryBuilder->select('basket_attributes.basketID')
            ->addSelect('basket_attributes.*')
            ->from('s_order_basket_attributes', 'basket_attributes')
            ->where('basket_attributes.basketID IN (:basketIds)')
            ->setParameter('basketIds', array_keys($this->basket), Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAll(\PDO::FETCH_GROUP | \PDO::FETCH_UNIQUE | \PDO::FETCH_ASSOC);
    }
}
