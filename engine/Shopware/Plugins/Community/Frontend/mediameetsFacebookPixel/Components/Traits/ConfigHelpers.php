<?php

/**
 * @package    mediameetsFacebookPixel
 * @copyright  media:meets GmbH
 * @author     Marvin Schröder <schroeder@mediameets.de>
 */

namespace Shopware\mediameetsFacebookPixel\Components\Traits;

trait ConfigHelpers
{
    /**
     * @param string $priceMode
     * @return bool
     */
    private function isGrossPriceMode($priceMode)
    {
        return $priceMode === 'gross';
    }

    /**
     * @param string $productIdentifier
     * @return bool
     */
    private function isOrderNumberMode($productIdentifier)
    {
        return $productIdentifier !== 'id';
    }
}
