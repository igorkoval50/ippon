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

namespace SwagPromotion\Components\Promotion;

use Shopware_Components_Config as Config;

class Tax
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param float $discount
     *
     * @return array
     */
    public function calculate($discount, array $basket, array $matchingProducts = [])
    {
        $taxInt = $this->config->get('discounttax');
        $auto = $this->config->get('taxautomode');

        if ($auto) {
            $taxInt = $this->getMaxBasketTaxRate($basket, $matchingProducts);
        }
        $taxRate = (100 + $taxInt) / 100;

        // if amount and net amount are equal we don't have to calculate a net price for the discount
        $net = $basket['AmountNumeric'] === $basket['AmountNetNumeric'] ? $discount : $discount / $taxRate;

        return ['taxRate' => $taxInt, 'net' => $net];
    }

    /**
     * Returns the highest tax rate of all basket items which activate the promotion.
     */
    private function getMaxBasketTaxRate(array $basket, array $matchingProducts)
    {
        $promotionProducts = array_column($matchingProducts, 'ordernumber');

        return max(
            array_map(
                function ($item) {
                    return $item['tax_rate'];
                },
                array_filter(
                    $basket['content'],
                    function ($item) use ($promotionProducts) {
                        if ($promotionProducts) {
                            return (int) $item['mode'] === 0 && in_array($item['ordernumber'], $promotionProducts, true);
                        }

                        return (int) $item['mode'] === 0;
                    }
                )
            )
        );
    }
}
