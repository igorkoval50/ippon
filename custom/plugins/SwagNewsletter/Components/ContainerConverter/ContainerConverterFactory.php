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

namespace SwagNewsletter\Components\ContainerConverter;

use SwagNewsletter\Components\ContainerConverter\Converter\BannerConverter;
use SwagNewsletter\Components\ContainerConverter\Converter\ConverterInterface;
use SwagNewsletter\Components\ContainerConverter\Converter\LinksConverter;
use SwagNewsletter\Components\ContainerConverter\Converter\ProductConverter;
use SwagNewsletter\Components\ContainerConverter\Converter\SuggestConverter;
use SwagNewsletter\Components\ContainerConverter\Converter\TextConverter;
use SwagNewsletter\Components\ContainerConverter\Converter\VoucherConverter;

class ContainerConverterFactory implements ContainerConverterFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRegistry()
    {
        return new ContainerConverterRegistry($this->initConverter());
    }

    /**
     * @return ConverterInterface[]
     */
    private function initConverter()
    {
        return [
            'ctArticles' => new ProductConverter(),
            'ctLinks' => new LinksConverter(),
            'ctBanner' => new BannerConverter(),
            'ctSuggest' => new SuggestConverter(),
            'ctText' => new TextConverter(),
            'ctVoucher' => new VoucherConverter(),
        ];
    }
}
