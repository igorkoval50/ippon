<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components\Calculators;

use Shopware\Bundle\StoreFrontBundle\Struct\Customer\Group;
use Shopware\mediameetsFacebookPixel\Components\PluginConfig;
use Shopware\mediameetsFacebookPixel\Components\Traits\ConfigHelpers;

class PriceValueCalculator
{
    use ConfigHelpers;

    /**
     * @var Group
     */
    private $customerGroup;

    /**
     * @var array
     */
    private $pluginConfig;

    /**
     * @param Group $customerGroup
     */
    public function __construct(Group $customerGroup)
    {
        $this->customerGroup = $customerGroup;
        $this->pluginConfig = (new PluginConfig())->get();
    }

    /**
     * @param float $price
     * @param int $tax
     * @return float
     */
    public function getPrice($price, $tax)
    {
        $displayGrossPrices = $this->customerGroup->displayGrossPrices();

        $isGrossPriceMode = $this->isGrossPriceMode($this->pluginConfig['priceMode']);

        if (
            $isGrossPriceMode &&
            $displayGrossPrices === false
        ) {
            $price = $price + ($price / 100) * $tax;
        }

        if (
            ! $isGrossPriceMode &&
            $displayGrossPrices === true
        ) {
            $price = ($price / (100 + $tax)) * 100;
        }

        return floatval(round($price, 2));
    }
}
