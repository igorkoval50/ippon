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

namespace SwagCustomProducts\Components\DataConverter\Converter;

use SwagCustomProducts\Components\DataConverter\ConverterInterface;
use SwagCustomProducts\Components\Types\Types\WysiwygType;

class WysiwygConverter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convertRequestData($data)
    {
        return [html_entity_decode($data)];
    }

    /**
     * Removes html tags from the given data array.
     *
     * {@inheritdoc}
     */
    public function convertBasketData(array $optionData, array $data)
    {
        $data[0] = strip_tags($data[0]);
        $optionData['selectedValue'] = $data;

        return $optionData;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($key)
    {
        return $key === WysiwygType::TYPE;
    }
}
