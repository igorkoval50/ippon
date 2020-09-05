<?php
/**
 * @copyright  Copyright (c) 2017, Net Inventors GmbH
 * @category   Shopware
 * @author     dpogodda
 */

namespace NetiFoundation\Service;


/**
 * @inheritdoc
 */
class Tax implements TaxInterface
{
    /**
     * @inheritdoc
     */
    public function netToGross($netPrice, $taxRate)
    {
        return $netPrice * (1 + $taxRate / 100);
    }

    /**
     * @inheritdoc
     */
    public function getVatAmount($price, $taxRate, $isNetPrice = false)
    {
        if ($isNetPrice) {
            return $price * $taxRate / 100;
        }

        return $price - $this->grossToNet($price, $taxRate);
    }

    /**
     * @inheritdoc
     */
    public function grossToNet($grossPrice, $taxRate)
    {
        return $grossPrice / (1 + $taxRate / 100);
    }
}