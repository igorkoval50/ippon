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
use SwagNewsletter\Components\ContainerConverter\Strategy\ConverterStrategyInterface;
use SwagNewsletter\Components\ContainerConverter\Strategy\TextConvertStrategy;

class VoucherConverter implements ConverterInterface
{
    /**
     * @var ConverterStrategyInterface
     */
    private $strategy;

    public function __construct()
    {
        $this->strategy = new TextConvertStrategy();
    }

    /**
     * {@inheritdoc}
     *
     * @throws ContainerConverterException
     */
    public function convert(array $data)
    {
        $data = $data['container'];

        if (!isset($data['text'], $data['value'])) {
            throw new ContainerConverterException('Invalid data was passed.');
        }

        $result = $this->strategy->convert($data);
        $result[] = $this->convertVoucherSelection($data);

        return $result;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function convertVoucherSelection(array $data)
    {
        return [
            'key' => 'voucher_selection',
            'value' => (int) $data['value'],
        ];
    }
}
