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

namespace SwagNewsletter\Components\ContainerConverter\Converter;

use SwagNewsletter\Components\ContainerConverter\ContainerConverterException;

class BannerConverter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ContainerConverterException
     */
    public function convert(array $data)
    {
        $data = $data['container'];

        if (!isset($data['description'], $data['banner'])) {
            throw new ContainerConverterException('Invalid data was passed.');
        }

        return [
            $this->convertDescription($data),
            $this->convertFile($data),
            $this->convertLink($data),
            $this->convertTargetSelection($data),
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function convertDescription(array $data)
    {
        return [
            'key' => 'description',
            'value' => $data['description'],
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function convertFile(array $data)
    {
        return [
            'key' => 'file',
            'value' => $data['banner']['image'],
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function convertLink(array $data)
    {
        return [
            'key' => 'link',
            'value' => $data['banner']['link'],
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function convertTargetSelection(array $data)
    {
        return [
            'key' => 'target_selection',
            'value' => $data['banner']['target'],
        ];
    }
}
