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

namespace SwagCustomProducts\Components\Services;

use Shopware\Bundle\StoreFrontBundle\Struct\Product;
use Shopware\Models\Article\Article;
use SwagLiveShopping\Components\LiveShoppingInterface;

class LiveShoppingHelper implements LiveShoppingHelperInterface
{
    /**
     * {@inheritdoc}
     */
    public function checkForLiveShoppingPrice(
        $number,
        LiveShoppingInterface $liveShopping = null,
        $returnGross = true
    ) {
        if (!$liveShopping) {
            return false;
        }

        /** @var array $liveShoppingData */
        $liveShoppingData = $liveShopping->getLiveShoppingByNumber($number);

        if ($liveShoppingData) {
            $grossPrice = $liveShoppingData[0]['currentPrice'];

            if ($returnGross) {
                return round($grossPrice, 2);
            }

            /** @var Article $product */
            $product = $liveShoppingData[1]->getArticle();
            $netPrice = $grossPrice / (1 + $product->getTax()->getTax() / 100);

            return round($netPrice, 2);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getLiveShoppingProductAttribute(Product $product)
    {
        if ($product->hasAttribute('live_shopping') && $product->getAttribute('live_shopping')->get('has_live_shopping')) {
            return $product->getAttribute('live_shopping')->get('live_shopping');
        }

        return null;
    }
}
