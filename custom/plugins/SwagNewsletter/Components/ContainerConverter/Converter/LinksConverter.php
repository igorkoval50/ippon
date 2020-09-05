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

class LinksConverter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ContainerConverterException
     */
    public function convert(array $data)
    {
        if (!isset($data['container'], $data['data'])) {
            throw new ContainerConverterException('Invalid data was passed');
        }

        $linkData = $this->convertLinkData($data);
        $headline = $this->convertHeadline($data['container']['description']);

        return $this->mergeData($headline, $linkData);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function convertLinkData(array $data)
    {
        $linkData = [];
        foreach ($data['container']['links'] as $link) {
            $linkData[] = [
                'description' => $link['description'],
                'link' => $link['link'],
                'target' => $link['target'],
                'position' => $link['position'],
            ];
        }

        return $linkData;
    }

    /**
     * @param array $headline
     * @param array $linkData
     *
     * @return array
     */
    private function mergeData(array $headline, array $linkData)
    {
        return [
            $headline,
            ['key' => 'link_data', 'type' => 'json', 'value' => $linkData],
        ];
    }

    /**
     * @param string $description
     *
     * @return array
     */
    private function convertHeadline($description)
    {
        return ['key' => 'description', 'value' => $description];
    }
}
