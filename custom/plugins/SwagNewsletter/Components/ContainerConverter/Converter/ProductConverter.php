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

class ProductConverter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ContainerConverterException
     */
    public function convert(array $data)
    {
        if (!isset($data['container'], $data['data'])) {
            throw new ContainerConverterException('Invalid data was passed.');
        }

        $articleData = $this->convertProductData($data['container']['articles']);
        $headline = $this->convertHeadline($data['container']['description']);

        return $this->mergeData($headline, $articleData);
    }

    /**
     * @param array $products
     *
     * @return array
     */
    private function convertProductData(array $products)
    {
        $productData = [];
        foreach ($products as $product) {
            $productData[] = [
                'name' => $product['name'],
                'ordernumber' => $product['number'],
                'position' => $product['position'],
                'type' => $product['type'],
            ];
        }

        return $productData;
    }

    /**
     * @param array $headline
     * @param array $productData
     *
     * @return array
     */
    private function mergeData(array $headline, array $productData)
    {
        return [
            $headline,
            [
                'key' => 'article_data',
                'type' => 'json',
                'value' => $productData,
            ],
        ];
    }

    /**
     * @param string $description
     *
     * @return array
     */
    private function convertHeadline($description)
    {
        return ['key' => 'headline', 'value' => $description];
    }
}
