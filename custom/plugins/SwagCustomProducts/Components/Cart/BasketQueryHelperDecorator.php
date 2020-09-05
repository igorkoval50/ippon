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

use Shopware\Components\Cart\BasketQueryHelper;
use Shopware\Components\Cart\BasketQueryHelperInterface;
use Shopware\Components\Cart\Struct\DiscountContext;

class BasketQueryHelperDecorator implements BasketQueryHelperInterface
{
    /**
     * @var BasketQueryHelper
     */
    private $basketQueryHelper;

    public function __construct(BasketQueryHelperInterface $basketQueryHelper)
    {
        $this->basketQueryHelper = $basketQueryHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getPositionPricesQuery(DiscountContext $discountStruct)
    {
        $query = $this->basketQueryHelper->getPositionPricesQuery($discountStruct);

        $joinAlias = $this->getJoinAlias(
            $query->getQueryPart('join'),
            self::BASKET_ATTRIBUTE_TABLE_NAME
        );

        if ($joinAlias === '') {
            $joinAlias = 'swag_cp_basket_attributes';

            $query->join(
                self::BASKET_TABLE_ALIAS,
                self::BASKET_ATTRIBUTE_TABLE_NAME,
                $joinAlias,
                'basket.id = ' . $joinAlias . '.basketID'
            );
        }

        $query->orWhere(
            $query->expr()->andX(
                $query->expr()->eq('basket.modus', '4'),
                $query->expr()->isNotNull(
                    $joinAlias . '.swag_custom_products_configuration_hash'
                ),
                $query->expr()->eq('basket.sessionID', ':session')
            )
        );

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getInsertDiscountQuery(DiscountContext $discountContext)
    {
        return $this->basketQueryHelper->getInsertDiscountQuery(
            $discountContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getInsertDiscountAttributeQuery(DiscountContext $discountContext)
    {
        return $this->basketQueryHelper->getInsertDiscountAttributeQuery($discountContext);
    }

    /**
     * {@inheritdoc}
     */
    public function getLastInsertId()
    {
        return $this->basketQueryHelper->getLastInsertId();
    }

    /**
     * @param string $table
     *
     * @return string
     */
    private function getJoinAlias(array $joins, $table)
    {
        if (!array_key_exists('basket', $joins)) {
            return '';
        }

        foreach ($joins['basket'] as $join) {
            if ($join['joinTable'] === $table) {
                return $join['joinAlias'];
            }
        }

        return '';
    }
}
