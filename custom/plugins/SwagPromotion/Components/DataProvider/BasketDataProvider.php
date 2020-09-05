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

namespace SwagPromotion\Components\DataProvider;

use Enlight_Components_Db_Adapter_Pdo_Mysql as PdoConnection;
use Enlight_Components_Session_Namespace as Session;

/**
 * Returns basket context, e.g. amount, numberOfProducts
 */
class BasketDataProvider implements DataProvider
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var PdoConnection
     */
    private $db;

    public function __construct(Session $session, PdoConnection $db)
    {
        $this->session = $session;
        $this->db = $db;
    }

    /**
     * Return current basket's info
     *
     * {@inheritdoc}
     */
    public function get($context = null)
    {
        $sessionId = $this->session->get('sessionId');
        $amount = $this->getAmount($sessionId);
        $amount['numberOfProducts'] = $this->getProductCount($sessionId);
        $amount['shippingFree'] = $this->getShippingFree($sessionId);

        return $amount;
    }

    /**
     * Get total amount of basket
     *
     * @param string $sessionId
     *
     * @return array
     */
    private function getAmount($sessionId)
    {
        $sql = 'SELECT
                    basket.quantity,
                    basket.price,
                    basket.netprice AS netPrice
                FROM s_order_basket AS basket
                LEFT JOIN s_order_basket_attributes AS basket_attr 
                    ON basket.id = basket_attr.basketID
                WHERE basket.sessionID = :sessionId 
                  AND basket_attr.swag_is_free_good_by_promotion_id IS NULL 
                  AND modus = 0;';

        $basketPositions = $this->db->fetchAll($sql, ['sessionId' => $sessionId]);

        $gross = 0.0;
        $net = 0.0;
        foreach ($basketPositions as $position) {
            $quantity = (int) $position['quantity'];

            $gross += $quantity * (floor($position['price'] * 100 + 0.55) / 100);
            $net += $quantity * (floor($position['netPrice'] * 100 + 0.55) / 100);
        }

        return ['amountGross' => $gross, 'amountNet' => $net];
    }

    /**
     * Return number of products in basket
     *
     * @param string $sessionId
     *
     * @return string
     */
    private function getProductCount($sessionId)
    {
        $sql = 'SELECT SUM(quantity) FROM s_order_basket WHERE modus = 0 AND sessionID = :sessionId';

        return $this->db->fetchOne($sql, ['sessionId' => $sessionId]);
    }

    /**
     * Returns whether or not the basket is shipping free
     *
     * @param string $sessionId
     *
     * @return string
     */
    private function getShippingFree($sessionId)
    {
        $sql = 'SELECT MAX(shippingfree) FROM s_order_basket WHERE sessionID = :sessionId';

        return $this->db->fetchOne($sql, ['sessionId' => $sessionId]);
    }
}
