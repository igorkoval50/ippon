<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components\Template\Data;

use Shopware\mediameetsFacebookPixel\Components\Traits\ConfigHelpers;

class Basket
{
    use ConfigHelpers;

    /**
     * @var array
     */
    private $sBasket;

    /**
     * @param array $sBasket
     */
    public function __construct(array $sBasket)
    {
        $this->sBasket = $sBasket;
    }

    /**
     * @param bool $includeShippingCosts
     * @param string $priceMode
     * @return float
     */
    public function getValue($includeShippingCosts, $priceMode)
    {
        $isGrossPriceMode = $this->isGrossPriceMode($priceMode);

        $value = $isGrossPriceMode
            ? $this->sBasket['AmountNumeric']
            : $this->sBasket['AmountNetNumeric'];

        if ($includeShippingCosts === false) {
            $value -= $isGrossPriceMode
                ? $this->sBasket['sShippingcostsWithTax']
                : $this->sBasket['sShippingcostsNet'];
        }

        return $value;
    }

    /**
     * @param string $productIdentifier
     * @return array
     */
    public function getContents($productIdentifier)
    {
        $identifier = $this->isOrderNumberMode($productIdentifier)
            ? 'ordernumber'
            : 'articleDetailId';

        $contents = [];
        foreach ($this->sBasket['content'] as $basketItem) {
            $contents[] = [
                'id' => $basketItem[$identifier],
                'quantity' => $basketItem['quantity'],
            ];
        }

        return $contents;
    }
}
