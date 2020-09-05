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
use Doctrine\DBAL\Query\QueryBuilder;
use Enlight\Event\SubscriberInterface;
use Enlight_Components_Session_Namespace as Session;

class Basket implements SubscriberInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Session
     */
    private $session;

    public function __construct(Connection $connection, Session $session)
    {
        $this->connection = $connection;
        $this->session = $session;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Modules_Basket_getPriceForUpdateArticle_FilterPrice' => 'onGetPriceForUpdateProduct',
            'Shopware_Modules_Basket_GetBasket_FilterResult' => 'updateReferencePrice',
            'Shopware_Modules_Basket_AddArticle_CheckBasketForArticle' => 'onCheckBasketForProduct',
        ];
    }

    public function onCheckBasketForProduct(\Enlight_Event_EventArgs $args)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $args->get('queryBuilder');

        if (!$this->session->offsetExists('freeGoodsOrderNumber')) {
            $queryBuilder
                ->join('basket', 's_order_basket_attributes', 'promotion_basket_attr', 'promotion_basket_attr.basketID = basket.id')
                ->andWhere('promotion_basket_attr.swag_is_free_good_by_promotion_id IS NULL');

            return;
        }

        $queryBuilder
            ->join('basket', 's_order_basket_attributes', 'promotion_basket_attr', 'promotion_basket_attr.basketID = basket.id')
            ->andWhere('promotion_basket_attr.swag_is_free_good_by_promotion_id IS NOT NULL');
    }

    public function updateReferencePrice(\Enlight_Event_EventArgs $args)
    {
        if (!$this->session->offsetExists('swag-promotion-direct-promoted-items')) {
            return;
        }

        $promotedItems = $this->session->offsetGet('swag-promotion-direct-promoted-items');
        if (!$promotedItems) {
            return;
        }

        $return = $args->getReturn();
        $products = $return['content'];
        foreach (array_keys($promotedItems) as $key) {
            foreach ($products as &$product) {
                if ((int) $product['id'] === (int) $key && isset($product['additional_details']['unitID'])) {
                    $price = (float) str_replace(',', '.', $product['price']);
                    $purchaseUnit = $product['additional_details']['purchaseunit'];
                    $referenceUnit = $product['additional_details']['referenceunit'];

                    $product['additional_details']['referenceprice'] = $price / $purchaseUnit * $referenceUnit;

                    continue 2;
                }
            }
        }

        unset($product);

        $return['content'] = $products;

        $args->setReturn($return);
    }

    public function onGetPriceForUpdateProduct(\Enlight_Event_EventArgs $args)
    {
        if (!$this->session->offsetExists('swag-promotion-direct-promoted-items')) {
            return;
        }
        $basketItemId = $args->get('id');
        $itemData = $args->getReturn();

        $promotedItems = $this->session->offsetGet('swag-promotion-direct-promoted-items');

        if (isset($promotedItems[$basketItemId])) {
            $taxRate = (float) $itemData['tax_rate'];
            $itemPrice = (float) $itemData['price'];
            $directDiscount = (float) $promotedItems[$basketItemId]['discount'];

            if ($this->session->offsetGet('sOutputNet') === true) {
                $directDiscountNet = $directDiscount;
            } else {
                $directDiscountNet = $directDiscount / (1 + ($taxRate / 100));
            }

            $itemData['price'] = (string) ($itemPrice - $directDiscountNet);

            $args->setReturn($itemData);
        }
    }
}
